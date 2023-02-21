<?php
class alerta{
	
	private $_mensajes;
	private $_logError;	
	public function __construct()
	{
		$ruta_mensajes = CONF_PATH . 'mensajes.ini';       
        if(is_readable($ruta_mensajes))
		{
			$this->_mensajes = $mensajes =  parse_ini_file($ruta_mensajes,TRUE);
				
		}else
		{
			$this->_mensajes = array(
				"alerta"=>array("100"=>"Llene todos los campos del formulario  ",
					"101"=>"Caracter no permitido   ",
					"102"=>"Accion no permitida ",
					"103"=>"Sin permiso de recurso ",
					"104"=>"Su sesion fue cerrada por seguridad ",
					"105"=>"Acceso denegado  "),
				"error"=>array("200"=>"Error de conexion intente luego",
					"201"=>"Error interno ",
					"202"=>"Modulo no encontrado ",
					"203"=>"Recurso no encontrado "	),
				"default"=> array("500"=>"Error interno .....")					
			);
							
		}
				
	}
	
	//--------------------------------------------------------------------
	// Escribe el error generado en el log de errores
	//--------------------------------------------------------------------
	public function setLogError($mensaje)
	{
		$this->getLog();
		
		$mensaje = $mensaje . date("F j, Y, g:i a");
		fwrite($this->_logError,$mensaje);
			
		fclose($this->_logError);						
	}
	//----------------------------------------------------------------------
	//carga el log de error si existe sino lo crea
	//------------------------------------------------------------------------
	public function getLog()
	{
		$rutaLogError = LOG_PATH ;
		if(is_readable($rutaLogError . "logError.txt"))
		{
			$this->_logError = fopen($rutaLogError ."logError.txt","a+");						
		}			
	}
	
	//----------------------------------------------------------------------
	//Envia mensaje de error
	//----------------------------------------------------------------------
	public function getMensajes($tipo='default',$codigo=201)
	{
				
			if(is_int($codigo))
			{
				return $this->_mensajes[$tipo][$codigo];
			}else
			{			
				return $this->_mensajes['default']['500'];
			}
		
	}
	

}
?>