<?php
class requisicionModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    //==========================================================================
    //CARGA LAS RECEPCIONES DEL PROCESO INTERNO ENTRE DEPOSITOS
    //==========================================================================
    public function cargarRequisicion($unidad = false,$almacen = FALSE)
    {
        $trabajador = session::get('trabajador');
        if($unidad)
        {
            if($almacen)
                $sql = "select req.*,dreq.*,marca.nombre_marca,um.nombre_uni_med,prd.nombre_producto,uo.nombre_unidad_operativa,dep.nombre_deposito
			from requisicion as req,det_requisicion as dreq,producto as prd,unidad_operativa as uo,deposito as dep,
			det_producto as dprd,marca,uni_med as um where dreq.requisicion_id = req.id_requisicion and
			dreq.id_requisito = dprd.id_det_producto and prd.id_producto = dprd.producto_id and marca.id_marca = dprd.marca_id and
			um.id_uni_med = dprd.unidad_almacenamiento and uo.id_unidad_operativa = req.unidad_operativa_id and dep.id_deposito = req.deposito_id and req.deposito_id = '$almacen' and req.unidad_id = '$unidad' ";
            else
		$sql = "select req.*,dreq.*,marca.nombre_marca,um.nombre_uni_med,prd.nombre_producto,uo.nombre_unidad_operativa,dep.nombre_deposito
			from requisicion as req,det_requisicion as dreq,producto as prd,unidad_operativa as uo,deposito as dep,
			det_producto as dprd,marca,uni_med as um where dreq.requisicion_id = req.id_requisicion and
			dreq.id_requisito = dprd.id_det_producto and prd.id_producto = dprd.producto_id and marca.id_marca = dprd.marca_id and
			um.id_uni_med = dprd.unidad_almacenamiento and uo.id_unidad_operativa = req.unidad_operativa_id and dep.id_deposito = req.deposito_id and req.deposito_id = '$almacen' ";

        }else
        {
             $sql = "select req.*,dreq.*,marca.nombre_marca,um.nombre_uni_med,prd.nombre_producto,uo.nombre_unidad_operativa,dep.nombre_deposito
		from requisicion as req,det_requisicion as dreq,producto as prd,unidad_operativa as uo,deposito as dep,
		det_producto as dprd,marca,uni_med as um where dreq.requisicion_id = req.id_requisicion and
		dreq.id_requisito = dprd.id_det_producto and prd.id_producto = dprd.producto_id and marca.id_marca = dprd.marca_id and
		um.id_uni_med = dprd.unidad_almacenamiento and uo.id_unidad_operativa = req.unidad_operativa_id and dep.id_deposito = req.deposito_id ";
        }

        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();

    }

    //==========================================================================
    //LISTA LAS REQUISICIONES
    //==========================================================================
    public function listarRequisicion($unidad = false,$almacen = FALSE,$emp)
    {
       
        if($unidad)
        {
            if($almacen)
                $sql = "select req.*,dreq.*,uo.nombre_unidad_operativa,dep.nombre_deposito
                from requisicion as req,det_requisicion as dreq,unidad_operativa as uo,deposito as dep,
                uni_med as um where dreq.requisicion_id = req.id_requisicion and
                um.id_uni_med = dprd.unidad_almacenamiento and uo.id_unidad_operativa = req.unidad_operativa_id
                and dep.id_deposito = req.deposito_id and req.deposito_id = '$almacen' and req.unidad_id = '$unidad'
                 and estatus_requisicion !='9' and req.empresa_id='$emp' order by id_requisicion ";
            
            else
                $sql = "select req.*,dreq.*,uo.nombre_unidad_operativa,dep.nombre_deposito
                from requisicion as req,det_requisicion as dreq,unidad_operativa as uo,deposito as dep,
                uni_med as um where dreq.requisicion_id = req.id_requisicion and um.id_uni_med = dprd.unidad_almacenamiento
                and uo.id_unidad_operativa = req.unidad_operativa_id and dep.id_deposito = req.deposito_id 
                and  req.empresa_id='$emp' and estatus_requisicion !='9' order by id_requisicion ";

        }else
        {
            $sql = "select req.*,uo.nombre_unidad_operativa,dep.nombre_deposito
            from requisicion as req,unidad_operativa as uo,deposito as dep
            where uo.id_unidad_operativa = req.unidad_operativa_id
            and dep.id_deposito = req.deposito_id and estatus_requisicion !='9' and req.empresa_id='$emp' order by id_requisicion ";
        }

        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();

    }
  //-------------------------------------------------------------------------	
  //METODO QUE CREA  ARREGLO DEL DETALLE DE REQUISICION SEA PRODUCTO O SERVICIO
  //--------------------------------------------------------------------------
  public function cargarDetalle($req)
  {
      //if($req)
      //{
          $sql = "select drq.*,prd.nombre_producto as requisito from det_requisicion as drq,producto as prd,det_producto as dprd
                where drq.requisicion_id = '".$req."' and drq.id_requisito = dprd.id_det_producto and prd.id_producto = dprd.producto_id
                and drq.tipo_requisito='PRODUCTO'
            union
    		select drq.*,svr.nombre_servicio as requisito from det_requisicion as drq,servicio as svr
    		where drq.requisicion_id = '".$req."' and drq.id_requisito = svr.id_servicio
                and drq.tipo_requisito='SERVICIO'" ;

      //}
      //die($sql);
      $res = $this->_db->query($sql);
      if($res){
          $res->setFetchMode(PDO::FETCH_ASSOC);
          return $res->fetchAll();
      }else
          return array();


  }

