<?php
class cargoModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
    public function cargarCargo($ref=false)
    { 
        if($ref)
        {
            $sql = "select car.*,dep.descripcion_departamento as medida from cargo as car,departamento as dep"
            . " where car.departamento_id = dep.id_departamento and car.nombre_cargo like '%$ref%' order by car.nombre_cargo";
        }
        else
        {   
            $sql = "select car.*,dep.descripcion_departamento as medida from cargo as car,departamento as dep"
            . " where car.departamento_id = dep.id_departamento order by car.nombre_cargo";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    } 
    public function incluir($datos)
    {
        if($datos)
        {
            $sql = "insert into cargo(nombre_cargo, fecha_creacion, estatus_cargo,departamento_id)
             values('".ucfirst($datos['descripcion'])."',	now(), 1,".$datos['medida'].")";
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
        $sql = "update cargo set "
                . "nombre_cargo = '".$datos['descripcion']."' , "
                . "departamento_id= ".$datos['medida']." "
                . " where id_cargo = ".$datos['id'];
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
    public function buscarMarca($descripcion)
    {
        $sql = "select * from cargo as car where car.nombre_cargo = '$descripcion'";
       // die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return FALSE;
    }
    public function buscar($id)
    {
        $sql = "select * from cargo where id_cargo='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
        
    }
    public function comprobar($datos)
    {
        $sql ="select car.id_cargo from cargo as car where car.nombre_cargo = ".$datos['descripcion'];
        $res = $this->_db->query($sql);
        if($res->rowCount()>0)
            return TRUE;
        else
            return FALSE;
    }
    public function desactivar($id)
    {
        $sql = "update cargo set estatus_cargo = '9' where id_cargo = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }        
}
