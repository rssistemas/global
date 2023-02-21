<?php
class tipoDepositoController extends archivoController
{
    private $_tipoDeposito;
    
    public function __construct() {
        parent::__construct();
        $this->_tipoDeposito = $this->loadModel('tipoDeposito');
    }
    
    /* Cuando se realiza el llamado al maestro principal
    * http://localhost/pdval/archivo/nombreDeLaVista/index/
    * se ejecuta la funcion index() del controlador del maestro */
    public function index($pagina = 1)
    {
         //define el titulo de la presente vista
        $this->_view->titulo = "Tipo de Depósito";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('tipoDeposito'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_tipoDeposito->cargarTipoDeposito_index(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_tipoDeposito->cargarTipoDeposito_index(),$pagina);
        }
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/tipoDeposito/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','archivo');
    }

    //llama a la inclucion o edicion del registro segun sea el caso
    public function agregar()
    {
        $datos = array( 
        "id"=>validate::getPostParam('id'),
        "descripcion"=>validate::getPostParam('descripcion'),
        "comentario"=>validate::getPostParam('comentario'));
        if(validate::getPostParam('guardar')==1)
        {
            if($this->_tipoDeposito->incluir($datos))
            {
                $this->_view->error = "Registro nuevo guardado...";
            }else
            {
                $this->_view->error = "Error Guardando Registro nuevo..." . $this->_tipoDeposito->regLog();
                $this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if(validate::getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_tipoDeposito->modificar($datos))
            {
                $this->_view->error = "Registro editado almacenado..." . $this->_tipoDeposito->regLog();
            }else //sino hubo edicion recibe false
            {
                $this->_view->error = "Error guardando edicion..." . $this->_tipoDeposito->regLog();
                $this->_view->renderizar('index','archivo');
                //exit();
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('archivo/tipoDeposito/index/','archivo');
    }

    /* llama a la desactivacion del objeto a traves del id
    devolviendo un valor por json */
    public function eliminarTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->desactivar(validate::getInt('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->verificar_existencia(validate::getPostParam('valor')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->buscar(validate::getInt('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
