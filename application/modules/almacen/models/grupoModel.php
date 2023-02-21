<?php
class grupoModel extends model
{
    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarGrupo($ref=false)
    {
        if($ref)
        {
            $sql = "select * from grupo where nombre_grupo like '%$ref%' order by nombre_grupo";
        }
        else
        {
            $sql = "select * from grupo order by nombre_grupo";
        }
        $res = $this->_db->query($sql);
        if ($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
        {
            return array();
        }
    }
	
	public function cargarGrupoCla($ref)
    {
        $sql = "select * from grupo where clasificacion_id='$ref' order by nombre_grupo";
        
        $res = $this->_db->query($sql);
        if ($res)
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
    public function cargarGrupoClasificacion($grupo,$clasificacion)
    {
        $sql = "select * from grupo where clasificacion_id = '$clasificacion' and nombre_grupo = '$grupo' ";
        //die($sql);
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
    
    // cargar datos de rubros en un deposito 
    public function cargarRubroDeposito($deposito)
    {
        $sql = "select * from rubro where estatus_rubro = '1' "
                . "and id_rubro in (select rubro_id from producto as pro,stock"
                . " where pro.id_producto = stock.producto_id and stock.deposito_id = '$deposito' )";
        //die($sql);
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
        $sql = "insert into grupo("
            . "nombre_grupo,"
            . "estatus_grupo,"
            . "comentario_grupo,"
            . "fecha_creacion,"
			. "clasificacion_id)values("
            . "'".strtoupper($datos['descripcion'])."',"
            . "'1',"
            . "'".strtoupper($datos['comentario'])."',"
            . "now(),'".$datos['clasificacion']."')";
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
        $sql = "update grupo set "
            . "nombre_grupo = '".$datos['descripcion']."', "
            . "comentario_grupo = '".$datos['comentario']."', "
			. "clasificacion_id = '".$datos['clasificacion']."' "
            . " where id_grupo = ".$datos['id'];
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
        $sql = "update rubro set estatus_rubro = '9' where id_rubro = '$id'";
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
        $sql = "select count(*)as total from grupo where nombre_grupo = '$ref'";
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
        $sql = " select * from grupo where id_grupo='$id' ";
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
	
	public function autoGrupo($ref)
	{
		$val = array();
		$datos = $this->cargarGrupo($ref);
		foreach ($datos as $valor)
		{
			$val[] = array(
				"label"=>  ucfirst($valor['nombre_grupo']),
				"value"=>array("nombre"=>  ucfirst($valor['nombre_grupo']),
								"id"=>$valor['id_grupo']));
		}
		
		return $val;
	}
    
}//FIN DE LA CLASE OBJETO DEL MODELO