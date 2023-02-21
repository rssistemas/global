<?php
class unidadMedidaModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    //metodo que carga todos los permisos
    public function cargarUnidadMedida($ref=false)
    {
        if($ref)
        {
            $sql = "select * from uni_med where nombre_uni_med like '%$ref%' order by nombre_uni_med";
        }
        else
        {
            $sql = "select * from uni_med order by nombre_uni_med";
        }

        
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }
    
    public function incluirUnidadMedida($datos)
    {
        if($datos)
        {
            $sql = "insert into uni_med("
                    . "nombre_uni_med,"
                    . "simbolo_uni_med,"
                    . "estatus_uni_med,"
                    . "fecha_creacion)"
                    . "values("
                    . "'".ucfirst($datos['nombre'])."',"
                    . "'".ucfirst($datos['simbolo'])."',"
                    . "'1',"
                    . "now())";
            $res = $this->_db->exec($sql);
            if(!$res)
            {
                return FALSE;               
            }  else {
                return TRUE;
            }
        }
            
        
    }
    public function buscar($id)
    {
        $sql = "select * from uni_med where id_uni_med = '$id'";
        $res = $this->_db->query($sql);
        if($res)
        {    
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
        {
            return array();
        }
        
    }
    public function modificarU($datos)
    {
        if($datos)
        {
            $sql = "update uni_med set "
                    . "nombre_uni_med = '".$datos['nombre']."',"
                    . "simbolo_uni_med = '".$datos['simbolo']."'"
                    . " where id_uni_med = '".$datos['id']."'";
            $res = $this->_db->exec($sql);
            if(!$res)
            {
                return FALSE;               
            }
            else
            {
                return TRUE;
            }
            
        }    
    }        
    public function buscarUnidadMedida($referencia)
    {
        $sql = "select * from  uni_med where nombre_uni_med = '$referencia' ";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return FALSE;
        
    }
    public function activar($unidadM)
    {
        $sql = "update uni_med  set estatus_uni_med = '1' where id_uni_med='$unidadM'";
        $res = $this->_db->exec($sql);
            if(!$res)
                return FALSE;               
            else
                return TRUE;
    }        
    
    public function desactivar($unidadM)
    {
        $sql = "update uni_med  set estatus_uni_med = '9' where id_uni_med='$unidadM'";
        $res = $this->_db->exec($sql);
            if(!$res)
                return FALSE;               
            else
                return TRUE;
    }
    
    public function buscarAutoMedida($valor)
    {
        $sql ="Select id_uni_med,nombre_uni_med from uni_med where nombre_uni_med like '%$valor%' order by id_uni_med";
        //die($sql);
	$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            
            $datos = $res->fetchAll();
            foreach ($datos as $valor)
            {
                $val[] = array(
                "label"=>$valor['nombre_uni_med'],
                "value"=>array("nombre"=>$valor['nombre_uni_med'],"id"=>$valor['id_uni_med']));
            }
           
            return $val;
        }else
            return array();
        
    }
    
    
      
}