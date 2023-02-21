<?php
	function autoLoadCore($class)
	{
		
		if(file_exists(CORE_PATH . strtolower($class) . '.php'))
		{
			include_once CORE_PATH . strtolower($class) . '.php';	
		}
			
	}
	
	function autoLoadLibs($class)
	{
		
		if(file_exists(APP_PATH .'libs'.DS. strtolower($class) . '.php'))
		{
			include_once APP_PATH .'libs'.DS. strtolower($class) . '.php';	
		}	
	}
	spl_autoload_register('autoLoadCore');

	spl_autoload_register('autoLoadLibs');
?>