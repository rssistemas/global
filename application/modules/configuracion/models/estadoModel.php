<?php
class estadoModel  extends model
{
    public function __construct() {
        parent::__construct();
    }
   
    public function cargarEstado($ref=false)
    { 
        if($ref)
        {
            $sql = "select * from estado where estatus_estado!='9' and descripcion_estado like '%$ref%' order by descripcion_estado";
        }
        else
        {
            $sql = "select * from estado where estatus_estado!='9' order by descripcion_estado";
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
            $sql = "insert into estado(descripcion_estado,fecha_creacion, estatus_estado)
             values('".ucfirst($datos)."', now(), 1)";
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
            $sql = "update estado set "
                    . "descripcion_estado = '".$datos['descripcion']."'"
                    . " where id_estado = '".$datos['id']."'";
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
        $sql = "select * from estado as est where est.id_estado = '".$id."'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return array();
    }

    public function buscarEstado($descripcion)
    {
        $sql = "select * from estado as est where est.descripcion_estado = '$descripcion'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return FALSE;
        
    }
	
	public function verificarEstado($descripcion)
    {
        $sql = "select count(*)as total from estado as est where est.descripcion_estado = '$descripcion'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
			$dat = $res->fetch();
			if($dat['total']>0)
            	return $dat;
			else {
				return array('total'=>0);
			} 
        }else
            return FALSE;
        
    }
    public function comprobarEstado($datos)
    {
        $sql ="select est.id_estado from estado as est where est.descripcion_estado = ".$datos['descripcion'];
        $res = $this->_db->query($sql);
        if($res->rowCount()>0)
            return TRUE;
        else
            return FALSE;
    }
    public function desactivar($id)
    {
        $sql = "update estado set estatus_estado = '9' where id_estado = '$id'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
            return false;
    }        
}
