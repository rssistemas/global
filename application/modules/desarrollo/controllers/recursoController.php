<?php
class recursoController extends desarrolloController
{
    private $_recurso;
    private $_modulo;

    public function __construct() {
        parent::__construct();
        $this->_recurso = $this->loadModel('recurso');
        $this->_modulo = $this->loadModel('modulo');
    }
    public function index($pagina = 1)
    {
        $this->_view->title = "Recurso";
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_recurso->cargarRecurso($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista =  $paginador->paginar($this->_recurso->cargarRecurso(),$pagina);
        }   
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/recurso/index');
        $this->_view->renderizar('index');
        exit();
    }

    public function edicion($pagina = 1)
    {
        $this->_view->title = "Edición de Recurso";
        $this->_view->setJs(array('recurso'));
        $this->getLibrary('paginador');

        $paginador = new Paginador();
        $this->_view->recurso =  $paginador->paginar($this->_recurso->buscar(getPostParam('id')),$pagina);
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/recurso/index');
        
        $this->_view->renderizar('index');
        exit();
    }
    
    public function agregar()
    {
        $this->_view->title = "Agregar Recurso";
        $this->_view->setJs(array('recurso'));
        if($this->getInt('guardar')==1)
        {
			$modulo = $this->getPostParam('modulo');
			$nombre = $this->getPostParam('nombre');
            $datos = array(
                "nombre" =>$this->getPostParam('nombre'),
                "descripcion"=>$this->getPostParam('descripcion'),
                "url"=>  $this->getPostParam('url'),
                "icono"=>$this->getPostParam('icono'),
                "posicion"=>$this->getPostParam('posicion'),
                "estatus"=>$this->getPostParam('estatus'),
                "modulo"=>$this->getPostParam('modulo') );
            if($this->_recurso->insertar($datos))
            {
				
				$datosModulo = $this->_modulo->buscar($modulo);
				$nombreModulo = $datosModulo['nombre_modulo'];
				//-----------------------------------------------------------------------
				//se crea el controlador
				//------------------------------------------------------------------------
				if(is_readable(APP_PATH."modules". DS .$nombreModulo. DS .'controllers'))
				{
					$ruta_controler = APP_PATH."modules". DS .$nombreModulo. DS .'controllers';
					if($file = fopen($ruta_controler . DS .  $nombre.'Controller.php',"a+"))
					{
						$cadena = "<?php". PHP_EOL;
						$cadena = $cadena ."class ".$contenedor."Controller extends ".$nombreModulo."Controller{". PHP_EOL;
						$cadena	= $cadena ."public function __construct() {". PHP_EOL;
						$cadena = $cadena ."parent::__construct();}". PHP_EOL;
						$cadena = $cadena ."public function index() {}". PHP_EOL;
						$cadena = $cadena ."} ". PHP_EOL;
						
						if(fwrite($file,$cadena))
						{
							fclose($file);
							//$this->redireccionar('desarrollo/modulo/index/','desarrollo');
							//exit();
							//-------------------------------------------------------------
							//se crea el modelo 
							//-------------------------------------------------------------
							if(is_readable(APP_PATH."modules". DS .$nombreModulo. DS .'models'))
							{
								$ruta_model = APP_PATH."modules". DS .$nombreModulo. DS .'models';
								if($file = fopen($ruta_model . DS . $nombreModulo.'Model.php',"a+"))
								{
									
									$cadena = "<?php". PHP_EOL;
									$cadena = $cadena ."class ".$nombreModulo."Model extends model{". PHP_EOL;
									$cadena	= $cadena ."public function __construct() {". PHP_EOL;
									$cadena = $cadena ."parent::__construct();}". PHP_EOL;
									//$cadena = $cadena ."public function index() {}". PHP_EOL;
									$cadena = $cadena ."} ". PHP_EOL;
									
									if(fwrite($file,$cadena))
									{							
										//$this->redireccionar('desarrollo/recurso/index/','desarrollo');
										//exit();	
										//--------------------------------------------------------------------
										//se crea la vista
										//--------------------------------------------------------------------
										fclose($file);
										if(is_readable(APP_PATH."modules". DS .$nombreModulo. DS .'views'))
										{	
											mkdir(APP_PATH."modules". DS .$nombreModulo. DS .'views'.DS.$nombre, 0777, true);
											$ruta_vista = APP_PATH."modules". DS .$nombreModulo. DS .'views'.DS.$nombre ;
											if($file = fopen($ruta_vista . DS .'index.phtml',"a+"))
											{
												$cadena = "<div><h1>Vista index</h1></div>". PHP_EOL;
												fwrite($file,$cadena);
												
																							
											}
											fclose($file);
											$this->redireccionar('desarrollo/recurso/index/','desarrollo');
											exit();
											
										}
			
									}else
									{
										fclose($file);						   	
									}	
								}else
								{
									fclose($file);
								}	
							}	
							
						}else
						{
							fclose($file);						   	
						}			
						
					}else
					{
						fclose($file);
					}	
				
				}
								
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('desarrollo/recurso/index/','desarrollo');
                exit();
            }
            else
            {
                $this->_view->error = "Error Guardando Recurso ....." . $this->_recurso->regLog();
                $this->_view->renderizar('agregar','configuracion');
                exit();
            }
        }
		
		
        $this->_view->modulos = $this->_modulo->cargarModulo();
        $this->_view->renderizar('agregar','configuracion');
        exit();
    }        

    public function editar()
    {
        if($this->getInt('guardar')==1)
        {
            $datos = array(
                "nombre" =>$this->getPostParam('nombre'),
                "descripcion"=>$this->getPostParam('descripcion'),
                "url"=>  $this->getPostParam('url'),
                "icono"=>$this->getPostParam('icono'),
                "posicion"=>$this->getPostParam('posicion'),
                "estatus"=>$this->getPostParam('estatus'),
                "modulo"=>$this->getPostParam('modulo')  );
            if($this->_recurso->modificar($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('configuracion/recurso/index/','configuracion');
                exit();
            }
            else
            {
                $this->_view->error = "Error Guardando recurso ....." . $this->_recurso->regLog();
                $this->_view->renderizar('agregar','configuracion');
                exit();
            }
        }        
        $this->_view->renderizar('agregar','configuracion');
        exit();
    }        
    
    
    
    
    public function eliminar($id)
    {
        if($id)
        {
            if($this->_recurso->desactivar($id))
            {
                $this->redireccionar('configuracion/recurso/index');
                exit();
            }
            else
            {
                error::alerta('1002','configuracion/recurso/index');
                exit();
            }
        }
            
    }
    public function buscarRecurso()
    {
         echo json_encode($this->_recurso->buscar($this->getPostParam('valor')));
    }  
}