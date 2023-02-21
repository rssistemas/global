<?php
class bancoModel extends model
{
	
	public function __construct()
	{
		parent::__construct();
	
	} 
	
	public function cargarBancos($banco = false)
	{
		if($banco)
		{
			$sql = "select * from banco where nombre_banco like '%$banco%' order by id_banco";
			
		}else
			{
				$sql = "select * from banco order by nombre_banco";
			}
		
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
		
		
	}
	
	public function insertarBanco($datos)
	{
		
		$sql ="insert into banco("
			."nombre_banco,"
			."direccion_banco,"
			."telefono_banco,"
			."correo_banco,"
			."estatus_banco"		
			.")values("
			."'".$datos['nombre']."',"
			."'".$datos['direccion']."',"
			."'".$datos['telefono']."',"
			."'".$datos['correo']."',"
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
	
	
	public function contarBanco($valor)
	{
		$sql = "select count(*) as total  from banco where nombre_banco ='$valor' ";
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
			
		
	}
	
}
