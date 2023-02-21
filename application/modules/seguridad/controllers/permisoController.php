<?php
class permisoController extends seguridadController
{
    private $_permiso;
    public function __construct() {
        parent::__construct();
        $this->_permiso = $this->loadModel('permiso');
    }
    public function index($pagina = 1)
    {
        $this->_view->title = "Permiso";
        $this->getLibrary('paginador');
        $this->_view->setJs(array('permiso'));
        $this->_view->setJsPlugin(array('validaciones'));
        $paginador = new Paginador();
        
        $empresa = session::get('actEmp');
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_permiso->cargarPermiso(validate::getPostParam('busqueda'),$empresa[0]['id_empresa']),$pagina);
        }
        else      
        {
            $this->_view->lista = $paginador->paginar($this->_permiso->cargarPermiso(false,$empresa[0]['id_empresa']),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','seguridad/permiso/index');	
        $this->_view->renderizar('index','seguridad');
    }
    
    public function agregar()
    {
		$empresa = session::get('actEmp');
        $datos = array(
                'nombre'=>validate::getPostParam('nombre'),
                'descripcion'=>validate::getPostParam('descripcion'),
                'clave'=>validate::getPostParam('clave'),
                'id'=>validate::getPostParam('id'),
                'empresa'=>$empresa[0]['id_empresa']
                );
        if(validate::getInt('guardar')==1)
        {
            if(!$this->_permiso->incluirPermiso($datos))
            {
                $this->_view->error = "Error Guardando permisos .....";
                $this->_view->renderizar('nuevo','seguridad');
                exit();
            }
            else
            {
                $this->redireccionar('seguridad/permiso/index/');
                exit();
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if(validate::getInt('guardar')==2)
        {
            if(!$this->_permiso->modificarPermiso($datos))
            {
                $this->_view->mensage = "Permiso grabado correctamente ...";
                $this->redireccionar('seguridad/permiso/index/','seguridad');
                exit();    
            }else
            {
                $this->_view->error = "Error grabando Permiso ...";
                $this->redireccionar('seguridad/permiso/index/','seguridad');
                exit();
            }
        }    
        $this->redireccionar('seguridad/permiso/','seguridad');
    }
    
    public function activarPermiso($permiso)
    {
        if($permiso)
        {
            $this->_permiso->activar($permiso);
        }
        $this->redireccionar('seguridad/permiso/index/');
        exit();
            
    }        
    public function desactivarPermiso($permiso)
    {
        if($permiso)
        {
            $this->_permiso->desactivar($permiso);
        }
        $this->redireccionar('seguridad/permiso/index/');
        exit();
    }
    
    //busqueda formato json
    public function buscarPermiso()
    {
         echo json_encode($this->_permiso->buscar(validate::getPostParam('valor')));
    }
    
    public function eliminarPermiso()
    {
        echo json_encode($this->_permiso->desactivar(validate::getPostParam('valor')));
            
    }
    
    public function comprobarUso()
    {
        echo json_encode($this->_permiso->verificar_uso(validate::getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarPermiso()
    {
        echo json_encode($this->_permiso->verificar_existencia(validate::getPostParam('valor'),validate::getPostParam('desc')));
    }
    
}
