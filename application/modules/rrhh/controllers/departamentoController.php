<?php
class departamentoController extends rrhhController
{
    private $_departamento;
    public function __construct() {
        parent::__construct();
        $this->_departamento = $this->loadModel('departamento');
    }
    public function index($pagina = 1)
    {
        $this->_view->titulo = "Departamento";
        $this->_view->setJs(array('departamento'));
                
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_departamento->cargarDepartamento($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_departamento->cargarDepartamento(),$pagina);
        }     
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/departamento/index');	
        
        $this->_view->renderizar('index','configuracion');
    }
    
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            $datos = array(
                'descripcion'=>$this->getPostParam('descripcion'),
                'telefono'=>$this->getPostParam('telefono'));
            if(!$this->_departamento->incluirDepartamento($datos))
            {
                $this->_view->error = "Error Guardando Departamento .....".$this->_departamento->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }else
            {
                $this->redireccionar('configuracion/departamento/index/');
                exit();
            }
        }
        if($this->getInt('guardar')==2)
        {
            $datos = array(
                'descripcion'=>$this->getPostParam('descripcion'),
                'telefono'=>$this->getPostParam('telefono'),
                'id'=>$this->getInt('id'));
            if(!$this->_departamento->modificarDepartamento($datos))
            {
                $this->_view->error = "Error editando Departamento .....".$this->_departamento->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }else
            {
                $this->redireccionar('configuracion/departamento/index/');
                exit();
            }
        }
        $this->_view->renderizar('index','configuracion');
    }
    public function desactivarDepartamento($ref)
    {
        if($ref)
        {
            $this->_departamento->desactivar($ref);
        }
        $this->redireccionar('configuracion/departamento/index/');
        exit();
    }       
    public function buscarDepartamento()
    {
        echo json_encode($this->_departamento->buscar($this->getInt('valor')));
    }
    public function eliminarDepartamento()
    {
        echo json_encode($this->_departamento->desactivar($this->getInt('valor')));
    }
}