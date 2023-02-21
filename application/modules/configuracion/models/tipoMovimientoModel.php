<?php
class tipoMovimientoModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
// para cargar listado de registros en la vista principal
    public function cargarTipoMovimiento_index($ref = false)
    {
        if($ref)
            $sql = "select * from tipomovimiento "
            . "where nombre_tipo_movimiento like '%".strtoupper($ref)."%' ";
        else
            $sql = "select * from tipomovimiento"
            . " order by nombre_tipo_movimiento ASC  ";
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();  		
    }
    
    // cargar registros activos para combos de seleccion en otras vistas
    public function cargarTipoMovimiento()
    {
        $sql = "select * from tipomovimiento where estatus_tipo_movimiento = '1'";
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
        $sql="insert into tipomovimiento(fecha_creacion, nombre_tipo_movimiento,accion, estatus_tipo_movimiento) "
        . "values(now(),'".$datos['nombre']."','".strtoupper($datos['accion'])."','1')";
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
        $sql="update tipo_movimiento set nombre_tipo_movimiento= '".$datos['nombre']
        ."', accion= '".strtoupper($datos['accion'])."' where id_tipo_movimiento= '".$datos['id']."'";
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
    public function estatusTipoMovimiento($id,$est)
    {
        $sql = "update tipomovimiento set estatus_tipo_movimiento = '$est' where id_tipo_movimiento = '$id'";
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
        $sql = "select count(*)as total from tipomovimiento"
        . " where nombre_tipo_movimiento = '$ref'";
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
        $sql =" select count(*) as total from movimiento"
            . " where movimiento.tipo_movimiento='$cod'";
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
    public function buscar($ref)
    {
    	if(is_int($ref))
        	$sql = "select * from tipomovimiento where id_tipo_movimiento='".$ref."' ";
		else
			$sql = "select * from tipomovimiento where nombre_tipo_movimiento='".$ref."' ";
		
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