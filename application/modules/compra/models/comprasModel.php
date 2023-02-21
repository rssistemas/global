<?php
class comprasModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    //--------------------------------------------------------------------------
    // METODO QUE CARGA DATOS DE LA TABLA PROVEEDOR POR ID O TODOS LOS DATOS
    //--------------------------------------------------------------------------
    public function cargarCompras($item = FALSE)
    {
        if($item)
         $sql = "select *,sum(dcpra.mto_total_producto)as total from compra as cpra, det_compra as dcpra, proveedor as prv where  cpra.id_compra= dcpra.compra_id
				and  cpra.proveedor_id = prv.id_proveedor and prv.nombre_proveedor like '%$item%' order by fecha_compra,proveedor_id";
        else
            $sql = "select *,sum(dcpra.mto_total_producto)as total from compra as cpra, det_compra as dcpra, proveedor as prv where  cpra.id_compra= dcpra.compra_id
				and  cpra.proveedor_id = prv.id_proveedor group by id_compra order by cpra.fecha_creacion,cpra.proveedor_id";
        
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
	
public function cargarCompraPrv($prv)
    {
        
         $sql = "select * from compra as cpra, det_compra as dcpra, proveedor as prv where  cpra.id_compra= dcpra.compra_id
				and  cpra.proveedor_id = '$prv'  order by fecha_compra";
        
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

//-------------------------------------------------------------------------------------------
//METODO DE CONTEO DE DOCUMENTO  DE COMPRA A PROVEEDOR,comprueba si un documento ya fue registrado 
//-------------------------------------------------------------------------------------------
public function checkDocProveedor($prv,$doc,$nro)
{
    $empresa = session::get('actEmp');
    
    $sql ="select count(*)as total  from compra as cp where cp.proveedor_id = '$prv' 
    and tipo_doc_rec = '$doc' and empresa_id = '$nro' and empresa_id = '".$empresa[0]['id_empresa']."' ";

    //die($sql);
    $res = $this->_db->query($sql);
    if($res)
    {
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $data = $res->fetchAll();
        if(count($data)>0)
            return $data;
        else
            return array("total"=>0);
    }
    else
    {
        return array("total"=>0);
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
	
//-------------------------------------------------------------------------------------------------
//METODO QUE INSERTA NUEVO REGISTRO 
//-------------------------------------------------------------------------------------------------
public function insertar($datos)
{       
    
   // $this->_db->start();

    $sql = "insert into compra("
            . "fecha_creacion,"
            . "empresa_id,"
            . "proveedor_id,"
            . "deposito_id,"
            . "unidad_operativa,"
            . "estatus_compra,"
            . "condicion_compra,"
            . "comentario_compra,"
            . "emision_documento,"
            . "vencimiento_documento,"
            . "recepcion_id,"
            . "orden_compra,"
            . "tipo_compra,"
            . "tipo_doc_rec,"
            . "nro_doc_rec,"
            . "control_doc_rec)"
            . "values("
            . "now(),"
            . "'".$datos['empresa']."',"
            . "'".$datos['proveedor']."',"
            . "'".$datos['deposito']."',"
            . "'".$datos['unidad']."',"
            . "'1',"
            . "'REGISTRADA',"
            . "'".$datos['comentario']."',"
            . "'".$datos['emision']."',"
            . "'".$datos['vencimiento']."',"
            . "'".$datos['recepcion']."',"
            . "'".$datos['orden_compra']."',"
            . "'".$datos['tipo']."',"
            . "'".$datos['tdoc']."',"
            . "'".$datos['ndoc']."',"
            . "'".$datos['cdoc']."')";

    //die($sql);
    $res = $this->_db->exec($sql);
    if(!$res)
    {
        $error =$this->_db->getError();
        logger::errorLog($error['2'].' Table:COMPRAS','DB');
     //   $this->_db->cancel();
        return false;               
    }
    else
    {
        $ult_compra = $this->_db->getInsertedId();

                $producto = $datos['producto'];
                $cantidad = $datos['cantidad'];
                $precio   = $datos['precio'];
                $tsa_iva  = $datos['tsa_iva'];
                $monto = 0;
                $mto_iva =0;
                $total =0;
                $total_doc = 0;
                for($i = 0; $i < count($producto);$i++ )
                {
                        $monto = $precio[$i] * $cantidad[$i];
                        $total = $monto*(($tsa_iva/100)+1);
                        $mto_iva = $total - $monto;

                        $total_doc +=$total;

                        $sql ="insert into det_compra("
                                . "compra_id,"
                                . "det_producto_id,"
                                . "costo_producto,"
                                . "cantidad_producto,"
                                . "monto_producto,"
                                . "tsa_iva_producto,"
                                . "mto_iva_producto,"
                                . "mto_total_producto)"
                                . "values("
                                . "'".$ult_compra."',"
                                . "'".$producto[$i]."',"
                                . "'".$precio[$i]."',"
                                . "'".$cantidad[$i]."',"
                                . "'".$monto."',"
                                . "'".$tsa_iva."',"
                                . "'".$mto_iva."',"
                                . "'".$total."')";
                        $res = $this->_db->exec($sql);
                        if(!$res)
                        {
                                $error =$this->_db->getError();
                                logger::errorLog($error['2'].'Table:DetCompra','DB');
                     //           $this->cancel();
                                return false;               
                        }else
                        {
                                if($datos['recepcion'] > 0)
                                {
                                        $sql = "update stock set estatus_stock = '1',compra_id='$ult_compra',costo_stock = '$total'
                                        where producto_id = '".$producto[$i]."' and recepcion_id = '".$datos['recepcion']."'";
                                }else
                                    {
                                        $sql = "insert into stock("
                                            . "producto_id,cantidad,deposito_id,"
                                            . "fecha_ult_act,compra_id,costo_stock,"
                                            . "estatus_stock,empresa_id,unidad_id)
                                                values('".$producto[$i]."','".$cantidad[$i]."','".$datos['deposito']."',"
                                                . "now(),'".$ult_compra."','".$precio[$i]."','1','".$datos['empresa']."','".$datos['unidad']."')";

                                    }

                                $res = $this->_db->exec($sql);
                                if(!$res)
                                {
                        //            $this->cancel();
                                    return false;               
                                }	
                        }


                }
                if($datos['tipo']=='CREDITO')
                {
                        $sql="select * from tipo_documento where nombre_tipo_documento = 'COMPRA'";
                        $res = $this->_db->query($sql);
                        if($res)
                        {
                                $res->setFetchMode(PDO::FETCH_ASSOC);
                                $tipo_doc = $res->fetch();

                                $comentario_cxp ="CUENTA POR PAGAR GENERADA POR COMPRA N-".$ult_compra;
                                $sql = "insert into cxp("
                                        . "fecha_creacion,"
                                        . "proveedor_id,"
                                        . "tipo_doc_ori,"
                                        . "nro_doc_ori,"
                                        . "debito_cxp,"
                                        //. "credito_cxp,"
                                        . "saldo_cxp,"
                                        . "estatus_cxp,"
                                        . "condicion_cxp,"
                                        . "comentario_cxp)"
                                        . "values("
                                        . "now(),"
                                        . "'".$datos['proveedor']."',"
                                        . "'".$tipo_doc['id_tipo_documento']."',"
                                        . "'".$ult_compra."',"
                                        . "'".$total_doc."',"
                                        . "'".$total_doc."',"
                                        . "'1',"
                                        . "'POR CANCELAR',"
                                        . "'".$comentario_cxp."')";

                                $res = $this->_db->exec($sql);
                                if(!$res)
                                {
                          //              $this->_db->cancel();
                                        return false;               
                                }

                        }	
                }

}

      //  $this->_db->confirm();
        return true;

}
	
//-------------------------------------------------------------------------------------------
//METODO QUE CARGA LOS DETALLES DE UNA COMPRA
//------------------------------------------------------------------------------
public function cargarDetCompra($id)
{

    $sql = "select dcpra.*,prd.nombre_producto from det_compra as dcpra,det_producto as dprd,producto as prd
     where  dcpra.compra_id = '$id' and dprd.id_det_producto = dcpra.det_producto_id and
      prd.id_producto = dprd.producto_id ";

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
	
//------------------------------------------------------------------------------
//METODO QUE BUSCA LAS COMPRAS POR SU ID 
//------------------------------------------------------------------------------        
public function buscarCompra($id,$proveedor = false)
{
    if($proveedor)
    {
        $sql = "select cpra.*,dcpra.*,prd.nombre_producto,prov.razon_social_proveedor,rif_proveedor "
             . "from compra as cpra,det_compra as dcpra,det_producto as dprd,producto as prd,proveedor as prov "
             . "where dcpra.compra_id = cpra.id_compra and cpra.id_compra = '$id' and cpra.proveedor_sid = '$proveedor' "
             . "and dprd.id_det_producto = dcpra.det_producto_id "
             . "and prd.id_producto = dprd.producto_id and cpra.proveedor_id = prov.id_proveedor ";
        
    }else{
            $sql = "select cpra.*,dcpra.*,prd.nombre_producto,prov.razon_social_proveedor,rif_proveedor "
             . "from compra as cpra,det_compra as dcpra,det_producto as dprd,producto as prd,proveedor as prov "
             . "where dcpra.compra_id = cpra.id_compra and cpra.id_compra = '$id' and dprd.id_det_producto = dcpra.det_producto_id "
             . "and prd.id_producto = dprd.producto_id and cpra.proveedor_id = prov.id_proveedor ";
    }



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
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
//METODO QUE BUSCA LAS COMPRAS POR SU ID Y PROVEEDOR
//------------------------------------------------------------------------------        
public function buscarCompraProveedor($id,$proveedor)
{
    if($proveedor)
    {
        $sql = "select cpra.*,dcpra.*,prd.nombre_producto,prov.razon_social_proveedor,rif_proveedor "
             . "from compra as cpra,det_compra as dcpra,det_producto as dprd,producto as prd,proveedor as prov "
             . "where dcpra.compra_id = cpra.id_compra and cpra.nro_compra = '$id' and cpra.proveedor_id = '$proveedor' "
             . "and dprd.id_det_producto = dcpra.det_producto_id "
             . "and prd.id_producto = dprd.producto_id and cpra.proveedor_id = prov.id_proveedor ";
        
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

//---------------------------------------------------------------------------------



	public function eliminar($id)
	{
		$this->iniciar();
		
		$sql = "update compra set estatus_compra = '9',condicion_compra='ELIMINADA'  where id_compra='$id'";
		$res = $this->_db->exec($sql);
		if(!$res)
		{
			$this->_db->cancel();
			return false;               
		}else
		{			
			$sql = "select det_producto_id,cantidad_producto from det_compra where compra_id ='$id'";
			$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				$datos = $res->fetchAll();
				
				if(count($datos)>0)
				{
					foreach($datos as $valor)
					{
						$sql = "update stock set cantidad = cantidad - ".$valor['cantidad_producto'].", estatus_stock = '9' 
						where producto_id = ".$valor['det_producto_id']." and  compra_id = ".$id."";
						$res = $this->_db->exec($sql);
						if(!$res)
						{
							$this->_db->cancel();
							return false;               
						}
						
						//si tiene recepcion hay que 	
					
					}
					$this->confirmar();
					return true;					
				}			
				
			}
			else
			{
				$this->_db->cancel();
				return false;
			}
					
		}
			
	}

              
}
