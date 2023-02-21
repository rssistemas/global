<?php
class roleController extends seguridadController
{
    private $_role;
    private $_permiso;
    
    public function __construct() {
        parent::__construct();
        $this->_role = $this->loadModel('role');
        $this->_permiso = $this->loadModel('permiso');
    }
    public function index($pagina =1) 
    {
        //define el titulo de la presente vista
        $this->_view->title = "Roles";
         //carga el archivo JS del maestro
        $this->_view->setJs(array('role'));
        //llama a la libreria paginador del framework para
        $this->_view->setJsPlugin(array('validaciones'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        $empresa = session::get('actEmp');
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_role->cargarRoles(validate::getPostParam('busqueda'),$empresa[0]['id_empresa']),$pagina);
        }
        else      
        {
            $this->_view->lista = $paginador->paginar($this->_role->cargarRoles(false,$empresa[0]['id_empresa']),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','seguridad/role/index');	
        $this->_view->renderizar('index','seguridad','role');
        exit();
    }
    
    // metodo del controlador que carga formulario para crear un  role
    public function agregar()
    {
		$empresa = session::get('actEmp');
        $_datos = array(
            "id"=>validate::getPostParam('id'),
            "nombre"=>validate::getPostParam('nombre'),
            "descripcion"=>validate::getPostParam('descripcion'),
            "empresa"=>$empresa[0]['id_empresa']);
            
        if(validate::getInt('guardar')==1)
        {            
            if(!$this->_role->insertar($_datos))
            {
				$this->_view->error = "Error grabando Rol ...";
                logger::errorLog("Error grabando Rol ...",'ERROR');                   
            }  
            else
            {
                $this->_view->mensage = "Informacion: Rol grabado correctamente ...";                
            }    
        }
        if(validate::getInt('guardar')==2)
        {
            if(!$this->_role->actualizar($_datos))
            {
                logger::errorLog("Error editando Rol ...",'ERROR');    
            }  
                
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('seguridad/role/');
        exit();
    }     
    
    // metodo del conotrol que activa un role desactivado
    public function activar($role)
    {
        if($role)
        {
            if($this->_role->activar($role))
            {
                $this->_view->mensage = "Rol ACTIVADO correctamente ...";
                $this->redireccionar('seguridad/roles/index/','seguridad');
                exit();
            }else
                {
                    $this->_view->error = "Error ACTIVANDO Role ...";
                    $this->redireccionar('seguridad/roles/index/','seguridad');
                    exit();   
                }
        }    
        
    }
    //metodo del controlador que desactiva un rol activado
    public function desActivar($role)
    {
        if($role)
        {
            if($this->_role->desactivar($role))
            {
                $this->_view->mensage = "Role DESACTIVADO correctamente ...";
                $this->redireccionar('seguridad/roles/index/','seguridad');
                exit();
            }else
                {
                    $this->_view->error = "Error DESACTIVANDO Role ...";
                    $this->redireccionar('seguridad/roles/index/','seguridad');
                    exit();   
                }
        }    
        
    }
    // metodo del controlador que permite cargar permisos del role agrupados por recursos 
    public function permisoRoles($role)
    {
        $rol = $this->_role->buscarRole($role);
        $permisoRole = array();
        
        $_recursos = $this->_role->cargarRecursoRole($role); 
        if(count($_recursos))
        {
            foreach($_recursos as $val)
            {
               $permiso = $this->_permiso->cargarPermisoRecursoRole($val['recurso_id'],$role);
               if($permiso)
               {
                    $permisoRole[] = array("key"=>$val['clave'],
                                           "recurso"=>$val['nombre_recurso'],
                                           "permiso"=>$permiso,
                                           "id_recurso"=>$val['recurso_id'],
                                           "ult_fecha"=>$val['fecha_ult_act'] );   
               }else
                    {
                        $permisoRole[] = array("key"=>$val['clave'],
                                           "recurso"=>$val['nombre_recurso'],
                                           "permiso"=>array(),
                                           "id_recurso"=>$val['recurso_id'],
                                            "ult_fecha"=>$val['fecha_ult_act']);   
                    }
            }    
        }    
        //print_r($permisoRole);
        //exit();
        $this->_view->title = " Asigncación de Recursos para el Rol ".ucwords($rol['nombre_role']);
        $this->_view->rol = $rol;
        $this->_view->lista = $permisoRole;
        $this->_view->renderizar('permiso','seguridad');
        exit();
        
    }
    //================================================================================
    // metodo del controlador que carga formulario para crear un nuevo recurso de role
    //================================================================================
    public function nuevoRecursoRole($role)
    {
		$empresa = session::get('actEmp');
		
        $this->_view->setJs(array('role'));
        $_recurso = $this->loadModel('recurso');
        
        $this->_view->recurso = $_recurso->noRecursoRole($role,$empresa[0]['id_empresa']); 
        
        //llama a la libreria paginador del framework para
        $permiso = $this->_permiso->cargarPermiso();
        $this->_view->lista = $permiso;
        $this->_view->nro_permiso = count($permiso);
        $this->_view->role = $role;
        $this->_view->title = "Recurso del Rol";
        $this->_view->renderizar('recurso','seguridad');
        exit();
    }   
    
    //metodos que guarda los permisos de recurso de role
    public function guardarRecursoRole()
    {
        if(validate::getInt('guardar')==1)
        {
            $arrPermiso = array();
            if(!validate::getInt('recurso'))
            {
               $this->_view->error = "Indique el recurso";
               $this->_view->renderizar('nuevorecurso','seguridad');
               exit();  
            }
            for($i = 1; $i <= validate::getInt('nro_permiso');$i++)
            {
                if(validate::getInt('permiso'.$i))
                {
                    $arrPermiso[]=array(
                        "recurso"=>validate::getInt('recurso'),
                        "permiso"=>validate::getInt('permiso'.$i),
                        "role"=>validate::getInt('role'),
                        "valor"=>'1'
                    );
                }        
            }
            if(!$this->_permiso->incluirPermisoRecursoRole($arrPermiso))
            {
				logger::errorLog("Error registrando permiso en recurso de rol","ERROR");
				
            }   
            $this->redireccionar('seguridad/role/permisoRoles/'.validate::getInt('role').'/');
            exit();
        }
        if(validate::getInt('guardar')==2)
        {
            $arrPermiso = array();
            if(!validate::getInt('recurso'))
            {
               $this->_view->error = "Indique el recurso";
               $this->_view->renderizar('recurso','seguridad');
               exit();  
            }
            
            for($i = 1; $i <= validate::getInt('nro_permiso');$i++)
            {
                if(validate::getInt('permiso'.$i))
                {
                    $arrPermiso[]=array(
                        "recurso"=>validate::getInt('recurso'),
                        "permiso"=>validate::getInt('permiso'.$i),
                        "valor"=>validate::getInt('estatus'.$i),
                        "role"=>validate::getInt('role')
                    );
                }
                
            }
              
            //print_r($arrPermiso);
            //exit();
            if(!$this->_permiso->editarPermisoRecursoRole($arrPermiso))
            {
				logger::errorLog("Error registrando permiso en recurso de rol","ERROR");
                
            }
            $this->redireccionar('seguridad/role/permisoRoles/'.validate::getInt('role'));
            exit();            
            
            
        }
        
    }
    // metodo del controlador que carga formulario para editar un permiso de recurso de rol
    public function editarPermisoRecursoRole($role,$recurso)
    {
        $this->_view->setJs(array('role'));

        $_recurso = $this->loadModel('recurso');
        $this->_view->recursos = $_recurso->cargarRecurso();        

        $permisos = $this->_permiso->cargarPermisoRecursoRole($recurso,$role);
        $noPermisos = $this->_permiso->cargarNoPermisoRecursoRole($recurso,$role);
        $permiso = array_merge($permisos,$noPermisos);
        $this->_view->lista = $permiso;
        $this->_view->nro_permiso = count($permiso);
        $this->_view->role = $role;
        $this->_view->recurso = $recurso;
        $this->_view->title="Editar permisos del recurso";
        $this->_view->renderizar('editarrecurso','seguridad');
        exit();
        
        
    }
    public function eliminarPermisoRecursoRole($recurso,$rol)
    {
			$this->_role->eliminarRecurso($recurso,$rol);
			$this->redireccionar('seguridad/role/permisoRoles/'.$rol);
            exit();
    }
    //--------------------------------------------------------------------------
    //etodos con json
    //---------------------------------------------------------------------------
    
    public function comprobarUso()
    {
        echo json_encode($this->_role->verificar_uso(validate::getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarRole()
    {
        echo json_encode($this->_role->verificar_existencia(validate::getPostParam('valor'),validate::getPostParam('desc')));
    }
    
    public function eliminarRole()
    {
        echo json_encode($this->_role->desactivar(validate::getPostParam('valor')));
    }
    
    public function buscarRole()
    {
        echo json_encode($this->_role->buscarRole(validate::getPostParam('valor')));
    }
          
}
?>
