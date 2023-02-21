<?php
class recursoModel extends model{
    
    public function __construct() {
        parent::__construct();
    }
    //metodo del controlador que carga un listado de usuarios
    public function cargarRecurso($ref=false)
    {                
        if($ref)
        {
            $sql = "select rec.*,modu.nombre_modulo as modulos from recurso as rec,modulo as modu"
            . " where rec.nombre_recurso like '%$ref%' order by rec.nombre_recurso";
        }
        else
        {        
            $sql = "select rec.*,modu.nombre_modulo as modulos from recurso as rec, modulo as modu"
                . " where rec.modulo_id = modu.id_modulo order by rec.nombre_recurso";
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
        $sql = "select * from recurso where id_recurso='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
    }
    
    //--------------------------------------------------------------------------
    //    METODO PARA CARGAR LOS RECURSOS DE UN MODULO 
    //--------------------------------------------------------------------------
    public function buscarRecMod($modulo)
    {
        $sql = "select * from recurso where modulo_id='$modulo' order by nombre_recurso";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    }
    
    public function insertar($datos)
    {
        if($datos)
        {
            $sql = "insert into recurso("
                    . "nombre_recurso,"
                    . "descripcion_recurso,"
                    . "url_recurso,"
                    . "icon_recurso,"
                    . "posicion_recurso,"
                    . "estatus_recurso,"
                    . "modulo_id) values("
                    . "'".$datos['nombre']."',"
                    . "'".$datos['descripcion']."',"
                    . "'".$datos['url']."',"
                    . "'".$datos['icono']."',"
                    . $datos['posicion'].", 1,"
                    . $datos['modulo']." )";
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
        $sql = "update recurso set "
                . "nombre_recurso = '".$datos['nombre']."' , "
                . "descripcion_recurso = '".$datos['descripcion']."' , "
                . "url_recurso = '".$datos['url']."' , "
                . "icon_recurso = '".$datos['icono']."' , "
                . "posicion_recurso = ".$datos['posicion']." , "
                . "modulo_id = ".$datos['modulo']
                . " where id_recurso = ".$datos['id'];
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
    public function estatusRecurso($id,$est)
    {
        $sql = "update recurso set estatus_recurso = '$est' where id_recurso = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }        
}
