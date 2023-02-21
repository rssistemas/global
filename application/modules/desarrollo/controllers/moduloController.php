<?php
class moduloController extends desarrolloController
{
    private $_modulo;
    private $_recurso;
    private $_organizacion;
    
    public function __construct() {
        parent::__construct();
        $this->_modulo = $this->loadModel('modulo');
        $this->_recurso= $this->loadModel('recurso');
        $this->_organizacion = $this->loadModel('organizacion','archivo');
    }
    public function index($pagina = 1)
    {
        $this->_view->title = "Módulo";
        $this->_view->setJs(array('modulo'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_modulo->cargarModulo($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista =  $paginador->paginar($this->_modulo->cargarModulo(),$pagina);
        } 
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','desarrollo/modulo/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','configuracion');
        exit();
    }
    
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            $nombre = $this->getPostParam('nombre');
            $contenedor = $this->getPostParam('contenedor');
            $datos = array(
                "nombre" =>$this->getPostParam('nombre'),
		"contenedor" =>$this->getPostParam('nombre'),
                "descripcion"=>$this->getPostParam('descripcion'),
                "url"=>  $this->getPostParam('nombre'). '/index' ,
                "icono"=>$this->getPostParam('icono'),
                "posicion"=>$this->getPostParam('posicion'),
                "clave"=>$this->getPostParam('clave'),
                "condicion"=>$this->getPostParam('condicion'),
                "organizacion"=>$this->getInt('org'),
                "usuario"=>session::get('id_usuario')
                );
            if($this->_modulo->insertar($datos))
            {
              			
				$mensaje="CONFIRMACIÓN DE REGITRO - Registro  guardado exitosamente...";
				if(mkdir(APP_PATH.'modules'.DS.$contenedor, 0777, true)) 
				{
					
					mkdir(APP_PATH.'modules'.DS.$contenedor .DS.'controllers', 0777, true);
					mkdir(APP_PATH.'modules'.DS.$contenedor .DS.'models', 0777, true);
					mkdir(APP_PATH.'modules'.DS.$contenedor .DS.'views', 0777, true);
					
					
					if(is_readable(APP_PATH.'controllers'))
					{
						//make controller primary
                                                $ruta_controler = APP_PATH.'controllers';
						if($file = fopen($ruta_controler . DS . $contenedor.'Controller.php',"a+"))
						{
							
							$cadena = "<?php". PHP_EOL;
							$cadena = $cadena ."class ".$contenedor."Controller extends Controller{". PHP_EOL;
							$cadena	= $cadena ."public function __construct() {". PHP_EOL;
							$cadena = $cadena ."parent::__construct();}". PHP_EOL;
							$cadena = $cadena ."public function index() {}". PHP_EOL;
							$cadena = $cadena ."} ". PHP_EOL;
							
                                                        fwrite($file,$cadena);
							fclose($file);						   	
								
						}
                                                //genera la clase controlador
                                                if($file = fopen(APP_PATH.'modules'.DS. $contenedor .DS.'controllers'. DS .$contenedor.'Controller.php',"a+"))
						{
							
							$cadena = "<?php". PHP_EOL;
							$cadena = $cadena ."class ".$contenedor."Controller extends ".$contenedor."Controller{". PHP_EOL;
							$cadena	= $cadena ."public function __construct() {". PHP_EOL;
							$cadena = $cadena ."parent::__construct();}". PHP_EOL;
							$cadena = $cadena ."public function index() {}". PHP_EOL;
							$cadena = $cadena ."} ". PHP_EOL;
							
                                                        fwrite($file,$cadena);
							fclose($file);						   	
								
						}
                                                //genera la clase modelo  
                                                if($file = fopen(APP_PATH.'modules'.DS. $contenedor .DS.'models'. DS .$contenedor.'Model.php',"a+"))
						{
							
							$cadena = "<?php". PHP_EOL;
							$cadena = $cadena ."class ".$contenedor."Model extends model{". PHP_EOL;
							$cadena	= $cadena ."public function __construct() {". PHP_EOL;
							$cadena = $cadena ."parent::__construct();}". PHP_EOL;
							$cadena = $cadena ."public function listar() {}". PHP_EOL;
							$cadena = $cadena ."} ". PHP_EOL;
							
                                                        fwrite($file,$cadena);
							fclose($file);						   	
								
						}
                                                

                                                
					}	
				}
            }
            else
            {
                $this->_modulo->regLog();
                $this->_view->error = "Error Guardando Modulo ....." . $this->_modulo->regLog();
                $mensaje="CONFIRMACIÓN DE REGISTRO - ERROR al guardar el nuevo registro...";
            }
            $this->redireccionar('desarrollo/modulo/index/','desarrollo');
	    exit();
        }  
        else
        {
            $this->_view->title = "Agregar Módulo";
            $this->_view->setJs(array('modulo'));
            $this->_view->setJsPlugin(array('validaciones'));
            
            $this->_view->org = $this->_organizacion->listar();
            
            $this->getLibrary('paginador');
            $paginador = new Paginador();
            $this->_view->paginacion = $paginador->getView('paginacion','desarrollo/modulo/agregar');
            $this->_view->renderizar('agregar','desarrollo');
            exit();
        }
    }//FIN DE LA FUNCION AGREGAR

