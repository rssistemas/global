<?php
class departamentoModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    //metodo que carga todos los permisos
    public function cargarDepartamento($ref=false)
    {
        if($ref)
        {
            $sql = "select * from departamento where descripcion_departamento like '%$ref%' order by descripcion_departamento";
        }
        else
        {
            $sql = "select * from departamento order by descripcion_departamento";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();    
        }else
        {
            return array();
        }
    }
    
    public function incluirDepartamento($datos)
    {
        if($datos)
        {
            $sql = "insert into departamento("
                    . "descripcion_departamento,"
                    . "telefono_departamento,"
                    . "estatus_departamento,"
                    . "fecha_creacion)"
                    . "values("
                    . "'".$datos['descripcion']."',"
                    . "'".$datos['telefono']."',"
                    . "'1',"
                    . "now())";
            $res = $this->_db->exec($sql);
            if(!$res)
                return FALSE;               
            else
                return TRUE;
        }
    }
    public function modificarDepartamento($datos)
    {
        if($datos)
        {
            $sql = "update departamento set "
                    . "descripcion_departamento = '".$datos['descripcion']."',"
                    . "telefono_departamento = '".$datos['telefono']."'"
                    . " where id_departamento = '".$datos['id']."'";
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
    public function buscar($id)
    {
        $sql = "select * from  departamento where id_departamento = '$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return FALSE;
        
    }
    public function buscarDepartamento($referencia)
    {
        $sql = "select * from  departamento where descripcion_departamento = '$referencia' ";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return FALSE;
        
    }
    
    public function desactivar($id_ref)
    {
        $sql = "update departamento set estatus_departamento = '9' where id_departamento='$id_ref'";
        $res = $this->_db->exec($sql);
        if(!$res)
            return FALSE;               
        else
            return TRUE;
    }
      
}
?>