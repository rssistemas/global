<?php
class cargoController extends rrhhController
{
    private $_cargo;
    private $_ultimo_registro;
    private $_departamento;
	
    public function __construct()  {
        parent::__construct();
        $this->_ultimo_registro = 0;
        $this->_cargo= $this->loadModel('cargo');
        $this->_departamento = $this->loadModel('departamento');
    }
    
    public function index($pagina = 1)
    {
        $this->_view->titulo = "Cargo";
        $this->_view->setJs(array('cargo'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_cargo->cargarCargo($this->getPostParam('busqueda')),$pagina);
        }
        else      
        {
            $this->_view->lista = $paginador->paginar($this->_cargo->cargarCargo(),$pagina);
        }
        
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/cargo/index');
        
        $this->_view->medida = $this->_departamento->cargarDepartamento();
        
        $this->_view->renderizar('index','configuracion');
    }
    
     public function ult_persona_reg()
    {
        return $this->_ultimo_registro;
    }
    
    public function agregar()
    {
        if($this->getPostParam('guardar')==1)
        {
            $datos = array(
            "descripcion"=>$this->getPostParam('descripcion'),
            "medida"=>$this->getPostParam('medida'));
            if($this->_cargo->incluir($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('configuracion/cargo/index/','configuracion');
                exit();
            }else
            {
                $this->_view->error = "Error Guardando cargo ....." . $this->_cargo->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }
        }    
        if($this->getPostParam('guardar')==2)
        {
            $datos = array(
            "descripcion"=>$this->getPostParam('descripcion'),
            "medida"=>$this->getPostParam('medida'),
            "id"=>$this->getPostParam('id'));
            if($this->_cargo->modificar($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('configuracion/cargo/index/','configuracion');
                exit();
            }else
            {
                $this->_view->error = "Error Guardando Cargo ....." . $this->_cargo->regLog();
                $this->_view->renderizar('index','configuracion');
                exit();
            }
        }
    } 
  
    public function activar($id)
    {
        if($id)
        {
            if($this->_cargo->activar($id))
            {
                $this->redireccionar('configuracion/cargo/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/cargo/index');
                exit();
            }
            
        }
            
    }        
    public function desactivar($id)
    {
        if($id)
        {
            if($this->_cargo->desactivar($id))
            {
                $this->redireccionar('configuracion/cargo/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/cargo/index');
                exit();
            }
            
        }
            
    }
    public function buscar($valor)
    {
        if($valor){
            $pagina =1;
            $this->_view->setJs(array('cargo'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();

            $this->_view->datos = $paginador->paginar($this->_cargo->buscar($valor),$pagina);
            $this->_view->paginacion = $paginador->getView('paginacion','configuracion/cargo/index');	
            $this->_view->renderizar('index','configuracion/cargo/index');
            exit();
        }else
        {
            $this->redireccionar('configuracion/cargo/index/');
            exit();
        }
    }
    public function buscarCargo()
    {
         echo json_encode($this->_cargo->buscar($this->getPostParam('valor')));
    }        
    public function comprobarCargo()
    {
     
        echo json_encode($this->_cargo->verificar(strtolower($this->getPostParam('descripcion'))));
    
    }
       public function eliminarCargo()
    {
     
        echo json_encode($this->_cargo->desactivar($this->getInt('valor')));
    
    }
    
}


?>
