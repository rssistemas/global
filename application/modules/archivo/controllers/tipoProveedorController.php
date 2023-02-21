<?php
class tipoProveedorController extends archivoController
{
    private $_tipoProveedor;
    private $_ultimo_registro;
    
    public function __construct()  {
        parent::__construct();
        $this->_ultimo_registro = 0;
        $this->_tipoProveedor= $this->loadModel('tipoProveedor');
    }

    public function index($pagina = 1)
    {
        $this->_view->titulo = "Tipo de Proveedor";
        $this->_view->setJs(array('tipoProveedor'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_tipoProveedor->cargarTipoProveedor($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_tipoProveedor->cargarTipoProveedor(),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/tipoProveedor/index');	
        $this->_view->renderizar('index','archivo');
    }
    
     public function ult_persona_reg()
    {
        return $this->_ultimo_registro;
    }
    
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            if($this->getPostParam('descripcion'))
            {
                if($this->_tipoProveedor->insertar($this->getPostParam('descripcion')))
                {
                    $this->redireccionar('archivo/tipoProveedor/index/');
                    exit();   
                }else
                {
                    $this->_view->_error = "Error guardando proveedor ...".$this->_tipoProveedor->regLog();
                    $this->_view->renderizar('index','archivo');
                    exit();
                }
            }else
            {
                $this->_view->_error = "Datos incompletos ...";
                $this->_view->renderizar('index','archivo');
                exit();
            }
        }
        if($this->getInt('guardar')==2)
        {
            $datos = array(
                "id"=>$this->getPostParam('id'),
                "descripcion"=>$this->getPostParam('descripcion'));
            if($this->_tipoProveedor->modificar($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('archivo/tipoProveedor/index/','archivo');
                exit();
            }else
            {
                $this->_view->error = "Error Guardando Tipo Proveedor ....." . $this->_tipoProveedor->regLog();
                $this->_view->renderizar('index','archivo');
                exit();
            }
        }
        
    }
    
    public function editar($id)
    {
        if($this->getInt('editar')==1)
        {
            if($this->_tipoProveedor->editar($this->getPostParam('descripcion'),$this->getInt('id')))
            {
                $this->redireccionar('archivo/tipoProveedor/index/');
                exit();   
            }else
            {
                error::alerta('1002','archivo/tipoProveedor/index/');
                exit();
            }
        }    
        
        if($id)
        {
            $this->_view->datos = $this->_tipoProveedor->consultar($id);
        }
        $this->_view->renderizar('editar','archivo');
	exit();
    }
    
            
    public function activar($id)
    {
        if($id)
        {
            if($this->_tipoProveedor->activar($id))
            {
                $this->redireccionar('archivo/tipoProveedor/index');
                exit();
            }else
            {
                error::alerta('1002','archivo/tipoProveedor/index');
                exit();
            }
            
        }
            
    }        
    public function desactivar($id)
    {
        if($id)
        {
            if($this->_tipoProveedor->desactivar($id))
            {
                $this->redireccionar('archivo/tipoProveedor/index');
                exit();
            }else
            {
                error::alerta('1002','archivo/tipoProveedor/index');
                exit();
            }
            
        }
            
    }
    public function buscar($valor)
    {
        if($valor){
            $pagina =1;
            $this->_view->setJs(array('tipoProveedor'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();

            $this->_view->datos = $paginador->paginar($this->_tipoProveedor->buscar($valor),$pagina);
            $this->_view->paginacion = $paginador->getView('paginacion','archivo/tipoProveedor/index');	
            $this->_view->renderizar('index','archivo/tipoProveedor/index');
            exit();
        }else
        {
            $this->redireccionar('archivo/tipoProveedor/index/');
            exit();
        }
    }
    public function buscarTipoProveedor()
    {
         echo json_encode($this->_tipoProveedor->buscar($this->getPostParam('valor')));
    }      
    public function comprobarTipoProveedor()
    {
        echo json_encode($this->_tipoProveedor->verificar(strtolower($this->getPostParam('descripcion'))));
    
    }
    public function eliminarTipoProveedor()
    {
     
        echo json_encode($this->_tipoProveedor->desactivar($this->getInt('valor')));
    
    }
}


?>
