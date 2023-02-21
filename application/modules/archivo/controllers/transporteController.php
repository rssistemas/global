<?php
class transporteController extends archivoController
{
    private $_transporte;
    private $_tipoTransporte;
    private $_marcaTransporte;
    private $_unidad_med;
    
    public function __construct() {
        parent::__construct();
        $this->_transporte = $this->loadModel('transporte');
        $this->_tipoTransporte = $this->loadModel('tipoTransporte');
        $this->_marcaTransporte = $this->loadModel('marcaTransporte');
        $this->_unidad_med = $this->loadModel('unidadMedida','configuracion');
    }
    public function index($pagina = 1)
    {
        $this->_view->setJsPlugin(array('validaciones'));
        $this->_view->titulo = "Transporte";
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista =  $paginador->paginar($this->_transporte->cargarTransportes($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista =  $paginador->paginar($this->_transporte->cargarTransportes(),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/transporte/index');
        $this->_view->renderizar('index');
        exit();
    }
    
    //metodo que incluye un usuario  
    public function agregar()
    {
        if($this->getPostParam('guardar')=='gu')
        {
            $datos = array(
                "placa" =>$this->getPostParam('placa'),
                "marca"=>$this->getInt('marca'),
                "modelo"=>  $this->getPostParam('modelo'),
                "capacidad"=>$this->getPostParam('capacidad'),
                "condicion"=>"Disponible",
                "uni_med"=>$this->getInt('medida'),
                "tip_trans"=>$this->getInt('tipo') );
            if($this->_transporte->insertar($datos))
            {
                $this->redireccionar('archivo/transporte/index');
                exit();
            }
            else
            {
                $this->_view->error = "Error guardando TRANSPORTE.".$this->_transporte->regLog();
                $this->_view->renderizar('agregar','archivo');
                exit();
            }
        }
        else
        {
            $this->_view->titulo = "Agregar Transporte";
            $this->_view->setJs(array('transporte'));
            $this->_view->setJsPlugin(array('validaciones'));
            $this->_view->tipo = $this->_tipoTransporte->cargarTipoTransporte();
            $this->_view->marca = $this->_marcaTransporte->cargarMarcaTransporte();
            $this->_view->medida = $this->_unidad_med->cargarUnidadMedida();
            $this->_view->renderizar('agregar','archivo');
            exit();
        }
    }
    
    public function editar($id = FALSE)
    {
        if($this->getPostParam('guardar')=='ed')
        {
           $datos = array(
                "placa" =>$this->getPostParam('placa'),
                "marca"=>$this->getInt('marca'),
                "modelo"=>  $this->getPostParam('modelo'),
                "capacidad"=>$this->getPostParam('capacidad'),
                "uni_med"=>$this->getInt('medida'),
                "id"=>$this->getInt('id'),
                "tip_trans"=>$this->getInt('tipo') );
            if($this->_transporte->modificar($datos))
            {
                $this->redireccionar('archivo/transporte/index');
                exit();               
            }else
            {
                $this->_view->error = "Error editando TRANSPORTE....".$this->_transporte->regLog();
                $this->_view->renderizar('agregar','archivo');
                exit();
            }
            
        }
        else
        {
            $this->_view->transporte = $this->_transporte->buscar($id);
            $this->_view->tipo = $this->_tipoTransporte->cargarTipoTransporte();
            $this->_view->marca = $this->_marcaTransporte->cargarMarcaTransporte();
            $this->_view->medida = $this->_unidad_med->cargarUnidadMedida();
            $this->_view->titulo = "Editar Transporte";
            $this->_view->renderizar('editar','archivo');
            exit();    
        }    
    }
    
    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function eliminarTransporte()
    {
        echo json_encode($this->_transporte->desactivar($this->getInt('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTransporte()
    {
        echo json_encode($this->_transporte->verificar_existencia($this->getPostParam('placa')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTransporte()
    {
         echo json_encode($this->_transporte->buscar($this->getPostParam('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO