<?php
class grupoController extends almacenController
{
    private $_grupo;
    private $_clasificacion;
    public function __construct() {
        parent::__construct();
        $this->_grupo = $this->loadModel('grupo');
		$this->_clasificacion = $this->loadModel('clasificacion');
    }

    public function index($pagina = 1)
    {
        //define el titulo de la presente vista
        $this->_view->title = "Clasificaciones";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('grupo'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        //Define los registros que se cargaran
        //en la vista principal
        
        if($this->getPostParam('busqueda'))
        {
            $lista = $paginador->paginar($this->_grupo->cargarGrupo($this->getPostParam('busqueda')),$pagina);
            $this->_view->lista = $lista;
        }
        else
        {
            $lista = $paginador->paginar($this->_grupo->cargarGrupo(),$pagina);
            $this->_view->lista = $lista;
        }
		
		
		
            if(count($lista)==0)
		$this->_view->info = "Busqueda sin resultados ....";
		
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/rubro/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
		
	$this->_view->clasificacion = $this->_clasificacion->listar();
		
        $this->_view->renderizar('index','almacen','Clasificaciones');
        exit();
    }
	//---------------------------------------------------------------------------
    //llama a la inclucion o edicion del registro segun sea el caso
	//--------------------------------------------------------------------------
    public function agregar()
    {
        $datos = array( 
            "id"=>$this->getPostParam('id'),
            "clasificacion"=>$this->getInt('clasificacion'),
            "descripcion"=>$this->getPostParam('descripcion'),
            "comentario"=>$this->getPostParam('comentario'));
			
        if($this->getPostParam('guardar')==1)
        {
            if($this->_grupo->incluir($datos))
            {
                $this->_view->mensaje = "Registro nuevo guardado...";
                //$this->_view->renderizar('index','archivo');
            }
            else
            {
                $this->_view->error = "Error Guardando Registro nuevo..." . $this->_grupo->regLog();
                //$this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_rubro->modificar($datos))
            {
                //si es editado exitosamente
                $mensaje = $this->getMensaje('confirmacion','Registro Editado...');
                //$this->_view->error = "Registro editado almacenado...";
                //$this->_view->renderizar('index','archivo');
            }
            else //sino hubo edicion recibe false
            {
                 $mensaje = $this->getMensaje('error', 'Error Editando Registro.....'. $this->_grupo->regLog());
                //$this->_view->error = "Error guardando edicion..." . $this->_rubro->regLog();
                //$this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 2 para guardar edicion
        
        $this->redireccionar('almacen/grupo/index/','almacen');
	exit();
    }
	//-----------------------------------------------------------------------------------------------
    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function eliminarRubro()
    {
        echo json_encode($this->_rubro->desactivar($this->getInt('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarGrupo()
    {
        echo json_encode($this->_grupo->verificar_existencia($this->getPostParam('valor')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarGrupo()
    {
         echo json_encode($this->_grupo->buscar($this->getPostParam('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO