<?php
class trabajadorModel extends model{
    private $_ult_usuario;

    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarTrabajador_index($ref=FALSE)
    {
        if($ref)
        {
            $sql="select tra.*, car.nombre_cargo as cb_cargo, per.*, dep.descripcion_departamento as cb_depar"
            . " from trabajador as tra ,cargo as car, persona as per, departamento as dep "
            . " where car.id_cargo=tra.cargo_id and per.id_persona=tra.persona_id "
            . " and dep.id_departamento=car.departamento_id and per.pri_nombre_persona like '%$ref%' ";
        }
        else
        {
            $sql="select tra.*, car.nombre_cargo as cb_cargo, per.*, dep.descripcion_departamento as cb_depar"
            . " from trabajador as tra ,cargo as car, persona as per, departamento as dep "
            . " where car.id_cargo=tra.cargo_id and per.id_persona=tra.persona_id "
            . " and dep.id_departamento=car.departamento_id";
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

    //para incluir un nuevo registro
    public function incluir($datos)
    {
        $this->iniciar();
        $sql = "insert into trabajador(
            correo_trabajador,
            fecha_creacion,
            estatus_trabajador,
            persona_id,
            cargo_id,
            licencia,
            grado_licencia)values("
            ."'".$datos['correo']."',"
            ."now(),"
            ."'1',"
            ."'".$datos['persona']."',"
            ."'".$datos['cargo']."',"
            ."'".$datos['licencia']."',"
            ."'".$datos['grado_licencia']."'"
            .")";
        $res = $this->_db->exec($sql);
		if($res)
		{
				$this->_ultimo_registro = $this->_db->lastInsertId();
				
				$sql = "insert into relacion_deposito("
						. "trabajador_id,"
						. "deposito_id,"
						. "estatus_relacion"
						. ")values("
						. "'".$this->_ultimo_registro."',"
						. "'".$datos['ubicacion']."','1')";
				$res = $this->_db->exec($sql);
				if($res)
				{
					$this->confirmar();
					return TRUE;
				}else
				{
					$this->cancelar();
					return false;
				}            
		}
        else
        {
            $this->cancelar();
            return false;
		}
    }

    //para modificar un registro
    public function modificar($datos)
    {
        $sql = "update trabajador set "
            . "correo_trabajador= '".$datos['correo']."', "
            . "cargo_id= ".$datos['cargo']." "
            . " where persona_id = ".$datos['persona'];
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
        $sql="update trabajador set estatus_trabajador='9' where id_trabajador='$id'";
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
    
    //para consultar un registro por el id
    public function buscar($id)
    {
        $sql = "select * from trabajador as tra, persona as per, "
        ." cargo as car, departamento as dep, "
        ." sector as sec, parroquia as parr, municipio as mun, estado as est"
        ." where per.id_persona = tra.persona_id and tra.id_trabajador='".$id."'"
        . " and car.id_cargo = tra.cargo_id and car.departamento_id = dep.id_departamento "
        . " and sec.id_sector = per.sector_id and parr.id_parroquia = sec.parroquia_id "
        . " and mun.id_municipio = parr.municipio_id and est.id_estado = mun.estado_id";
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
    
    public function ubicacionTrabajador($trabajador)
    {
        
        
    }
	
	public function trabajadorCargo($cargo)
	{
		$sql = "select trab.*,per.* from trabajador as trab,cargo,persona as per where cargo.nombre_cargo='$cargo' 
		and trab.cargo_id = cargo.id_cargo and per.id_persona = trab.persona_id ";
		
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
     
	//---------------------------------------------------------------------------------------------
	//METODO QUE CARGA LISTADO DE TRABAJADORES DE UN DEPARTAMENTO 
	//---------------------------------------------------------------------------------------------
	public function trabajadorDpto($dpto)
	{
		$sql = "select trb.*,per.*,dep.descripcion_departamento from trabajador as trb,persona as per,departamento as dep 
		where trb.departamento_id = '$dpto' and dep.id_departamento = trb.departamento_id  
		and per.id_persona = trb.persona_id  order by id_trabajador";
		
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
    
}//FIN DE LA CLASE OBJETO DEL MODELO
?>