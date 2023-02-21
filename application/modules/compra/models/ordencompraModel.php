<?php
class ordencompraModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    //--------------------------------------------------------------------------
    // METODO QUE CARGA DATOS DE LA TABLA PROVEEDOR POR ID O TODOS LOS DATOS
    //--------------------------------------------------------------------------
    public function cargarOrdenCompra($item = FALSE)
    {
        if($item)
			$sql = "select oc.*,sum(doc.total)as total,prv.razon_social_proveedor from orden_compra as oc, det_orden_compra as doc, proveedor as prv
				where  oc.id_orden_compra = doc.orden_compra_id and oc.proveedor_id = prv.id_proveedor  group by id_orden_compra order by fecha_creacion";
        else
            $sql = "select oc.*,sum(doc.total)as total,prv.razon_social_proveedor from orden_compra as oc, det_orden_compra as doc, proveedor as prv
				where  oc.id_orden_compra = doc.orden_compra_id and oc.proveedor_id = prv.id_proveedor  group by oc.id_orden_compra ";
        //die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
        {
            return array();
        }
    }
	
	
	
	
	public function incluir($datos)
	{
		$this->iniciar();
		
		$sql = "insert into orden_compra("		
			. "fecha_creacion,"
			. "proveedor_id,"
			. "forma_compra,"
			. "estatus_orden_compra,"
			. "condicion_orden_compra,"
			. "comentario_orden_compra,"
			. "unidad_operativa_id,"
			. "presupuesto_nro"
			. ")values("
			. "now(),"
			. "'".$datos['proveedor']."',"
			. "'".$datos['tipo']."',"
			. "'1',"
			. "'ACTIVO',"
			. "'COMENTARIO DE PRUEBA','".$datos['unidad']."','".$datos['ndoc']."')";
		    $res = $this->_db->exec($sql);
			if(!$res)
			{
				
				$this->cancelar();
				return FALSE;
			}else
				{
					$ult_oc = $this->ultimo();
					if(count($datos['producto'])>0)
					{
						$producto = $datos['producto'];
						$cantidad = $datos['cantidad'];
						$precio = $datos['precio'];
						$tasa_iva = $datos['tsa_iva'];
						$total = $datos['total'];
						
						
						for($i=0;$i < count($producto);$i++)
						{
							$monto = $precio[$i]*$cantidad[$i];
							$mto_total = $monto * (($tasa_iva[$i]/100) + 1);
							$mto_iva =  $mto_total - $monto;  
							
							$sql = "insert into det_orden_compra("
									. "det_producto_id,"
									. "orden_compra_id,"
									. "cantidad,"
									. "precio,"
									. "monto,"
									. "tasa_impuesto,"
									. "impuesto,"
									. "total)"
									. "values("
									. "'".$producto[$i]."',"
									. "'".$ult_oc."',"
									. "'".$cantidad[$i]."',"
									. "'".$precio[$i]."',"
									. "'".$monto."',"
									. "'".$tasa_iva[$i]."',"
									. "'".$mto_iva."',"
									. "'".$mto_total."'"
									.")";
							$res = $this->_db->exec($sql);
							if(!$res)
							{
								$this->cancelar();
								return false;
							}		
							
						}
						
						$this->confirmar();
						return true;
														
					}	
					
				
				}
			
	}	
	

	//-------------------------------------------------------------------------------------------
	//metodo que busca ordenes de compra por su numero  o grupo de numeros
	//-------------------------------------------------------------------------------------------
	public function buscarOrden($id)
	{
		if(is_int($id))
		{
			$sql="select oc.*,prv.razon_social_proveedor,prv.rif_proveedor,dpro.codigo_producto,pro.nombre_producto,mar.nombre_marca, 
		doc.* from orden_compra as oc, det_orden_compra as doc, proveedor as prv,det_producto dpro,producto as pro,marca as mar,
		where  oc.id_orden_compra = doc.orden_compra_id and oc.id_orden_compra = '$id' and doc.det_producto_id = dpro.id_det_producto 
		and dpro.producto_id = pro.id_producto and oc.proveedor_id = prv.id_proveedor and mar.id_marca = dpro.marca_id group by doc.id_det_orden_compra order by doc.id_det_orden_compra"; 
		}else
		{
			$sql="select oc.*,prv.razon_social_proveedor,prv.rif_proveedor,dpro.codigo_producto,pro.nombre_producto,mar.nombre_marca, 
		doc.* from orden_compra as oc, det_orden_compra as doc, proveedor as prv,det_producto dpro,producto as pro,marca as mar
		where  oc.id_orden_compra = doc.orden_compra_id and oc.id_orden_compra in (".$id.") and doc.det_producto_id = dpro.id_det_producto 
		and dpro.producto_id = pro.id_producto and oc.proveedor_id = prv.id_proveedor and mar.id_marca = dpro.marca_id group by doc.id_det_orden_compra order by doc.id_det_orden_compra"; 
		}
		
		//die($sql);
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
        {
            return array();
        }
		
		
	}

    
    public function buscarAutoCompra($valor)
    {        
        $val = array();
		$sql = "select * from proveedor where razon_social like '%$valor%'";
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);            
            $datos = $res->fetchAll();
            foreach ($datos as $valor)
            {
                $val[] = array(
                    "label"=>  ucfirst($valor['razon_social']),
                    "value"=>array("nombre"=>  ucfirst($valor['razon_social']),
                                    "id"=>$valor['id_proveedor']));
            }
            
            return $val;
            
        }else
            return array();
    }
            
    public function buscarOrdenProveedor($proveedor)
	{
		$sql = "select oc.id_orden_compra,oc.fecha_creacion  from orden_compra as oc where
				oc.proveedor_id = '$proveedor' and oc.condicion_orden_compra='ACTIVO'";
				
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
        {
            return array();
        }
		
	}
	
	
	//------------------------------------------------------------------------------------
	//METODO QUE BUSCA ORDENES DE COMPRA DE LAS RECEPCIONES sin productos faltante
	//------------------------------------------------------------------------------------
	public function buscarOcRecepcion($rec)
	{
		$sql = "select orden_compra_id as orden from det_recepcion where recepcion_id = '$rec' and orden_compra_id not in 
		(select orden_compra_id  from det_recepcion where recepcion_id = '$rec' and recibido_producto < cantidad_producto)  ";
				
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
        {
            return array();
        }
		
	}
	
	public function cerrarOrdenRecepcion($id)
	{
		
		$orden = $this->buscarOcRecepcion($id);
		
		if(count($orden))
		{
			$this->iniciar();
			foreach($orden as $val)	
			{
				$sql = "update orden_compra set condicion_orden_compra ='CERRADA' where id_orden_compra=".$val['orden'];
				
				$res = $this->_db->exec($sql);
				if(!$res)
				{
					$this->cancelar();
					return false;
				}
				
			}
			$this->confirmar();
			return true;
		}	
		
	}	
	
	
	
}
