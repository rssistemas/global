<?php
class servicioController extends archivoController
{
	private $_servicio;
	private $_clasificacion;
	private $_grupo;
	public function __construct()
	{
		parent::__construct();
		
		$this->_servicio = $this->loadModel('servicio');
		$this->_clasificacion = $this->loadModel('clasificacion','almacen');
		$this->_grupo = $this->loadModel('grupo','almacen');
	}	
	
	public function index($pagina = 1)
	{
		//define el titulo de la presente vista
        $this->_view->title = "Inventario";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('servicio'));
        
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_servicio->cargarServicio($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_servicio->cargarServicio(),$pagina);
        }
        
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/servicio/index');
        $this->_view->clasificacion = $this->_clasificacion->listar(5);
        $this->_view->renderizar('index','almacen','Registro de Inventario');
        exit();
		
		
	}
	

	public function agregar()
	{
		
			$datos = array( 
            "id"=>$this->getPostParam('id'),
			"clasificacion"=>$this->getInt('clasificacion'),
            "grupo"=>$this->getInt('grupo'),
			"nombre"=>$this->getPostParam('nombre'),
            "comentario"=>$this->getPostParam('comentario'),
			"usuario"=> session::get('id_usuario')			
			);
			
        if($this->getPostParam('guardar')==1)
        {
            if($this->_servicio->insertar($datos))
            {
                $this->_view->mensaje = "Registro nuevo guardado...";
                //$this->_view->renderizar('index','archivo');
            }
            else
            {
                $this->_view->error = "Error Guardando Registro nuevo..." . $this->_servicio->regLog();
                //$this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_servicio->modificar($datos))
            {
                //si es editado exitosamente
                //$mensaje = $this->getMensaje('confirmacion','Registro Editado...');
                $this->_view->info = "Registro editado ...";
                //$this->_view->renderizar('index','archivo');
            }
            else //sino hubo edicion recibe false
            {
                 //$mensaje = $this->getMensaje('error', 'Error Editando Registro.....'. $this->_grupo->regLog());
                $this->_view->info = "Error guardando edicion..." . $this->_servicio->regLog();
                //$this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 2 para guardar edicion
        
        $this->redireccionar('archivo/servicio/index/','compra');
		exit();
		
		
		
	}		
	
	
	
	
	
	
	
	public function cargarGrupo()
    {
         echo json_encode($this->_grupo->cargarGrupoCla($this->getInt('valor')));
    }

}


?>