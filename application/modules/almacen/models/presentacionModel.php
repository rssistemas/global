<?php
class presentacionModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
    public function cargarPresentacion($ref=false)
    { 
        if(!$ref)
        {
            $sql = "select pre.*,uni.simbolo_uni_med as medida from presentacion as pre,uni_med as uni"
                . " where pre.uni_med_id = uni.id_uni_med order by pre.nombre_presentacion";
        }
        else
        {      
            $sql = "select pre.*,uni.simbolo_uni_med as medida from presentacion as pre,uni_med as uni"
                . " where pre.uni_med_id = uni.id_uni_med and pre.nombre_presentacion like '%$ref%' order by pre.nombre_presentacion";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    } 
    //para incluir un nuevo registro
    public function incluir($datos)
    {
        $sql = "insert into presentacion("
        . " nombre_presentacion,"
        . " cantidad_presentacion,"
        . " fecha_creacion,"
        . " estatus_presentacion, "
        . " uni_med_id,  cantidad_uni_med)values("
        . "'".strtoupper($datos['descripcion'])."',"
        . "'".$datos['unidades']."',"
        . "now(),"
        . "'1',"
        . "'".$datos['medida']."',"
		. "'".$datos['cantidad']."')";
		
		//die($sql);
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
    public function modificar($datos)
    {
        $sql = "update presentacion set "
                . "nombre_presentacion = '".$datos['descripcion']."' , "
                . "uni_med_id = ".$datos['medida']." "
                . " where id_presentacion = ".$datos['id'];
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
    
	
	public function buscarPresentacion($descripcion)
    {
        $sql = "select * from presentacion as pre where pre.nombre_presentacion = '$descripcion'";
       // die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return array();
    }
    
	
	public function buscar($id)
    {
        $sql = "select * from presentacion where id_presentacion='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
        
    }
    
	public function buscarPresentacionProducto($producto)
    {
        $sql = "select * from presentacion as pre where id_presentacion  in
		(select presentacion_id from det_presentacion where det_producto_id= '$producto') order by nombre_presentacion";
        //die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
            return array();
    }
	
	
	
	
	public function comprobarPresentacion($datos)
    {
        $sql ="select pre.id_presentacion from presentacion as pre where pre.nombre_presentacion = ".$datos['nombre_presentacion'];
        $res = $this->_db->query($sql);
        if($res->rowCount()>0)
            return TRUE;
        else
            return FALSE;
    }
    public function desactivar($id)
    {
        $sql = "update presentacion set estatus_presentacion = '9' where id_presentacion = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
	 //para verificar que no se repita un mismo registro
    public function verificar_existencia($ref,$unidad,$cant_uni,$cant_pre)
    {
        $sql = "select count(*)as total from presentacion"
        . " where nombre_presentacion = '$ref' and uni_med_id= '$unidad'"
        . " and cantidad_presentacion= '$cant_pre' and cantidad_uni_med='$cant_uni' ";
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

	public function autoPresentacion($ref)
	{
		$val = array();
		$datos = $this->cargarPresentacion($ref);
		foreach ($datos as $valor)
		{
			$val[] = array(
				"label"=>  ucfirst($valor['nombre_presentacion']),
				"value"=>array("nombre"=>  ucfirst($valor['nombre_presentacion']),
								"id"=>$valor['id_presentacion']));
		}
		
		return $val;
	}	
	
}