    public function editar($id = FALSE)
    {
        if($this->getInt('guardar')==2)
        {
            $datos = array(
                "id"=>  $id,
                "nombre" =>$this->getPostParam('nombre'),
                "descripcion"=>$this->getPostParam('descripcion'),
                "url"=>  $this->getPostParam('url'),
                "icono"=>$this->getPostParam('icono'),
                "posicion"=>$this->getPostParam('posicion'),
                "clave"=>$this->getPostParam('clave')  );
            
			if($this->_modulo->modificar($datos))
            {
                $this->_view->error = "Registro editado guardado...";
            }
            else
            {
                $this->_view->error = "Error guardando edicion...".$this->_modulo->regLog();
            }
            $this->redireccionar('desarrollo/modulo/','configuracion');
        }  
        else
        {
            $this->_view->titulo = "Edición del Módulo";
            $this->_view->setJs(array('modulo'));
            $this->_view->setJsPlugin(array('validaciones'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();
            $this->_view->modulo = $this->_modulo->buscar($id);
            $this->_view->paginacion = $paginador->getView('paginacion','desarrollo/modulo/editar');
            $this->_view->renderizar('editar','desarrollo');
        }
    }//FIN DE LA FUNCION EDITAR        
        
    
    public function edicion($pagina = 1)
    {
        $this->_view->titulo = "Edición de Módulo";
        $this->_view->setJs(array('modulo'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        $this->_view->modulo =  $paginador->paginar($this->_modulo->buscar(getPostParam('id')),$pagina);
        $this->_view->paginacion = $paginador->getView('paginacion','desarrollo/modulo/index');
        
        $this->_view->renderizar('index');
        exit();
    }
    
    

    
    public function estatusModulo()
    {
        echo json_encode($this->_modulo->estatusModulo($this->getInt('valor'),$this->getInt('estatus')));
    }
    
    public function eliminar($id)
    {
        if($id)
        {
            if($this->_modulo->desactivar($id))
            {
                $this->redireccionar('desarrollo/modulo/index');
                exit();
            }
            else
            {
                error::alerta('1002','desarrollo/modulo/index');
                exit();
            }
        }
            
    }
    public function buscarModulo()
    {
         echo json_encode($this->_modulo->buscar($this->getPostParam('valor')));
    }
    
    
    //--------------------------------------------------------------------------
    //    METODO QUE MUESTRA LISTADO DE LOS RECURSOS DE UN MODULO
    //--------------------------------------------------------------------------
    public function recurso($modulo,$pagina = 1)
    {
        $this->_view->titulo = "Recursos del Modulo : ";
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        $this->_view->lista =  $paginador->paginar($this->_recurso->buscarRecMod($modulo),$pagina);
        $this->_view->paginacion = $paginador->getView('paginacion','desarrollo/modulo/recurso');
        
        $this->_view->renderizar('recurso');
        exit();
        
    } 

	public function archivoModulo($valor = false)
	{
		$lista = array();
		if($valor)
		{
			$datos =  $this->_modulo->buscar($valor);
			$dir = strstr($datos['url_modulo'], '/', true);
			
			$ruta = APP_PATH . 'modules'. DS .$dir;
			if(is_dir($ruta))
			{		
				if($directorio = opendir($ruta)) 	
				{	
					while (false !== ($archivo = readdir($directorio)))
					{
						if(is_dir($archivo))
						{
							$lista[] = array("tipo"=>"dir","nombre"=>$archivo);	
						}else
						{
							$lista[] = array("tipo"=>"arc","nombre"=>$archivo);	
						}	
						
					}
				}
			}else
			{
				
			}
			//print_r($lista);exit();	
			$this->_view->lista = $lista; 
			$this->_view->renderizar('visor','desarrollo');
			exit();
		}		
	}	
}