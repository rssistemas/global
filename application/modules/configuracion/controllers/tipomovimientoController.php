<?php
class tipomovimientoController extends configuracionController
{
    private $_tipoMov;
    private $_tipoMovimiento;
    
    public function __construct()  {
        parent::__construct();
        $this->_tipoMovimiento= $this->loadModel('tipoMovimiento','configuracion');
    }

    public function index($pagina = 1)
    {
        //define el titulo de la presente vista
        $this->_view->title = "Tipo de Movimiento";
        //carga el archivo JS del maestro
        $this->_view->setJs(array("tipoMovimiento"));
        $this->_view->setJsPlugin(array('validaciones'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        if(validate::getPostParam('busqueda'))
        {
            //$this->_view->lista = $paginador->paginar($this->_tipoMov->buscarTipoMovimiento($this->getPostParam('busqueda')),$pagina);
            $this->_view->lista = $paginador->paginar($this->_tipoMovimiento->cargarTipoMovimiento_index(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            //$this->_view->lista = $paginador->paginar($this->_tipoMov->cargaTipoMovimiento(),$pagina);
            $this->_view->lista = $paginador->paginar($this->_tipoMovimiento->cargarTipoMovimiento_index(),$pagina);
        }
        
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/tipoMovimiento/index');
        
        $this->_view->renderizar('index','configuracion','Tipo Movimientos');
    }

    //llama a la inclucion o edicion del registro segun sea el caso
    public function agregar()
    {
        $datos = array( 
            "id"=>validate::getPostParam('id'),
            "nombre"=>validate::getPostParam('nombre'),
            "accion"=>  validate::getPostParam('accion') );
        if($this->getInt('guardar')==1)
        {
            if($this->_tipoMovimiento->incluir($datos))
            {
                $mensaje="Registro nuevo guardado exitosamente...";
                $this->getMensaje('confirmacion', $mensaje);
            }
            else
            {
                $this->_tipoMovimiento->regLog();
                $mensaje="ERROR al guardar el nuevo registro...";
                $this->getMensaje('error', $mensaje);
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if(validate::getInt('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_tipoMovimiento->modificar($datos))
            {
                $mensaje="Registro Modificado exitosamente...";
                $this->getMensaje('confirmacion', $mensaje);
            }
            else //sino hubo edicion recibe false
            {
                $this->_tipoMovimiento->regLog();
                $mensaje="ERROR Modificando registro...";
                $this->getMensaje('error', $mensaje);
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('configuracion/tipoMovimiento/','configuracion');
    }
    
    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function estatusTipoMovimiento()
    {
        echo json_encode($this->_tipoMovimiento->estatusTipoMovimiento(validate::getInt('valor'),validate::getInt('estatus')));
    }

    public function comprobarUso()
    {
        echo json_encode($this->_tipoMovimiento->verificar_uso(validate::getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTipoMovimiento()
    {
        echo json_encode($this->_tipoMovimiento->verificar_existencia(validate::getPostParam('valor')));
    }
    
    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTipoMovimiento()
    {
        echo json_encode($this->_tipoMovimiento->buscar(validate::getInt('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
