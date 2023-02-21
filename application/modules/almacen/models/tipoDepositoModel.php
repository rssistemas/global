<?php
class tipoDepositoModel extends model
{
    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarTipoDeposito_index($ref=false)
    {
        if($ref)
        {
            $sql = "select * from tipodeposito "
            . "where descripcion_tipo_deposito like '%$ref%' "
            . "order by descripcion_tipo_deposito ASC";
        }
        else
        {
            $sql = "select * from tipodeposito"
            . " order by descripcion_tipo_deposito ASC";
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
    public function cargarTipoDeposito()
    {
        $sql = "select * from tipodeposito"
        . " where estatus_tipo_deposito = '1' "
        . "order by descripcion_tipo_deposito ASC ";
        $res = $this->_db->query($sql);
        if ($res) {
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
        $sql = "insert into tipodeposito("
            . "nombre_tipo_deposito,"
            . "descripcion_tipo_deposito,"
            . "estatus_tipo_deposito,"
            . "fecha_creacion)"
            . "values("
            . "'".strtoupper($datos['descripcion'])."',"
            . "'".strtoupper($datos['comentario'])."',"
            . "'1',"
            . "now())";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;               
        }
        else
        {
             $this->regLog();
            return FALSE;
        }
    }
    
    //para modificar un registro
    public function modificar($datos)
    {
        $sql = "update tipodeposito set "
            . "nombre_tipo_deposito = '". strtoupper($datos['descripcion'])."',"
            . "descripcion_tipo_deposito = '".strtoupper($datos['comentario'])."'"
            . " where id_tipo_deposito = '".$datos['id']."'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
             $this->regLog();
            return FALSE;
        }
    }
    
    //para eliminar logicamente un registro
    public function estatusTipoDeposito($id,$est)
    {
        $sql = "update tipodeposito set estatus_tipo_deposito = '$est' "
        . "where id_tipo_deposito = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
             $this->regLog();
            return FALSE;
        }
    }
    
    //para verificar que no se repita un mismo registro
    public function verificar_existencia($ref)
    {
        $sql = "select count(*)as total from tipodeposito"
        . " where nombre_tipo_deposito = '$ref'";    
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $data = $res->fetch();
            if($data['total']>0)
            {
                return $data;
            }
            else
            {
                return array("total"=>0);
            }
        }
        else
        {
            return array("total"=>0);
        }
    }

    //VERIFICAR UTILIZACION
    public function verificar_uso($cod)
    {
        $sql =" select count(*) as total from deposito"
        . " where deposito.tipo_deposito=".$cod;
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
    
    
    public function eliiminarTipoDeposito($valor)       
    {
        $sql="delete  from tipodeposito where id_tipo_deposito = '$valor'";
        
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            $this->regLog();
            return FALSE;
        }        
    }
    
    
    //para consultar un registro por el id
    public function buscar($id)
    {
        $sql = "select * from  tipodeposito where id_tipo_deposito = '$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
        {
            return FALSE;
        }
    }
}//FIN DE LA CLASE OBJETO DEL MODELO