<?php
class tipoTransporteController extends archivoController
{
    private $_tipoTransporte;
    
    public function __construct() {
        parent::__construct();
        $this->_tipoTransporte = $this->loadModel('tipoTransporte');
    }
    
    /* Cuando se realiza el llamado al maestro principal
    * http://localhost/pdval/archivo/nombreDeLaVista/index/
    * se ejecuta la funcion index() del controlador del maestro */
    public function index($pagina = 1)
    {
        //define el titulo de la presente vista
        $this->_view->titulo = "Tipo de Transporte";
         //carga el archivo JS del maestro
        $this->_view->setJs(array('tipoTransporte'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        //Define los registros que se cargaran
        //en la vista principal
        /*Cargara en la vista el listado de los archivos registrados
        * a traves del metodo del modelo -->  cargarObjeto();
        * y recibe el resultado en el atributo lista
        *  para usado desde la vista marca principal  */
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_tipoTransporte->cargarTipoTransporte_index($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_tipoTransporte->cargarTipoTransporte_index(),$pagina);
        }
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/tipoTransporte/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','archivo');
    }

    //llama a la inclucion o edicion del registro segun sea el caso
   public function agregar()
    {
        $datos = array( 
        "id"=>$this->getPostParam('id'),
        "descripcion"=>$this->getPostParam('descripcion'),
        "comentario"=>$this->getPostParam('comentario'));
        if($this->getPostParam('guardar')==1)
        {
            if($this->_tipoTransporte->incluir($datos))
            {
                $this->_view->error = "Registro nuevo guardado...";
            }else
            {
                $this->_view->error = "Error Guardando Registro nuevo..." . $this->_tipoTransporte->regLog();
                $this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_tipoTransporte->modificar($datos))
            {
                $this->_view->error = "Registro editado almacenado..." . $this->_tipoTransporte->regLog();
            }else //sino hubo edicion recibe false
            {
                $this->_view->error = "Error guardando edicion..." . $this->_tipoTransporte->regLog();
                $this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('archivo/tipoTransporte/index/','archivo');
    }
    
    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */ 
    public function eliminarTipoTransporte()
    {
        echo json_encode($this->_tipoTransporte->desactivar($this->getInt('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTipoTransporte()
    {
        echo json_encode($this->_tipoTransporte->verificar_existencia($this->getPostParam('valor')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTipoTransporte()
    {
        echo json_encode($this->_tipoTransporte->buscar($this->getPostParam('valor')));
    }
    
}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO