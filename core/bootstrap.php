<?php
        //-----------------------------------------------------------------------
	//framework basiquitoPHP
	//clase bootstrap; procesa las llamadas a los controladores y a los metodos 
        //autor:Rafael Pérez 
	//------------------------------------------------------------------------
	class bootstrap{
		
		public static function start( request $peticion)
		{
			$modulo = $peticion->getModulo();
					
			$controller = $peticion->getControlador(). 'Controller';
			
			$rutaControlador = APP_PATH . 'controllers'. DS . $controller . '.php';
			
			$metodo = $peticion->getMetodo();
			
			$argumento=$peticion->getArgumento();
			
			if($modulo)
			{
				$rutaModulo = APP_PATH . 'controllers' . DS . $modulo . 'Controller.php'; 
			
				if(is_readable($rutaModulo))
				{
					require_once $rutaModulo;
					$rutaControlador = APP_PATH . 'modules' . DS . $modulo . DS . 'controllers' . DS . $controller.'.php'; 
				
				}else
					{
						throw new error("Error de base de Modulo, La ruta del modulo no existe");
					}
				
			}else
				{
					$rutaControlador = APP_PATH . 'controllers'. DS . $controller . '.php';
				}
			
			
			// si el archivo existe y es leible
			if(is_readable($rutaControlador))
			{
                            
				// importamos el archivo								
				require_once $rutaControlador;
				// instanciamos la clase
				$controller = new $controller; 
				
				//se cargan en arraglo los metodos declarados en la clase instaciada
				$métodos_clase = get_class_methods($controller);
				//se busca si existen los metodos 'initialize y finalize' para marcar las banderas
				foreach ($métodos_clase as $nombre_método) {
					if($nombre_método=='initialize')
					{
						//$controller->setInitialize(TRUE);
						$controller->initialize();
					}
					
				}
			   //die($metodo);
				// si el metodo del controlador no es ejecutable o no existe establece el metodo en index
				if(!is_callable(array($controller,$metodo)))
				{
					$metodo = 'index';
				}
				// si los argumentos existen
				if(isset($argumento))
				{
                     //die($metodo);
					// se invocan los metodos contenidon en una clase pasandole los argumentos
					call_user_func_array(array($controller,$metodo),$argumento);
				}else
					{
                                            
						// se invoca el metodo contenido en una clase sin argumentos
						call_user_func(array($controller,$metodo));
					}
						//si existe el metodo finalize se llama 
						foreach ($métodos_clase as $nombre_método) {
							if($nombre_método=='finalize')
							{
								//$controller->setInitialize(TRUE);
							   $controller->finalize();
							}
							
						}        
                                
				unset($_GET['url']);	
			  
			}else
			{
				   //die("pase0");
					throw new errorsys('Controlador No encontrado, debe crear controlador : '. $rutaControlador . " ");
			}
				
		}
		
	}

