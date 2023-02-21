 <?php

	//-------------------------------------------------------------------------------------------------------------
	//CONFIGURACION GENERAL
	//-------------------------------------------------------------------------------------------------------------
        $ruta_general = CONF_PATH . 'general.ini';       
        $general =  parse_ini_file($ruta_general,TRUE);
		
        //define la url base
        define('BASE_URL',$general['general']['urlBase']);

        //define la ruta de imagenes
        define('BASE_IMG',BASE_URL.'application/public/img/');

        //definimos  el controlador por defecto
        define('DEFAULT_CONTROLLER', 'index');

        // define la plantilla por defecto 
        define('DEFAULT_LAYOUT',$general['general']['template']);

        //define tiempo de vida de session
        define('SESSION_TIME',$general['general']['sessionTime']);

        define('DEFAULT_ERROR', $general['general']['nivelError']);

        //define llave para incriptacion
        define('HASH_KEY','525f7321c0e5a');

        //define uso horario
        date_default_timezone_set('America/Caracas');

        //definimos tiempo de vida de una clave en dias
        define('TIME_KEY',$general['general']['keyTime']);

        //define eslogan de la aplicacion
        define('APP_SLOGAN',$general['general']['slogan']);

        //define el nombre de la aplicacion
        define('APP_NAME',$general['general']['name']);

        //define el nombre de la empresa
        define('APP_COMPANY',$general['general']['company']);

        //define los modulos del sistema
        define('APP_MODULE',$general['general']['modules']);
	//--------------------------------------------------------------------------------------------------------------
	
                
                
	//-------------------------------------------------------------------------------------------------------------
	//CONFIGURACION CONEXION 
	//-------------------------------------------------------------------------------------------------------------
        $ruta_conexion = CONF_PATH . 'conexion.ini';       
        $cnx =  parse_ini_file($ruta_conexion,TRUE);
                
        define('DB_HOST',$cnx['conexion']['host']);
        define('DB_USER',$cnx['conexion']['user']);
        define('DB_PASS',$cnx['conexion']['pass']);
        define('DB_NAME',$cnx['conexion']['name']);
        define('DB_CHAR',$cnx['conexion']['char']);
	
	//--------------------------------------------------------------------------------------------------------------

?>
