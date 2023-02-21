<?php
class tipoProveedorModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
    public function cargarTipoProveedor($ref=false)
    { 
        if($ref)
        {
            $sql = "select * from tipo_proveedor where nombre_tipo_proveedor like '%$ref%' order by nombre_tipo_proveedor";
        }
        else
        {
        $sql = "select * from tipo_proveedor order by nombre_tipo_proveedor";
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
			$sql = "insert into tipo_proveedor(nombre_tipo_proveedor, fecha_creacion, estatus_tipo_proveedor)
			 values('".ucfirst($datos)."',	now(), 1)";
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
    
    public function buscar($id)
    {
        $sql = "select * from tipo_proveedor where id_tipo_proveedor='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
        
    }
    public function modificar($datos)
    {
        $sql = "update tipo_proveedor set "
                . "nombre_tipo_proveedor = '".$datos['descripcion']."'"
                . " where id_tipo_proveedor = ".$datos['id'];
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }
    public function buscarTipoProveedor($descripcion)
    {
        $sql = "select * from tipo_proveedor as tip where tip.nombre_tipo_proveedor = '$descripcion'";
       // die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
			 $res->setFetchMode(PDO::FETCH_ASSOC);
			 return $res->fetch();
        }else
            return FALSE;
        
    }
    public function comprobarTipoProveedor($datos)
    {
        $sql ="select tip.id_tipo_proveedor from tipo_proveedor as tip where tip.nombre_tipo_proveedor = ".$datos['nombre_tipo_proveedor'];
        $res = $this->_db->query($sql);
        if($res->rowCount()>0)
            return TRUE;
        else
            return FALSE;
    }
    
    public function desactivar($id)
    {
        $sql = "update tipo_proveedor set estatus_tipo_proveedor = '9' where id_tipo_proveedor = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    } 
    
}
