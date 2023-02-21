<?php
class estadoController extends configuracionController
{
    private $_estado;
    private $_ultimo_registro;
    
    public function __construct()  {
        parent::__construct();
        $this->_ultimo_registro = 0;
        $this->_estado= $this->loadModel('estado');
    }
    public function index($pagina = 1)
    {
    	$this->_acl->acceso('estado_consultar',105,'configuracion-estado-index');
		
        $this->_view->title = "Localidad";
        $this->_view->setJs(array('estado'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_estado->cargarEstado(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
        	$dat =$this->_estado->cargarEstado();
            $this->_view->lista = $paginador->paginar($dat,$pagina,5,true);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/estado/index');
        
        $this->_view->renderizar('index','configuracion','Localidad');
		exit();
    }
    
    public function agregar()
    {
        if(validate::getInt('guardar')==1)
        {
        	$this->_acl->acceso('estado_agregar',105,'configuracion-estado-index');
			
            if(validate::getPostParam('descripcion'))
            {
                if($this->_estado->insertar(validate::getPostParam('descripcion')))
                {
                    $this->redireccionar('configuracion/estado/index/');
                    exit();   
                }else
                {
                    $this->_view->error = "Error guardando estado .....".$this->_estado->regLog();
                    $this->_view->renderizar('index','configuracion');
                    exit();
                }
            }else
            {
                $this->_view->error = "Datos incompletos .....";
                $this->_view->renderizar('index','configuracion');
                exit();
            }
        }
        if(validate::getInt('guardar')==2)
        {
        	$this->_acl->acceso('estado_editar',105,'configuracion-estado-index');
			
            $datos = array("descripcion"=>validate::getPostParam('descripcion'),"id"=>  validate::getInt('id'));
            if($this->_estado->modificar($datos))
            {
                $this->redireccionar('configuracion/estado/index/');
                exit();
            }else
            {
                $this->_view->error = "Error editando estado .....".$this->_estado->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }
        }
    }
    
    public function editar($id)
    {
        if(validate::getInt('editar')==1)
        {
            if($this->_estado->editar(validate::getPostParam('descripcion'),validate::getInt('id')))
            {
                $this->redireccionar('configuracion/estado/index/');
                exit();   
            }else
            {
                error::alerta('1002','configuracion/estado/index/');
                exit();
            }
        }    
        
        if($id)
        {
            $this->_view->datos = $this->_estado->consultar($id);
        }
        $this->_view->renderizar('editar','archivo');
	exit();
    }
    
            
    public function activar($id)
    {
        if($id)
        {
            if($this->_estado->activar($id))
            {
                $this->redireccionar('configuracion/estado/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/estado/index');
                exit();
            }
            
        }
            
    }        
    public function desactivar($id)
    {
        if($id)
        {
            if($this->_estado->desactivar($id))
            {
                $this->redireccionar('configuracion/estado/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/estado/index');
                exit();
            }
        }
    }
    
    public function buscar($valor)
    {
        if($valor){
            $pagina =1;
            $this->_view->setJs(array('estado'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();
            $this->_view->datos = $paginador->paginar($this->_estado->buscar($valor),$pagina);
            $this->_view->paginacion = $paginador->getView('paginacion','configuracion/estado/index');	
            $this->_view->renderizar('index','configuracion/estado/index');
            exit();
        }else
        {
            $this->redireccionar('configuracion/estado/index/');
            exit();
        }
    }
    public function comprobarEstado()
    {
        echo json_encode($this->_estado->verificarEstado(strtolower(validate::getPostParam('descripcion'))));
    }
    public function buscarEstado()
    {
        echo json_encode($this->_estado->buscar(validate::getInt('valor')));
    }
    public function eliminarEstado()
    {
        echo json_encode($this->_estado->desactivar(validate::getInt('valor')));    
    }
}
?>
