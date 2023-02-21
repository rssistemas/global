<?php
class TipoTransporteModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    // para cargar listado de registros en la vista principal
    public function cargarTipoTransporte_index($ref=false)
    {
        if($ref)
        {
            $sql = "select * from tipo_transporte where nombre_tipo_trans like '%$ref%' order by nombre_tipo_trans";
        }
        else
        {
            $sql = "select * from tipo_transporte order by nombre_tipo_trans";
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
    
   // cargar registros activos para combos de seleccion en otras vistas
    public function cargarTipoTransporte()
    {
        $sql = "select * from tipo_transporte where estatus_tipo_trans = '1'";
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

    //para incluir un nuevo registro
    public function incluir($datos)
    {
        $sql = "insert into tipo_transporte("
            . "nombre_tipo_trans,"
            . "descripcion_tipo_trans,"
            . "estatus_tipo_trans,"
            . "fecha_creacion) values("
            . "'".$datos['descripcion']."',"
            . "'".$datos['comentario']."',"
            . "'1',"
            . "now())";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    //para modificar un registro
    public function modificar($datos)
    {
        $sql = "update tipo_transporte set "
            . "nombre_tipo_trans = '".$datos['descripcion']."',"
            . "descripcion_tipo_trans = '".$datos['comentario']."'"
            . " where id_tipo_transporte = '".$datos['id']."'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;               
        }
        else
        {
            return FALSE;
        }
    }
    
    //para eliminar logicamente un registro
    public function desactivar($id)
    {
        $sql = "update tipo_transporte set estatus_tipo_trans = '9' where id_tipo_transporte = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            return false;
        }
    }

    //para verificar que no se repita un mismo registro
    function verificar_existencia($ref)
    {
        $sql = "select count(*)as total from tipo_transporte where nombre_tipo_trans = '$ref'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $data = $res->fetch();
            if($data['total'] > 0)
            {
                return $data;
            }
            else
            {
                return array("total" => 0);
            }
        }
        else
        {
            return array("total" => 0);
        }
    }

    //para consultar un registro por el id
    public function buscar($id)
    {
        $sql = "select * from tipo_transporte where id_tipo_transporte = '$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
        {
            return array();
        }
    }

}//FIN DE LA CLASE OBJETO DEL MODELO