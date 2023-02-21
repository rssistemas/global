<?php
class marcaModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
    public function cargarMarca($ref=false)
    { 
        if($ref)
        {
            $sql = "select * from marca where nombre_marca like '%$ref%' order by nombre_marca";
        }
        else
        {
            $sql = "select * from marca order by nombre_marca";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    } 
    public function insertar($datos)
    {
        if($datos)
        {
            $sql = "insert into marca(nombre_marca, fecha_creacion, estatus_marca)
             values('".strtoupper($datos['descripcion'])."',	now(), 1)";
            $res = $this->_db->exec($sql);
            if($res)
            {
                $this->_ultimo_registro = $this->_db->lastInsertId();	
                return TRUE;	
            }
            else
                return false;
        }
    }
    public function modificar($datos)
    {
        if($datos)
        {
            $sql = "update marca set "
                    . "nombre_marca = '".strtoupper($datos['descripcion'])."'"
                    . " where id_marca = '".$datos['id']."'";
            $res = $this->_db->exec($sql);
            if($res)
            {
                return TRUE;	
            }
            else
                return false;
            
        }    
    }        
    public function buscar($id)
    {
        $sql = "select * from marca as mar where mar.id_marca = '".$id."'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
        
    }
            
    public function buscarMarca($descripcion)
    {
        $sql = "select * from marca as mar where mar.nombre_marca = '$descripcion' ";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return FALSE;
        
    }
    public function comprobarMarca($datos)
    {
        $sql ="select mar.id_marca from marca as mar where mar.nombre_marca = ".$datos['nombre_marca'];
        $res = $this->_db->query($sql);
        if($res->rowCount()>0)
            return TRUE;
        else
            return FALSE;
    }
	
	public function verificar_existencia($ref)
    {
        $sql = "select count(*)as total from marca where nombre_marca = '$ref'";
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
    public function desactivar($id)
    {
        $sql = "update marca set estatus_marca = '9' where id_marca = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
    
    public function buscarDescMarca($ref=false)
    { 
		$val=array();
        if($ref)
        {
            $sql = "select * from marca where nombre_marca like '%$ref%' order by nombre_marca";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $res->fetchAll();
            if(count($datos)>0)
			{	
				foreach ($datos as $valor)
				{
					$val[] = array(
						"label"=>$valor['nombre_marca'],
						"value"=>array("nombre"=>$valor['nombre_marca'],
										"id"=>$valor['id_marca']));
				}
			}
            return $val;
        }else
            return array();
    }
}
