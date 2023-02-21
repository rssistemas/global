<?php
class moduloModel extends model{
    
    public function __construct() {
        parent::__construct();
    }
    //metodo del controlador que carga un listado de usuarios
    public function cargarModulo($ref=FALSE)
    { 
        if($ref)
            $sql = "select * from modulo where nombre_modulo like '%$ref%' order by nombre_modulo";
        else
        {
            $sql = "select * from modulo order by nombre_modulo";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    } 
    //metodo del controlador que busca un usuario por su id 
    
    public function buscar($id)
    {
        $sql = "select * from modulo where id_modulo='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
    }

    public function insertar($datos)
    {
        if($datos)
        {
            $sql = "insert into modulo("
                    . "nombre_modulo,"
                    . "descripcion_modulo,"
                    . "url_modulo,"
                    . "icon_modulo,"
                    . "posicion_modulo,"
                    . "estatus_modulo,"
                    . "clave_modulo,"
                    . "condicion_modulo,"
                    . "organizacion_id,"
                    . "fecha_creado,"
                    . "usuario_creado "
                    . ") values("
                    . "'".$datos['nombre']."',"
                    . "'".$datos['descripcion']."',"
                    . "'".$datos['url']."',"
                    . "'".$datos['icono']."',"
                    . $datos['posicion'].","
                    . "1,"
                    . "'".$datos['clave']."',"
                    . "'".$datos['condicion']."',"
                    . "'".$datos['organizacion']."',"
                    . "now(),"
                    . "'".$datos['usuario']."'"
                    
                    . ")";
            $res = $this->_db->exec($sql);
            if($res)
            {
                $this->_ultimo_registro = $this->_db->lastInsertId();	
                return TRUE;	
            }
            else
                return false;
        }    
    }
    public function modificar($datos)
    {
        $sql = "update modulo set "
                . "nombre_modulo = '".$datos['nombre']."' , "
                . "descripcion_modulo = '".$datos['descripcion']."' , "
                . "url_modulo = '".$datos['url']."' , "
                . "icon_modulo = '".$datos['icono']."' , "
                . "posicion_modulo = ".$datos['posicion']." , "
                . "clave_modulo = '".$datos['clave']."' "
                . " where id_modulo = ".$datos['id'];
		//die($sql);		
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
    public function desactivar($id)
    {
        $sql = "update modulo set estatus_modulo = '9' where id_modulo = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }        
}
