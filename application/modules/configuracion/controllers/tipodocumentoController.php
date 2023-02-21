<?php
class tipodocumentoController extends configuracionController
{
    private $_tipoDocumento;
    
    public function __construct()  {
        parent::__construct();
        $this->_tipoDocumento= $this->loadModel('tipoDocumento');
    }

    /* Cuando se realiza el llamado al maestro principal
    * http://localhost/pdval/archivo/nombreDeLaVista/index/
    * se ejecuta la funcion index() del controlador del maestro */
    public function index($pagina = 1)
    {
        //define el titulo de la presente vista
        $this->_view->title = "Tipo de Documento";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('tipoDocumento'));
        $this->_view->setJsPlugin(array('validaciones'));
       
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_tipoDocumento->cargarTipoDocumento_index(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_tipoDocumento->cargarTipoDocumento_index(),$pagina);
        }
        
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/tipoDocumento/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','configuracion');
    }

    //llama a la inclucion o edicion del registro segun sea el caso
    public function agregar()
    {
        $datos = array( 
            "id"=>validate::getPostParam('id'),
            "nombre"=>validate::getPostParam('nombre'),
            "corto"=>  validate::getPostParam('corto'),
            "accion"=>  validate::getPostParam('accion') );  
        if($this->getInt('guardar')==1)
        {
            if($this->_tipoDocumento->incluir($datos))
            {
                $mensaje="CONFIRMACIÓN DE REGITRO - Registro nuevo guardado exitosamente...";
            }
            else
            {
                $mensaje="CONFIRMACIÓN DE REGISTRO - ERROR al guardar el nuevo registro...";
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if(validate::getInt('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_tipoDocumento->modificar($datos))
            {
                $mensaje = "CONFIRMACIÓN DE REGITRO - Registro editado exitosamente...";
            }
            else //sino hubo edicion recibe false
            {
                $mensaje = array("error"=>"Error guardando edicion..." . $this->_tipoDocumento->regLog());
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('configuracion/tipoDocumento/','configuracion');
    }
    
    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function estatusTipoDocumento()
    {
        echo json_encode($this->_tipoDocumento->estatusTipoDocumento(validate::getInt('valor'),validate::getInt('estatus')));
    }

    public function comprobarUso()
    {
        echo json_encode($this->_tipoDocumento->verificar_uso(validate::getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTipoDocumento()
    {
        echo json_encode($this->_tipoDocumento->verificar_existencia(validate::getPostParam('valor')));
    }
    
    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTipoDocumento()
    {
        echo json_encode($this->_tipoDocumento->buscar(validate::getPostParam('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
