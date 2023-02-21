<?php
class cotizacionModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function cargarCotizacion($valor = false)
    {
        if($valor)
        {
            $sql = "select  from cotizacion where proveedor_id ='$valor' and estatus_cotizacion !='9' order by fecha_creacion ";
        } else {
            
            $sql="select * from cotizacion where estatus_coyizacion !='9' order by id_cotizacion,fecha_creacion";
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
    //--------------------------------------------------------------------------
    //METODO QUE CARGA EL MAESTRO DE REQUERIMIENTOS UNIDO CON LAS PLANIFICACIONES
    //
    //--------------------------------------------------------------------------
    public function cargarSolicitud()
    {
        
        $sql ="select 'REQUISICION' as origen,id_requisicion as id,fecha_requisicion as fecha,concat(per.pri_nombre_persona,' ', per.pri_apellido_persona)as solicitante,
            dep.nombre_deposito as destino,req.prioridad_requisicion as prioridad,req.plazo_requisicion as plazo
            from requisicion as req,det_requisicion as dreq,usuario as usu,persona as per,deposito as dep
            where usu.id_usuario = req.usuario_id and per.id_persona = usu.persona_id and dep.id_deposito = req.deposito_id and 
            dreq.requisicion_id = req.id_requisicion and dreq.condicion_requisito ='POR COTIZAR'
        UNION
            select 'PLANIFICACION' as origen,pln.id_planificacion as id,pln.fecha_creacion as fecha,concat(per.pri_nombre_persona,' ',per.pri_apellido_persona)as solicitante,
            dep.descripcion_departamento as destino,dpln.prioridad_requisito as prioridad,plazo_ejecucion as plazo  
            from pln_compra as pln,det_pln_compra as dpln,usuario as usu,persona as per,departamento as dep 
            where usu.id_usuario = pln.usuario_pln and per.id_persona = usu.persona_id and dep.id_departamento = pln.departamento_id 
            and dpln.pln_compra_id = pln.id_planificacion and dpln.condicion_requisito='POR COTIZAR' order by origen";
        
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
    
    public function buscarSolicitud($tipo,$valor)
    {
        
        $solicitud = array();
        if($tipo == 'REQUISICION'){
            $sql = "select * from requisicion where id_requisicion ='$valor' "; 
            $res = $this->_db->query($sql);
            if($res)
            {
                $res->setFetchMode(PDO::FETCH_ASSOC);
                $maestro = $res->fetch();
                if(count($maestro))
                {
                    $sql = "select * from ";
                    
                }
            }
            
        }
        
        
        
    }
    
    
//--------------------------------------------------------------------------------------------------
//METODO QUE BUSCA LOS DETALLES DE REQUISICION ES CONDICION (POR COTIZAR) FILTRADO POR LOS PARAMETROS
//--------------------------------------------------------------------------------------------------
public function buscarRequisitoProducto($empresa,$unidad,$valor = false)
{
    if($valor!='undefined')
    {
            $sql="select dreq.*,prd.nombre_producto,req.fecha_requisicion,req.tipo_requisicion from det_requisicion as dreq,det_producto as dprd,producto as prd,requisicion as req "
                . "where dreq.condicion_requisito ='POR COTIZAR' and dreq.requisicion_id = req.id_requisicion and req.empresa_id='$empresa'"
                . " and req.unidad_operativa_id ='$unidad' "
                . "and dprd.id_det_producto = dreq.id_requisito and dreq.tipo_requisito ='PRODUCTO' and dprd.producto_id = prd.id_producto "
                . "and prd.nombre_producto like '%$valor%' order by id_det_requisicion  ";
    } else {
            $sql="select dreq.*,prd.nombre_producto,req.fecha_requisicion,req.tipo_requisicion from det_requisicion as dreq,det_producto as dprd,producto as prd,requisicion as req "
                . "where dreq.condicion_requisito ='POR COTIZAR' and dreq.requisicion_id = req.id_requisicion and req.empresa_id='$empresa'"
                . " and req.unidad_operativa_id ='$unidad' "
                . "and dprd.id_det_producto = dreq.id_requisito and dreq.tipo_requisito ='PRODUCTO' and dprd.producto_id = prd.id_producto "
                . " order by id_det_requisicion  ";
    }
    //die($sql);
    $res = $this->_db->query($sql);
    if($res)
    {
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }else
        return array();
       
}

public function requisitoProducto($empresa,$unidad,$valor)
{
    
    $sql="select dreq.*,prd.nombre_producto,req.fecha_requisicion,req.tipo_requisicion from det_requisicion as dreq,det_producto as dprd,producto as prd,requisicion as req "
        . "where dreq.condicion_requisito ='POR COTIZAR' and dreq.requisicion_id = req.id_requisicion and req.empresa_id='$empresa'"
        . " and req.unidad_operativa_id ='$unidad' "
        . "and dprd.id_det_producto = dreq.id_requisito and dreq.tipo_requisito ='PRODUCTO' and dprd.producto_id = prd.id_producto "
        . "and dreq.id_det_requisicion = '$valor' ";
    
    //die($sql);
    $res = $this->_db->query($sql);
    if($res)
    {
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetch();
    }else
        return array();
       
}




//--------------------------------------------------------------------------------------------------
//METODO QUE BUSCA LOS DETALLES DE REQUISICION ES CONDICION (POR COTIZAR) FILTRADO POR LOS PARAMETROS
//--------------------------------------------------------------------------------------------------
public function buscarRequisitoServicio($empresa,$unidad,$valor = false)
{
    if($valor!='undefined')
    {
        $sql="select dreq.*,prd.nombre_producto,req.fecha_requisicion,req.tipo_requisicion from det_requisicion as dreq,servicio as ser,requisicion as req "
            . "where dreq.condicion_requisito ='POR COTIZAR' and dreq.requisicion_id = req.id_requisicion and req.empresa_id='$empresa'"
            . " and req.unidad_operativa_id ='$unidad' "
            . "and  dreq.tipo_requisito ='SERVICIO' and dreq.id_requisito = ser.id_servicio and ser.nombre_servicio like '%$valor%' order by id_det_requisicion  ";
    }else
    {
        $sql="select dreq.*,prd.nombre_producto,req.fecha_requisicion,req.tipo_requisicion from det_requisicion as dreq,servicio as ser,requisicion as req "
            . "where dreq.condicion_requisito ='POR COTIZAR' and dreq.requisicion_id = req.id_requisicion and req.empresa_id='$empresa'"
            . " and req.unidad_operativa_id ='$unidad' "
            . "and  dreq.tipo_requisito ='SERVICIO' and dreq.id_requisito = ser.id_servicio  order by id_det_requisicion  ";
        
    }
    //die($sql);
    $res = $this->_db->query($sql);
    if($res)
    {
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }else
        return array();
       
}

public function requisitoServicio($empresa,$unidad,$valor = false)
{
    
        $sql="select dreq.*,ser.nombre_servicio,req.fecha_requisicion,req.tipo_requisicion from det_requisicion as dreq,servicio as ser,requisicion as req "
            . "where dreq.condicion_requisito ='POR COTIZAR' and dreq.requisicion_id = req.id_requisicion and req.empresa_id='$empresa'"
            . " and req.unidad_operativa_id ='$unidad' and dreq.tipo_requisito ='SERVICIO' and dreq.id_requisito = ser.id_servicio "
            . " and dreq.id_det_requisicion =  '$valor' ";
        
    
    //die($sql);
    $res = $this->_db->query($sql);
    if($res)
    {
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetch();
    }else
        return array();
       
}
    
}