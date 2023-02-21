<?php
class unidadMedidaController extends configuracionController
{
    private $_unidadMedida;
    public function __construct() {
        parent::__construct();
        $this->_unidadMedida = $this->loadModel('unidadMedida');
    }
    public function index($pagina = 1)
    {
        $this->_view->title = "Clasificaciones";
        $this->_view->setJs(array('unidadMedida'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_unidadMedida->cargarUnidadMedida(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_unidadMedida->cargarUnidadMedida(),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/unidadMedida/index');	
        $this->_view->renderizar('index','almacen','Clasificaciones');
        exit();
    }
    
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            $datos = array(
                'nombre'=>validate::getPostParam('descripcion'),
                'simbolo'=>validate::getPostParam('simbolo'));
            if(!$this->_unidadMedida->incluirUnidadMedida($datos))
            {
                $this->_view->error = "Error Guardando Unidad de Medida .....".$this->_unidadMedida->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }else
            {
                $this->redireccionar('configuracion/unidadMedida/index/');
                exit();
            }
        }
        if($this->getInt('guardar')==2)
        {
            $datos = array(
                'id'=>validate::getPostParam('id'),
                'nombre'=>validate::getPostParam('descripcion'),
                'simbolo'=>validate::getPostParam('simbolo'));
            
            if(!$this->_unidadMedida->modificarU($datos))
            {
                $this->_view->error = "Error Guardando Unidad de Medida .....".$this->_unidadMedida->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }else
            {
                $this->redireccionar('configuracion/unidadMedida/index/');
                exit();
            }
            
        }    
        
        $this->_view->renderizar('index','almacen');
        exit();
    }
    public function activarUnidadMedida($unidadM)
    {
        if($unidadM)
        {
            $this->_unidadMedida->activar($unidadM);
        }
        $this->redireccionar('configuracion/unidadMedida/index/');
        exit();
            
    }        
    public function desactivarUnidadMedida($unidadM)
    {
        if($unidadM)
        {
            $this->_unidadMedida->desactivar($unidadM);
        }
        $this->redireccionar('configuracion/unidadMedida/index/');
        exit();
    }
    public function buscarUnidadMedida()
    {
        echo json_encode($this->_unidadMedida->buscar(validate::getInt('valor')));        
    }
    public function eliminarUnidadMedida()
    {
        echo json_encode($this->_unidadMedida->desactivar(validate::getInt('valor')));
    }
}