//---------------------------------------------------------------------------
//METODO QUE INSERTA REGISTRO EN LA TABLA REQUISICION Y DET REQUISICION
//---------------------------------------------------------------------------
public function insertar($datos)
{
    $this->_db->start();
    $sql="insert into requisicion("
            . "fecha_requisicion,"
            . "empresa_id,"
            . "unidad_operativa_id,"
            . "deposito_id,"
            . "departamento_id,"
            . "usuario_id,"
            . "tipo_requisicion,"
            . "plazo_requisicion,"
            . "motivo_requisicion,"
            . "prioridad_requisicion,"
            . "estatus_requisicion,"
            . "condicion_requisicion,"
            . "comentario_requisicion"
            . ")values("
            . "now(),"
            . "'".$datos['unidad']."',"
            . "'".$datos['empresa']."',"
            . "'".$datos['deposito']."',"
            . "'".$datos['deposito']."',"
            . "'".$datos['usuario']."',"
            . "'".strtoupper($datos['tipo_mae'])."',"
            . "'".$datos['plazo']."',"
            . "'".strtoupper($datos['motivo'])."',"
            . "'".strtoupper($datos['prioridad'])."',"
            . "'1',"
            . "'ABIERTA',"
            . "'".strtoupper($datos['comentario'])."'"
            . ")";
    //die($sql);
    $res = $this->_db->exec($sql);
    if(!$res)
    {
        $this->_db->cancel();
        return FALSE;
    }else
        {
            $ult_requisicion = $this->_db->getInsertedId();
            $producto = $datos['producto'];
            $cantidad = $datos['cantidad'];
            $marca    = $datos['marca'];
            $tipo     = $datos['tipo_det'];
            for($i = 0;$i < count($producto);$i++ )
            {
                $sql = "insert into det_requisicion("
                        . "fecha_creacion,"
                        . "requisicion_id,"
                        . "tipo_requisito,"
                        . "id_requisito,"
                        . "cantidad_requisito,"
                        . "estatus_det_requisito,"
                        . "condicion_requisito,"
                        . "usuario_creador"
                        . ")values("
                        . "now(),"
                        . "'".$ult_requisicion."',"
                        . "'".strtoupper($tipo[$i])."',"
                        . "'".$producto[$i]."',"
                        . "'".$cantidad[$i]."',"
                        . "'1','POR EVALUAR',"
                        . "'".$datos['usuario']."')";
                
                $res = $this->_db->exec($sql);
                if(!$res)
                {
                    $this->_db->cancel();
                    return FALSE;
                }
            }
            $this->_db->confirm();
            return true;
        }

}


