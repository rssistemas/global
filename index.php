<?php
        ini_set('display_errors', 1);
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        

        // define el separador de directorio
        define('DS', DIRECTORY_SEPARATOR);

        //define la ruta base
        define('ROOT',realpath(dirname(__FILE__)).DS);

        //define la ruta de la aplicaion
        define('APP_PATH',ROOT . 'application' . DS);
        //define la ruta del core
        define('CORE_PATH',ROOT . 'core' . DS);

        //define la ruta de las librerias externas
        define('LIB_PATH',APP_PATH. 'libs' . DS);

        //define la ruta de los log
        define('LOG_PATH',APP_PATH . 'log' . DS);

         //define la ruta de configuracion
        define('CONF_PATH',APP_PATH . 'config' . DS);
       
        
        try{          
            require_once CORE_PATH . 'config.php' ;	  		
            require_once CORE_PATH . 'autoload.php' ;
            require_once CORE_PATH . 'Hash.php';
            //require_once LIB_PATH  . 'rb.php';    
            session::init();

            $registry = registry::getInstancia();
            $registry ->_request = new request();
            
            $registry ->_db = new database();
            
            $registry ->_acl = new acl();
            
            bootstrap ::start($registry->_request);
          
            }catch(errorsys $e)
                {
                    echo $e->errorMessage();
                }
		
			 
	 
	 
?>
