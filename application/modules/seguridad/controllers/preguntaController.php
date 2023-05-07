<?php
class preguntaController extends seguridadController
{
    private $_pregunta;
    private $_empresa;




    public function __construct() {
        parent::__construct();
        $this->_empresa = session::get('empresa');

        $this->_pregunta= $this->loadModel('pregunta');

        $this->_pregunta->get_all('empresa_id='.$this->_empresa);
    }

    
    public function index($pagina = 1)
    {
        $this->_view->title = "Preguntas de Seguridad";
        $this->_view->setJs(array('pregunta'));
        $this->_view->setJsPlugin(array('validaciones'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        $empresa = session::get('actEmp');
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_pregunta->cargarPregunta(validate::getPostParam('busqueda'),$this->_empresa),$pagina);
        }
        else      
        {
            $this->_view->lista = $paginador->paginar($this->_pregunta->cargarPregunta(false,$this->_empresa),$pagina);
        }
		$this->_view->paginacion = $paginador->getView('paginacion','seguridad/pregunta/index');	
		$this->_view->renderizar('index','seguridad');
		exit();
    }

    public function agregar()
    {
		$empresa = session::get('actEmp');
		
        $datos = array("descripcion"=>validate::getPostParam('descripcion'),"id"=>validate::getPostParam('id'),"empresa"=>$empresa[0]['id_empresa']);
        
        if(validate::getInt('guardar')==1)
        {
            if($this->_pregunta->insertar($datos))
            {
                $this->_view->mensage = "Pregunta grabado correctamente ...";
                $this->redireccionar('seguridad/pregunta/index/','seguridad');
                exit();  
            }
            else
            {
                $this->_view->error = "Error grabando Pregunta ...";
                $this->redireccionar('seguridad/pregunta/index/','seguridad');
                exit();
            }
        }
        if(validate::getInt('guardar')==2)
        {
            if($this->_pregunta->modificar($datos))
            {
                $this->_view->mensage = "Pregunta grabado correctamente ...";
                $this->redireccionar('seguridad/pregunta/index/','seguridad');
                exit();    
            }
            else
            {
                $this->_view->error = "Error grabando Pregunta ...";
                $this->redireccionar('seguridad/pregunta/index/','seguridad');
                exit();
            }
        }    
        $this->redireccionar('seguridad/pregunta/','seguridad');
    }//FIN DE OPCION 2 para guardar edicion
   
    public function editar($id)//analisar para borrarrrrrrr
    {
        if(validate::getInt('editar')==1)
        {
            if($this->_pregunta->editar(validate::getPostParam('descripcion'),validate::getInt('id')))
            {
                $this->redireccionar('configuracion/pseguridad/index/');
                exit();   
            }else
            {
                error::alerta('1002','configuracion/pseguridad/index/');
                exit();
            }
        }    
        
        if($id)
        {
            $this->_view->datos = $this->_pregunta->consultar($id);
        }
        $this->_view->renderizar('editar','configuracion');
		exit();
    }
    
            
    public function activar($id)
    {
        if($id)
        {
            if($this->_pregunta->activar($id))
            {
                $this->redireccionar('configuracion/pseguridad/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/pseguridad/index');
                exit();
            }
            
        }
            
    }        
    public function desactivar($id)
    {
        if($id)
        {
            if($this->_pregunta->desactivar($id))
            {
                $this->redireccionar('configuracion/pseguridad/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/pseguridad/index');
                exit();
            }
            
        }
            
    }
    public function buscar($valor)
    {
        if($valor){
            $pagina =1;
            $this->_view->setJs(array('pseguridad'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();

            $this->_view->datos = $paginador->paginar($this->_pregunta->buscar($valor),$pagina);
            $this->_view->paginacion = $paginador->getView('paginacion','configuracion/pseguridad/index');	
            $this->_view->renderizar('index','configuracion/pseguridad/index');
            exit();
        }else
        {
            $this->redireccionar('configuracion/pseguridad/index/');
            exit();
        }
    }
            
    public function comprobarPregunta()
    {
        echo json_encode($this->_pregunta->verificar_existencia(strtolower(validate::getPostParam('valor'))));
    }
    public function comprobarUso()
    {
        echo json_encode($this->_pregunta->verificar_uso(validate::getPostParam('valor')));
    }
    public function buscarPregunta()
    {
        echo json_encode($this->_pregunta->buscar(validate::getPostParam('valor')));
    }
    public function eliminarPregunta()
    {
        echo json_encode($this->_pregunta->desactivar(validate::getPostParam('valor')));
    }        
}


?>
