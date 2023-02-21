<?php
class marcaController extends almacenController
{
    private $_marca;
    
    public function __construct()  {
        parent::__construct();
        $this->_marca= $this->loadModel('marca');
    }

    
    public function index($pagina = 1)
    {
       
        //carga el archivo JS del maestro
        $this->_view->setJs(array('marca'));
        $this->_view->setJsPlugin(array('validaciones'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        //Define los registros que se cargaran
        
        if($this->getPostParam('busqueda'))
        {
        	$param = strtoupper($this->getPostParam('busqueda'));
		$lista = $paginador->paginar($this->_marca->cargarMarca($param),$pagina);
            
            $this->_view->lista = $lista;
			
			
        }
        else
        {
            $lista = $paginador->paginar($this->_marca->cargarMarca(),$pagina);
            $this->_view->lista = $lista;
        }
		
		if(count($lista)==0)
                	$this->_view->info = "Busqueda sin resultados......";
       
        $this->_view->paginacion = $paginador->getView('paginacion','almacen/marca/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->title = "Clasificaciones";
	$this->_view->renderizar('index','almacen','Clasificaciones');
        exit();
    }

    //llama a la inclucion o edicion del registro segun sea el caso
    public function agregar()
    {
        $datos = array( 
            "id"=>$this->getPostParam('id'),
            "descripcion"=>$this->getPostParam('descripcion'));
			
        if($this->getPostParam('guardar')==1)
        {
            if($this->_marca->insertar($datos))
            {
                $mensaje="CONFIRMACIÓN DE REGITRO - Registro nuevo guardado exitosamente...";
            }
            else
            {
                $mensaje="CONFIRMACIÓN DE REGISTRO - ERROR al guardar el nuevo registro...";
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_marca->modificar($datos))
            {
                $mensaje = "CONFIRMACIÓN DE REGITRO - Registro editado exitosamente...";
            }
            else //sino hubo edicion recibe false
            {
                $mensaje = array("error"=>"Error guardando edicion..." . $this->_marca->regLog());
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('almacen/marca/','almacen');
        exit();
    }

    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function estatusMarca()
    {
        echo json_encode($this->_marca->estatusMarca($this->getInt('valor'),$this->getInt('estatus')));
    }

    public function comprobarUso()
    {
        echo json_encode($this->_marca->verificar_uso($this->getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarMarca()
    {
        echo json_encode($this->_marca->verificar_existencia($this->getPostParam('valor')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarMarca()
    {
        echo json_encode($this->_marca->buscar($this->getInt('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO