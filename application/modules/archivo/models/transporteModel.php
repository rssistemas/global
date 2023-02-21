<?php
class transporteModel extends model{

    public function __construct() {
        parent::__construct();
    }

    //metodo del controlador que carga un listado de usuarios
    public function cargarTransportes($ref = false)
    {
        if($ref)
        {
            $sql = "select trans.*, tip_trans.nombre_tipo_trans as tipo_transporte, mar_trans.nombre_marca as marca_transporte,"
                . " medida.nombre_uni_med as unidad "
            . "from transporte as trans, tipo_transporte as tip_trans, marca_transporte as mar_trans, uni_med as medida "
            . "where tip_trans.id_tipo_transporte = trans.tipo_trans_id and trans.unidad_medida_id=medida.id_uni_med and mar_trans.id_marca=trans.marca_transporte_id "
            . " and tip_trans.nombre_tipo_trans like '%$ref%' order by tip_trans.nombre_tipo_trans";
        }
        else
        {
            $sql = "select trans.*, tip_trans.nombre_tipo_trans as tipo_transporte, mar_trans.nombre_marca as marca_transporte,"
                    . " medida.nombre_uni_med as unidad "
                . "from transporte as trans, tipo_transporte as tip_trans, marca_transporte as mar_trans, uni_med as medida "
                . "where tip_trans.id_tipo_transporte = trans.tipo_trans_id and trans.unidad_medida_id=medida.id_uni_med and mar_trans.id_marca=trans.marca_transporte_id "
                . " order by tip_trans.nombre_tipo_trans";
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
    //metodo del controlador que busca un usuario por su id 
    public function buscar($id)
    {
         $sql="select trans.*, tip_trans.nombre_tipo_trans as tipo, medida.nombre_uni_med as unidad "
            . "from transporte as trans, tipo_transporte as tip_trans, uni_med as medida "
            . "where trans.id_transporte=".$id." and tip_trans.id_tipo_transporte = trans.tipo_trans_id and trans.unidad_medida_id=medida.id_uni_med ";
        //die($sql);
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
    public function insertar($datos)
    {
        if($datos)
        {
            $sql = "insert into transporte("
                    . "placa,"
                    . "marca_transporte_id,"
                    . "modelo,"
                    . "capacidad,"
                    . "condicion_transporte,"
                    . "tipo_trans_id,"
                    . "unidad_medida_id,"
                    . "fecha_creacion,"
                    . "estatus_transporte"
                    . ")values("
                    . "'".$datos['placa']."', "
                    . "'".$datos['marca']."', "
                    . "'".$datos['modelo']."', "
                    . "'".$datos['capacidad']."', "
                    . "'".$datos['condicion']."', "
                    . "'".$datos['tip_trans']."',"
                    . "'".$datos['uni_med']."', "
                    . "now(), "
                    . "'1')";
            $res = $this->_db->exec($sql);
            if($res)
            {
                return true;
            }
            else
            {
                return false;
            }  
        }    
    }
    public function modificar($datos)
    {
        if($datos)
        {
            $sql = "update transporte set "
            . "placa = '".$datos['placa']."',"
            . "marca_transporte_id = '".$datos['marca']."',"
            . "modelo = '".$datos['modelo']."',"
            . "capacidad = '".$datos['capacidad']."',"
            . "tipo_trans_id = '".$datos['tip_trans']."',"
            . "unidad_medida_id = '".$datos['uni_med']."'"
            . " where id_transporte = '".$datos['id']."'";
            $res = $this->_db->exec($sql);
            if (!$res) {
                return FALSE;
            } else {
                return TRUE;
            }
        }    
    }
    
    //para eliminar logicamente un registro
    public function desactivar($id)
    {
        $sql="update transporte set estatus_transporte='9' where id_transporte='$id'";
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

    //para verificar que no se repita un mismo registro
    public function verificar_existencia($ref)
    {
        $sql = "select count(*)as total from transporte where placa = '$ref'";
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

}//FIN DE LA CLASE OBJETO DEL MODELO