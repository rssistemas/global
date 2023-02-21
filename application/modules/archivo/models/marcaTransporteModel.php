<?php
class marcaTransporteModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
    // para cargar listado de registros en la vista principal
    public function cargarMarcaTransporte_index($ref=false)
    { 
        if($ref)
        {
            $sql = "select * from marca_transporte where nombre_marca like '%$ref%' order by nombre_marca";
        }
        else
        {
            $sql = "select * from marca_transporte order by nombre_marca";
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
    public function cargarMarcaTransporte()
    {
        $sql = "select * from marca_transporte where estatus_marca = '1'";
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
        $sql = "insert into marca_transporte("
            ."nombre_marca,"
            ."fecha_creacion,"
            ."estatus_marca) values("
            ."'".$datos['descripcion']."',"
            ."now(),"
            ."'1')";
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
        $sql = "update marca_transporte set "
                . "nombre_marca = '".$datos['descripcion']."'"
                . " where id_marca = '".$datos['id']."'";
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

    //para eliminar logicamente un registro
    public function desactivar($id)
    {
        $sql = "update marca_transporte set estatus_marca = '9' where id_marca = '$id'";
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
    public function verificar_existencia($ref)
    {
        $sql = "select count(*)as total from marca_transporte where nombre_marca = '$ref'";
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
        $sql = "select * from marca_transporte where id_marca = '$id'";
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