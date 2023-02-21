<?php
class tipodepositoController extends almacenController
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
        $this->_view->title = "Tipo de Depósito";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('tipodeposito'));
        $this->_view->setJsPlugin(array('validaciones'));
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
        
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/tipoDeposito/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','configuracion','Tipo Deposito');
        exit();
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
            if($this->_tipoDeposito->incluir($datos))
            {
                $mensaje="CONFIRMACIÓN DE REGITRO - Registro nuevo guardado exitosamente...";
            }else
            {
                $mensaje="CONFIRMACIÓN DE REGISTRO - ERROR al guardar el nuevo registro...";
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_tipoDeposito->modificar($datos))
            {
                $mensaje = "CONFIRMACIÓN DE REGITRO - Registro editado exitosamente...";
            }
            else //sino hubo edicion recibe false
            {
                $mensaje = array("error"=>"Error guardando edicion..." . $this->_tipoDeposito->regLog());
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('almacen/tipoDeposito/','configuracion');
        exit();
    }

    /* llama a la desactivacion del objeto a traves del id
    devolviendo un valor por json */
    public function estatusTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->estatusTipoDeposito($this->getInt('valor'),$this->getInt('estatus')));
    }
    
    public function eliminarTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->eliiminarTipoDeposito($this->getInt('valor')));
    }
    
    public function comprobarUso()
    {
        echo json_encode($this->_tipoDeposito->verificar_uso($this->getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->verificar_existencia($this->getPostParam('valor')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTipoDeposito()
    {
        echo json_encode($this->_tipoDeposito->buscar($this->getInt('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
