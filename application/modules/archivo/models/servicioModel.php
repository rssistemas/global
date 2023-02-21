<?php
class servicioModel extends model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	
	
	
	public function cargarServicio($ref = false)
	{
		if($ref)
		{
			$sql = "select ser.*,gr.nombre_grupo,cl.nombre_clasificacion from servicio as ser,grupo as gr,clasificacion as cl where 
				cl.id_clasificacion = ser.clasificacion_id and gr.id_grupo = ser.grupo_id and 
				nombre_servicio like '%".$ref."%' order by id_servicio";
			
		}else{	
			$sql = "select ser.*,gr.nombre_grupo,cl.nombre_clasificacion from servicio as ser,grupo as gr,clasificacion as cl where 
				cl.id_clasificacion = ser.clasificacion_id and gr.id_grupo = ser.grupo_id order by id_servicio";
		}
		
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
	
	//------------------------------------------------------------------------
	//METODO QUE INSERTA REGISTRO EN TABLA SERVICIO
	//------------------------------------------------------------------------	
	public function insertar($datos)
	{
		$sql ="insert into servicio("
			. "fecha_creacion,"
			. "usuario_creador,"
			. "nombre_servicio,"
			. "estatus_servicio,"
			. "condicion_servicio,"
			. "comentario_servicio,"
			. "clasificacion_id,"
			. "grupo_id)values("
			. "now(),"
			. "'".$datos['usuario']."',"
			. "'".$datos['nombre']."',"
			. "'1',"
			. "'ACTIVO',"
			. "'".$datos['comentario']."',"
			. "'".$datos['clasificacion']."',"
			. "'".$datos['grupo']."'"
			. ")";
		
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
	
	
	//------------------------------------------------------------------------
	//METODO QUE MODIFICA REGISTRO EN TABLA SERVICIO
	//------------------------------------------------------------------------	
	public function modificar($datos)
	{
		$sql ="update servicio set "
			. "nombre_servicio = '".$datos['nombre']."',"
			. "comentario_servicio = '".$datos['comentario']."',"
			. "clasificacion_id = '".$datos['clasificacion']."',"
			. "grupo_id = '".$datos['grupo']."' where id_servicio = '".$datos['id']."'";
		
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
	
	//----------------------------------------------------------------------------
	//mMETODO QUE RETORNA DATOS DE LA TABLA SERVICIO PARA CONTROL AUTOCOMPLETE
	//----------------------------------------------------------------------------
	public function autoServicio($valor)
	{
		$val= array();
		$datos = $this->cargarServicio(strtoupper($valor));
		   
		if(count($datos)>0)
		{		
			foreach ($datos as $valor)
			{
				$val[] = array(
					"label"=>$valor['nombre_servicio'],
					"value"=>array("nombre"=>$valor['nombre_servicio'],
								   "codigo"=>$valor['id_servicio']
									));
			}
			
			
			return $val;
		}else
			return array();
		
	}
	
	//-----------------------------------------------------------------------------------------
	//METODO QUE BUSCA UN SERVICIO
	//-----------------------------------------------------------------------------------------
	public function buscar($ref = false)
	{
		
		$sql = "select ser.*,gr.nombre_grupo,cl.nombre_clasificacion from servicio as ser,grupo as gr,clasificacion as cl where 
		cl.id_clasificacion = ser.clasificacion_id and gr.id_grupo = ser.grupo_id and 
		id_servicio = '$ref' order by id_servicio";
			
		//die($sql);		
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
			return false;
        
	}
	
	//-----------------------------------------------------------------------------------------
	//METODO QUE BUSCA UN SERVICIO por id para planificacion de compra
	//-----------------------------------------------------------------------------------------
	public function buscarId($ref = false)
	{
		
		$sql = "select ser.id_servicio as id,ser.nombre_servicio as requisito from servicio as ser where 
		id_servicio = '$ref' ";
			
		//die($sql);		
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
			return false;
        
	}
	
	
}


?>