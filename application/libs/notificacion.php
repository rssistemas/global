<?php

class Notificacion
{
	private $_registry;
	private $_alerta;
    private $_mensaje;
	private $_db;
	
	
	public function __construct()
	{
		$this->_alerta=array();
				
		$this->registry = registry::getInstancia();
		$this->_db = $this->registry->_db;	
	}
	

        //METODO QUE CARGA LAS ALERTAS EMITIDAS POR EL SISTEMA 
	public function getAlertaSistema( $id =false)
	{
            if($id)
            {
               $sql="select * from notificacion where id_notificacion = '$id'"; 
            }else
            {
                $sql="select * from notificacion where estatus_notificacion = '1' and condicion_notificacion='EN COLA' and destino_notificacion = '999999' order by fecha_creacion";
            }
		
            //die($sql);
            $datos=$this->_db->query($sql);

            $this->_alerta = $datos->fetchall();
            return  $this->_alerta;

	}
        ///METOLDO QUE CARGA LAS ALERTAS DE LOS USUARIO DE DESTINO
	public function getAlertaUsuarioDes($id = false)
	{
            $usuario = session::get('id_usuario');
            if($id)
            {
                $sql="select * from notificacion where  id_notificacion='$id'";
            }else
            {
                $sql="select * from notificacion where estatus_notificacion = '1' and condicion_notificacion='EN COLA'"
                        . " and destino_notificacion='$usuario' order by fecha_creacion";
            }

            //die($sql);
            $datos=$this->_db->query($sql);
            if($datos)
            {    
                $this->_alerta = $datos->fetchall();
                return  $this->_alerta;
            }else
            {
                return array();
            }
	}
	//METODO QUE ENGLOBA TODAS LAS ALERTAS
	public function getAlerta()
	{
                $arr1=$this->getAlertaSistema();
                $arr2= $this->getAlertaUsuarioDes();
		return array_merge($arr1,$arr2);
	}

    public function getMensajeDpto()
    {
        $sql = "select *,dep.nombre_deposito as origen,dpto.descripcion_departamento as destino,usu.correo_usuario,
		per.pri_nombre_persona as nombre,per.pri_apellido_persona as apellido from incidencia,deposito as dep,departamento as dpto,
		usuario as usu,persona as per where lugar_incidencia = dep.id_deposito and dpto_destino_incidencia = dpto.id_departamento
		and usuario_ori_incidencia = usu.id_usuario and per.id_persona = usu.persona_id ";
         $datos=$this->_db->query($sql);
        if($datos)
        {    
            $this->_mensaje = $datos->fetchAll();
            return  $this->_mensaje;
        }else
        {
            return array();
        }



    }
	
	public function cargarIncidenciaUsuario()
	{
		$trabajador = session::get('trabajador');
		
		$sql = "select *,dep.nombre_deposito as origen,dpto.descripcion_departamento as destino
		from incidencia,deposito as dep,departamento as dpto,relacion_deposito as rd
		where lugar_incidencia = dep.id_deposito and rd.deposito_id =  lugar_incidencia and rd.trabajador_id='$trabajador' 
		and dpto_destino_incidencia = dpto.id_departamento and dpto.id_departamento in 
		(select departamento_id  from cargo,trabajador where cargo.id_cargo = trabajador.cargo_id and trabajador.id_trabajador = '$trabajador' )";

		//die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();

	}
	
	
}


?>