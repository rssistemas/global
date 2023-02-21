<?php
class conceptoModel extends model
{
	public function __construct()
	{
		parent::__construct();
	
	}
	//------------------------------------------------------------------------------------------
	//METODO QUE CARGA CONCEPTOS DE LA TABLA CONCEPTO_PLN
	//------------------------------------------------------------------------------------------
	public function cargarConcepto()
	{
		
		$sql="select * from concepto_pln ";
		
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
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE INSERTA UN NUEVO REGISTRO EN LA TABLA CONCEPTO_PLN
	//------------------------------------------------------------------------------------------------
	public function insertar($datos)
	{
		
		$sql = "insert into concepto_pln("
				."fecha_creacion,"
				."usuario_creador,"
				."descripcion_concepto,"
				."estatus_concepto_pln,"
				."comentario_concepto_pln)values("
				."now(),"
				."'".$datos['usuario']."',"
				."'".strtoupper($datos['descripcion'])."',"
				."'1',"
				."'".strtoupper($datos['comentario'])."')";
		
		$res = $this->_db->exec($sql);
		if($res)
		{
			return TRUE;	
		}
		else
			return false;
		
		
	}
	
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE MODIFICA REGISTRO EN LA TABLA CONCEPTO_PLN
	//------------------------------------------------------------------------------------------------
	public function modificar($datos)
	{
		
		$sql = "update concepto_pln set "
				."descripcion_concepto = '".strtoupper($datos['descripcion'])."',
				comentario_concepto_pln = '".strtoupper($datos['comentario'])."'
				where id_concepto = '".$datos['id']."'";		
		$res = $this->_db->exec($sql);
		if($res)
		{
			return TRUE;	
		}
		else
			return false;
		
		
	}
	
	
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE CUENTA TOTAL DE CONCEPTO CON EL MISMO NOMBRE
	//------------------------------------------------------------------------------------------------
	public function verificarConcepto($descripcion)
    {
        $sql = "select count(*)as total from concepto_pln  where descripcion_concepto = '$descripcion'";
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
	
	//-------------------------------------------------------------------------------------------------
	//METODO QUE DESACTIVA UN CONCEPTO
	//-------------------------------------------------------------------------------------------------
	public function desactivar($id)
	{
		$sql ="update concepto_pln set estatus_concepto_pln = '9' where id_concepto_pln='$id'";

		$res = $this->_db->exec($sql);
		if($res)
		{
			return TRUE;	
		}
		else
			return false;	
		
	}
	
	//------------------------------------------------------------------------------------------
	//METODO QUE BUSCA CONCEPTOS DE LA TABLA CONCEPTO_PLN POR EL NUMERO DE ID
	//------------------------------------------------------------------------------------------
	public function buscarConcepto($id)
	{
		
		$sql="select * from concepto_pln where id_concepto_pln = '$id'";
		
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
	

}

?>