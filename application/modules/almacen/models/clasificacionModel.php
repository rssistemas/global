<?php
class clasificacionModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    //----------------------------------------------------------------------------------------------
	// METODO LISTAR: CARGA LOS DATOS DE CLASIFICACION POR COMPARACION O
	// TODOS DEPENDIENDO DEL PARAMETRO (2 = producto,5 servicios)
	//----------------------------------------------------------------------------------------------
    public function listar($tipo = 0)
    {
        if($tipo == 0)
			$sql = "select * from clasificacion  order by nombre_clasificacion";
		else
			$sql = "select * from clasificacion where tipo_clasificacion_id ='$tipo' order by nombre_clasificacion";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
        
    }
    
    public function cargarRubro()
    {
        $sql = "select * from rubro where estatus_rubro = '1'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
        
    }
	//-----------------------------------------------------------------------------------------
	//METODO QUE INCLUYE NUEVO REGISTRO E CLASIFICACION
	//-----------------------------------------------------------------------------------------
    public function incluir($datos)
    {
        $sql = "insert into clasificacion("
                . "nombre_clasificacion,"
                . "estatus_clasificacion,"
                . "comentario_clasificacion,"
                . "fecha_crea_clasificacion,"
				. "tipo_clasificacion_id)values("
                . "'".strtoupper($datos['descripcion'])."',"
                . "'1',"
                . "'".strtoupper($datos['comentario'])."',"
                . "now(),"
				. "'".$datos['tipo']."')";
				
            $res = $this->_db->exec($sql);
            if(!$res)
            {
                return FALSE;               
            }  else {
                return TRUE;
            }
        
    }
    public function modificar($datos)
    {
        $sql = "update rubro set "
                . "descripcion_rubro = '".$datos['descripcion']."', "
                . "comentario_rubro = '".$datos['comentario']."'"
                . " where id_rubro = ".$datos['id'];
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
	public function desactivar($id)
    {
        $sql = "update rubro set estatus_rubro = '9' where id_rubro = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
	//------------------------------------------------------------------------------------------------
	// METODO QUE REALIZA BUSQUEDA 
	//------------------------------------------------------------------------------------------------   
	//para consultar un registro por el id
    public function buscar($item = false)
    {
		if($item)
		{
			if(is_int($item))
			{
				$sql = " select * from clasificacion where id_clasificacion='$item' ";
				$res = $this->_db->query($sql);
				if($res)
				{
					$res->setFetchMode(PDO::FETCH_ASSOC);
					return $res->fetch();
				}	
				
			}else
			{
				$sql = " select * from clasificacion where nombre_clasificacion like '%".strtoupper($item)."%' order by nombre_clasificacion";
				$res = $this->_db->query($sql);
				if($res)
				{
					$res->setFetchMode(PDO::FETCH_ASSOC);
					return $res->fetchAll();
				}
			}
		}	
        
        else
        {
            return array();
        }
    }
	public function buscarClasificacion($descripcion)
    {
        $sql = "select * from clasificacion as cla where cla.nombre_clasificacion = '$descripcion' ";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return FALSE;
        
    }
	
	
	
	public function autoClasificacion($ref)
	{
		$val = array();
		$datos = $this->buscar($ref);
		foreach ($datos as $valor)
		{
			$val[] = array(
				"label"=>  ucfirst($valor['nombre_clasificacion']),
				"value"=>array("nombre"=>  ucfirst($valor['nombre_clasificacion']),
								"id"=>$valor['id_clasificacion']));
		}
		
		return $val;
	}	
            
}