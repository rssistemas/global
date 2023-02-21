<?php
class monedaModel  extends model
{
    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarMoneda($ref=false)
    {
        if($ref)
        {
            $sql = "select * from moneda"
            . " where nombre_moneda like '%$ref%' order by nombre_corto ASC";
        }
        else
        {
            $sql = "select * from moneda"
            . " order by nombre_corto ASC";
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
    public function cargarTipoDocumento()
    {
        $sql = "select * from tipodocumento where estatus_tipo_documento = '1'";
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
        $sql = "insert into tipodocumento("
            ."nombre_tipo_documento,"
            . "ncorto,accion,"
            ."fecha_creacion,"
            ."estatus_tipo_documento) values("
            ."'". $datos['nombre']."',"
            ."'".strtoupper($datos['corto'])."',"
            ."'".strtoupper($datos['accion'])."',"
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
        $sql = "update tipo_documento set "
                . "nombre_tipo_documento = '". $datos['nombre']."',"
                . "ncorto = '". strtoupper($datos['corto'])."',"
                . "accion = '".  strtoupper($datos['accion'])."' "
                . " where id_tipo_documento = '".$datos['id']."'";
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
    public function estatusTipoDocumento($id,$est)
    {
        $sql = "update tipodocumento set estatus_tipo_documento = '$est' where id_tipo_documento = '$id'";
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
        $sql = "select count(*)as total from tipodocumento "
        . "where nombre_tipo_documento = '$ref'";
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

    //VERIFICAR UTILIZACION
    public function verificar_uso($cod)
    {
        $sql =" select count(*) as total from recepcion where tipo_doc_ori=".$cod ;
        $res = $this->_db->query($sql);
        if($res)
        {
        /*esta parte del codigo espera que el sql retorna una columna de  nombre total
        pregunta si es mayor a cero retorna $data que es igual $data['total'] sino
        data es falso, pero como esta funcion es utilizada por un jquery tambien, entoces no
        puedo devolver falso y creo un array con un indice total con valor 0  = array("total" => 0)*/
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
        $sql = "select * from tipodocumento where id_tipo_documento = '$id'";
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

    public function documentoEntrada()
    {
        $sql = "select * from tipodocumento where accion = 'ENTRADA'";
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

    public function documentoSalida()
    {
        $sql = "select * from tipodocumento where accion = 'SALIDA'";
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
    // metodo que busca el tipo de documento por nombre o nombre corto
    public function buscarDocumento($valor)
    {
        $sql = "select * from tipodocumento where nombre_tipo_documento = '".strtoupper($valor)."' or ncorto = '".strtoupper($valor)."' ";
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
