<?php

class registry
{
	private static $_instancia;
	private $_data;
	
	// no se puede instanciar(para que solo se instancie dentro de la clase)
	private function __construct()
	{
		
		
	}
	
	// patron singleton
	public static function getInstancia()
	{
		if(!self::$_instancia instanceof self)
		{
			self::$_instancia = new registry();
		}
		
		return self::$_instancia;
	}
	
	// sobre escribimos el metodo magico __set
	public function __set($name,$value)
	{
		$this->_data[$name] = $value; 
	}
	
	// sobre escribimos el metodo magico __get
	public function __get($name)
	{
		if(isset($this->_data[$name]))
		{
			return $this->_data[$name];
		}else
			return FALSE;
	}
	
	
	
}


?>