<?php
class gastosModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    //--------------------------------------------------------------------------
    // METODO QUE CARGA DATOS DE LA TABLA GASTOS POR ID O TODOS LOS DATOS
    //--------------------------------------------------------------------------
    public function cargarGasto($item = FALSE)
    {
        if($item)
         $sql = "select * from gasto as cpra, det_compra as dcpra, proveedor as prv where  cpra.id_compra= dcpra.compra_id
				and  cpra.proveedor_id = prv.id_proveedor and prv.nombre_proveedor like '%$item%' order by fecha_compra,proveedor_id";
        else
            $sql = "select gto.*,sum(dgto.mto_tgasto)as total,prv.razon_social_proveedor from gasto as gto, det_gasto as dgto, proveedor as prv where  gto.id_gasto= dgto.gasto_id
				and  gto.proveedor_id = prv.id_proveedor group by gto.id_gasto order by gto.fecha_creacion,gto.proveedor_id";
        
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
    
	//--------------------------------------------------------------------------
    // METODO QUE CARGA DATOS DE LA TABLA DET_GASTOS POR ID O TODOS LOS DATOS
    //--------------------------------------------------------------------------
	public function cargarDetGasto($valor)
	{
		if($valor > 0)
		{
			$sql="select dgto.*,tgto.nombre_tipo_gasto,tgto.id_tipo_gasto from det_gasto as dgto, tipo_gasto as tgto where dgto.gasto_id = '$valor' and
			 tgto.id_tipo_gasto = dgto.det_tgasto_id order by dgto.id_det_gasto";
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
	//---------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA GASTO POR SI ID 
	//---------------------------------------------------------------------------------------------------
	public function buscarGasto($id)
	{
		$sql = "select gto.*,dgto.*,td.nombre_tipo_documento,prv.razon_social_proveedor,tgto.nombre_tipo_gasto  
		        from gasto as gto,tipo_documento as td,proveedor as prv,det_gasto as dgto,tipo_gasto as tgto
		        where dgto.gasto_id = gto.id_gasto and gto.id_gasto ='$id' and prv.id_proveedor = gto.proveedor_id
		         and td.id_tipo_documento = gto.tipo_doc_ori and dgto.det_tgasto_id = tgto.id_tipo_gasto  ";
		         
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
	
	
	public function insertar($datos)
	{
		
		
		$this->iniciar();
		
		$sql="select * from tipo_movimiento where nombre_tipo_movimiento = 'COMPRA'";
		$res = $this->_db->query($sql);
		if($res)
		{
			$res->setFetchMode(PDO::FETCH_ASSOC);
			$tipo_mov = $res->fetch();
		}
		
		$sql = "insert into gasto("
			. "fecha_creacion,"
			. "fecha_emision_gasto,"
			. "fecha_vencimiento_gasto,"
			. "tipo_doc_ori,"
			. "nro_doc_ori,"
			. "proveedor_id,"			
			. "estatus_gasto,"
			. "tipo_mov_af,"
			. "nro_mov_af,"
			. "condicion_gasto,"
			. "comentario_gasto)"
			. "values("
			. "now(),"
			. "'".$datos['emision']."',"
			. "'".$datos['vencimiento']."',"
			. "'".$datos['tdoc']."',"
			. "'".$datos['ndoc']."',"
			. "'".$datos['proveedor']."',"			
			. "'1',"
			. "'".$tipo_mov['id_tipo_movimiento']."',"
			. "'".$datos['compra']."',"
			. "'ACTIVO',"
			. "'".$datos['comentario']."')";
			
		$res = $this->_db->exec($sql);
        if(!$res)
        {
			$this->cancelar();
            return false;               
        }
        else
        {
            $ult_gasto = $this->ultimo();
			
			$producto = $datos['codigo'];
			$cantidad = $datos['cantidad'];
			$precio   = $datos['precio'];
			$tsa_iva  = $datos['tsa_iva'];
			$total 	  = $datos['total'];
			
			$monto = 0;
			$mto_iva =0;
			$total =0;
			$total_doc = 0;
			
			for($i = 0; $i < count($producto);$i++ )
			{
				$monto   = $precio[$i] * $cantidad[$i];
				//$total   = $total[$i];
				$tiva    = $tsa_iva[$i];
				$mto_iva = ($monto * (($tiva/100)+1)) - $monto ;
				$total   = $monto + $mto_iva;
				
				$total_doc += $total;
				
				$sql ="insert into det_gasto("
					. "gasto_id,"
					. "det_tgasto_id,"
					. "precio_tgasto,"
					. "cantidad_tgasto,"
					. "mto_tgasto,"
					. "tsa_iva_tgasto,"
					. "mto_iva_tgasto,"
					. "mto_total_tgasto,"
					. "estatus_tgasto)"
					. "values("
					. "'".$ult_gasto."',"
					. "'".$producto[$i]."',"
					. "'".$precio[$i]."',"
					. "'".$cantidad[$i]."',"
					. "'".$monto."',"
					. "'".$tsa_iva[$i]."',"
					. "'".$mto_iva."',"
					. "'".$total."','1')";
					
				$res = $this->_db->exec($sql);
				if(!$res)
				{
					$this->cancelar();
					return false;               
				}
					
				
			}
			
				$sql="select * from tipo_movimiento where nombre_tipo_movimiento = 'GASTO'";
				$res = $this->_db->query($sql);
				if($res)
				{
					$res->setFetchMode(PDO::FETCH_ASSOC);
					$tipo_mov = $res->fetch();
				}
						
				$comentario_cxp ="CUENTA POR PAGAR GENERADA POR GASTO N-".$ult_gasto;
				$sql = "insert into cxp("
					. "fecha_creacion,"
					. "proveedor_id,"
					. "tipo_doc_ori,"
					. "nro_doc_ori,"
					. "tipo_mov_ori,"
					. "nro_mov_ori,"
					. "debito_cxp,"
					//. "credito_cxp,"
					. "saldo_cxp,"
					. "estatus_cxp,"
					. "condicion_cxp,"
					. "comentario_cxp)"
					. "values("
					. "now(),"
					. "'".$datos['proveedor']."',"
					. "'".$datos['tdoc']."',"
					. "'".$datos['ndoc']."',"
					. "'".$tipo_mov['id_tipo_movimiento']."',"
					. "'".$ult_gasto."',"
					. "'".$total_doc."',"
					. "'".$total_doc."',"
					. "'1',"
					. "'PENDIENTE',"
					. "'".$comentario_cxp."')";
					
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
	
	//-----------------------------------------------------------------------------------------
	//METODO QUE CARGA LOS GASTOS GENERADOS POR UN DOCUMENTO
	//-----------------------------------------------------------------------------------------
	public function cargarGastoDoc($tipo,$id)
	{
		
		$sql = "select gto.*,prv.razon_social_proveedor,sum(dgto.mto_total_tgasto)as total from gasto as gto,det_gasto as dgto,proveedor as prv 
		where gto.id_gasto = dgto.gasto_id and gto.proveedor_id = prv.id_proveedor and
		gto.nro_mov_af ='$id' and gto.tipo_mov_af='$tipo' and estatus_gasto='1' group by gto.id_gasto 
		 order by id_gasto,fecha_emision_gasto";
		
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
	
	//--------------------------------------------------------------------------------------------------
	//METODO QUE GRABA LA DISTRIBUCION DE DASTOS DE UNA COMPRA EN UN PRODUCTO
	//--------------------------------------------------------------------------------------------------
    public function insertarDistribucion($datos)
	{
		if(count($datos) > 0)
		{
			$this->iniciar();
			foreach($datos as $val)
			{
					
				$producto = $val['producto'];
				$porcentaje = $val['porcentaje'];
				$distribucion = $val['distribucion'];
				$cantidad     = $val['cantidad'];
				
				for($i = 0;$i < count($producto);$i++) {
					
					$sql="insert into distribucion_gasto("			
					."fecha_distribucion,"
					."tipo_doc_ori,"
					."nro_doc_ori,"
					."gasto_id,"
					."producto_id,"
					."cantidad_producto,"
					."alicuota_distribucion,"
					."monto_distribucion,"
					."compra_id)"
					."values("
					."now(),"
					."'".$val['tipo_documento']."',"
					."'".$val['nro_documento']."',"
					."'".$val['gasto']."',"
					."'".$producto[$i]."',"
					."'".$cantidad[$i]."',"
					."'".$porcentaje[$i]."',"
					."'".$distribucion[$i]."',"
					."'".$val['compra']."'"
					.")";			
					
					//die($sql);	
					$res = $this->_db->exec($sql);
					if(!$res)
					{
						$this->cancelar();
						return false;               
					}else
						{
							$sql ="update gasto set estatus_gasto = '2' where id_gasto = '".$val['gasto']."'";
							$res = $this->_db->exec($sql);
						}					
				}
						
				
			}
			$this->confirmar();
			return TRUE;
		}
	}

	//-----------------------------------------------------------------------------------------------
	//METODO QUE CREA ARREGLO CON DATOS DE COSTO Y FORMATO NECESARIO PARA ACTUALIZAR COSTO DE PRODUCTO
	//-----------------------------------------------------------------------------------------------	
	public function calcularCostoGasto($datos)
	{
	$costo = array();
	
	if(count($datos))
	{
		foreach($datos as $val)
		{
			$producto = $val['producto'];
			$monto    = $val['distribucion'];
			$cantidad = $val['cantidad'];
			
			for($i=0; $i < count($producto);$i++)
			{
				$prd = $producto[$i];
				$cnt = $cantidad[$i];
				$mto = $monto[$i];
				
				
				if(in_array($prd, $costo))
				{
					$key = array_search($prd, array_column($costo, 'producto'));
					$costo[$key]['monto']= $costo[$key]['monto']+$mto/$cnt;						
					
				}else
					{
						$costo[]= array("producto"=>$prd,"monto"=>$mto/$cnt,"compra"=>$val['compra']);
					}
			}
			
		}
		
		//print_r($costo);
		//exit();
		return $costo;
		
	}else
		{
			return array();
	
		}		
	}
		
	
	//---------------------------------------------------------------------------
	//METODO PARA ANULAR GASTO Y REVERZAR OPERACION
	//---------------------------------------------------------------------------        
    public function anular($id)
	{
		
		
		
	}
}