//---------------------------------------------------------------------------------------------
//METODO QUE CREA ARREGLO PARA LA VISTA DE ANALISIS DE REQUISICIONES
//---------------------------------------------------------------------------------------------
public function analisis($val = false,$almacen = false)
{
        if($almacen)
        {
                $sql = "select req.* from requisicion as req where estatus_requisicion = '1'
                and condicion_requisicion ='POR EVALUAR' and req.deposito_id ='$almacen'
                order by req.id_requisicion";
        }else
        {
            if($val){
                $sql = "select req.*,per.pri_nombre_persona,per.pri_apellido_persona,dep.nombre_deposito,dpto.descripcion_departamento as depa
                                from requisicion as req,deposito as dep,usuario as usu,persona as per,departamento as dpto
                                where req.estatus_requisicion = '1' and  req.departamento_id = dpto.id_departamento and req.id_requisicion = '$val'
                                and usu.id_usuario=req.usuario_id and per.id_persona = usu.persona_id and dep.id_deposito = req.deposito_id
                                order by req.id_requisicion";
            }    
            else {

                  $sql = "select req.*,per.pri_nombre_persona,per.pri_apellido_persona,dep.nombre_deposito,dpto.descripcion_departamento as depa
                  from requisicion as req,deposito as dep,usuario as usu,persona as per,departamento as dpto
                  where estatus_requisicion = '1' and  req.departamento_id = dpto.id_departamento
                  and usu.id_usuario=req.usuario_id and per.id_persona = usu.persona_id and dep.id_deposito = req.deposito_id
                  order by req.id_requisicion";

            }
        }

		//die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $data = $res->fetchAll();
            if(count($data))
            {
               $cab = array();
               $i = 0;
               foreach($data as $row)
               {
		    $cab[$i]= array('id' => $row['id_requisicion'],'fecha' => $row['fecha_requisicion'],'dpto'=>$row['depa'],
                    'plazo' => $row['plazo_requisicion'],'tipo' => $row['tipo_requisicion'],'prioridad' => $row['prioridad_requisicion'],
                    'motivo' => $row['motivo_requisicion'],'responsable'=> $row['usuario_id'],'unidad'=> $row['unidad_operativa_id'],
                    'solicitante'=> $row['pri_nombre_persona'].' '.$row['pri_apellido_persona'],'deposito'=>$row['nombre_deposito'],
                    'condicion'=>$row['condicion_requisicion']);

                    $det = $this->cargarDetalle($row['id_requisicion']);
                    if(count($det)>0)
		   {
                        $dat = array();
						$j=0;
                        $inv = array();
			foreach($det as $val)
			{
                            $dat[$j]['id_detalle'] = $val['id_det_requisicion'];
                            $dat[$j]['descripcion'] = $val['requisito'];
                            $dat[$j]['cantidad'] = $val['cantidad_requisito'];
                            $dat[$j]['codigo'] = $val['id_requisito'];
                            $dat[$j]['tipo'] = $val['tipo_requisito'];
                            $dat[$j]['condicion'] = $val['condicion_requisito'];
                            $j++;

                            if($val['tipo_requisito']=="PRODUCTO")
                            {
                              $sql = "select prd.nombre_producto,dep.nombre_deposito,sum(stock.cantidad)as existencia
                                      from stock,det_producto as dprd,producto as prd, deposito as dep
                                      where stock.producto_id = dprd.id_det_producto and prd.id_producto = dprd.producto_id
                                      and dep.id_deposito = stock.deposito_id and stock.producto_id = '".$val['id_requisito']."'
                                      group by stock.deposito_id ";

                               //die($sql);
                               $res = $this->_db->query($sql);
                               if($res)
                               {
                                  $res->setFetchMode(PDO::FETCH_ASSOC);
                                  $inv = $res->fetchAll();
                               }

                            }
                        }
                        $cab[$i]['detalle']= $dat;
						$cab[$i]['inv']= $inv;
		}
                    $i++;
            }
            return $cab;
	}else
            return array();
}else
    return array();

}

//-----------------------------------------------------------------------------------------------------
//METODO QUE ACTUALIZA REGISTRO DE REQUISITO PARA EVALUACION
//---------------------------------------------------------------------------------------------------
public function evaluar($id,$com,$val,$usu)
{


    $valor = ($val =='RECHAZADO')?'RECHAZADO':'POR COTIZAR';

    $sql ="update det_requisicion set condicion_requisito='".$valor."',fecha_evaluacion =now(),
    comentario_evaluacion='".$com."',usuario_evaluacion='$usu' 	where id_det_requisicion='".$id."'";

    //die($sql);
    $res = $this->_db->exec($sql);
    if($res){        
        return true;
    }else
            return false;

}

    
//---------------------------------------------------------------------------------------
//METODO QUE BUSCA MAESTRO DE REQUISICION POR ID 
//---------------------------------------------------------------------------------------
public function buscarRequisicion($id)
{
    $data = array();
    $sql = "select req.*,dep.nombre_deposito,up.nombre_unidad_operativa 
            from requisicion as req,deposito as dep, unidad_operativa as up 
            where id_requisicion = '$id' and req.deposito_id = dep.id_deposito 
            and req.unidad_operativa_id = up.id_unidad_operativa ";
    //die($sql);
    $res = $this->_db->query($sql);
    if($res){
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $dt =  $res->fetch();
        $data['mae'] = $dt;
        $data['det'] = $this->cargarDetalle($id);

       //print_r($data); exit();
        return $data;
    }else
        return array();

}
	
	

public function verificarStockProd($deposito,$producto,$presentacion)
{
    $sql = "select count(*) as total from stock "
            . "where producto_id = '$producto' and deposito_id = '$deposito' and presentacion_id='$presentacion'";
    $res = $this->_db->query($sql);
    if($res){
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $res->fetch();
        if($datos['total']>0)
            return $res->fetch();
        else
            return array("total"=>0);
    }else
        return array("total"=>0);

}
 
//------------------------------------------------------------------------------
//METODO QUE DESACTIVA REQUISICION 
//------------------------------------------------------------------------------    
public function desactivar($id)
{
    $sql = "update requisicion set estatus_requisicion='9', condicion_requisicion='ELIMINADO' where id_requisicion ='$id'";
    $res = $this->_db->exec($sql);
    if(!$res)
    {
        //$this->cancelar();
        return FALSE;
    }else
        {
        return TRUE;            
    }

}




}
