<?php
class usuarioController extends seguridadController
{
    private $_usuario;
    private $_persona;
    private $_permiso;
    private $_pregunta;
    private $_rol;
    private $_bitacora;
    private $_empresa;

    public function __construct() {
        
        parent::__construct();

        $this->_empresa = session::get('empresa');
        //instancia modelo usuario
        $this->_usuario = $this->loadModel('usuario');
        
        $this->_usuario->get_all('empresa_id='.$this->_empresa);
        
        
    }
    public function index($pagina = 1)
    {

        $this->_view->title = "Usuarios";
        $this->_view->setJs(array('usuario_js'));
        $this->_view->setJsPlugin(array('validaciones'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();


		     //$empresa = session::get('actEmp');

        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista =  $paginador->paginar($this->_usuario->cargarUsuario(validate::getPostParam('busqueda')),$this->_empresa,$pagina);
        }
        else
        {
            //print_r($this->_usuario->get("_table"));
            $datos = $this->_usuario->get("_table");
            $this->_view->lista =  $paginador->paginar($datos['datos'],$pagina);                        
        }
        $this->_view->paginacion = $paginador->getView('paginacion','seguridad/usuario/index');

        $this->_view->renderizar('index','seguridad','usuario');
        exit();
    }
    //-------------------------------------------------------------------------------------------------------
    //metodo que incluye un usuario
    //-------------------------------------------------------------------------------------------------------
    public function agregar()
    {

        if(validate::getInt('guardar')==1)
        {

			//cargo modelo persona
			$this->_persona = $this->loadModel('persona');

			$licencia = (validate::getPostParam('licencia'))?validate::getPostParam('licencia'):'NA';
			//creamos promero la persona
			//-----------------------------
			$datos_persona = array(
				"nacionalidad" =>validate::getPostParam('nacionalidad'),
                "cedula"=>		validate::getPostParam('cedula'),
                "pri_nom"=>  	validate::getPostParam('pri_nombre'),
                "seg_nom"=>  	validate::getPostParam('seg_nombre'),
                "pri_ape"=>  	validate::getPostParam('pri_apellido'),
                "seg_ape"=>  	validate::getPostParam('seg_apellido'),
                "direccion"=>   validate::getPostParam('direccion'),
                "local"=>		validate::getPostParam('local'),
                "celular"=>		validate::getPostParam('celular'),
                "fecha_nac"=>	validate::getPostParam('fecha_nac'),
                "licencia"=>	$licencia,
                "sexo"=>		validate::getPostParam('sexo'),
                "estado_civil"=>validate::getPostParam('estado_civil'),
                "lugar_nac"=>  	validate::getPostParam('lugar_nac'),
                "sector"=>		validate::getInt('sector'),
                "parroquia"=>	validate::getInt('parroquia'),
                "estado"=>		validate::getInt('estado'),
                "municipio"=>	validate::getInt('municipio'),
                "empresa"=> $this->_empresa,
				        "correo"=>		validate::getPostParam('email')
			);

			//print_r($datos_persona); exit();
			if($this->_persona->incluir($datos_persona))
			{
				$ult_persona = $this->_persona->ult_persona_reg();
				$cedula = validate::getPostParam('nacionalidad').validate::getPostParam('cedula');

				$parametro = array(
					"persona"  =>$ult_persona,
					"condicion"=>validate::getPostParam('condicion'),
					"correo"   =>validate::getPostParam('email'),
					"alias"    =>$cedula,
					"pregunta" =>validate::getInt('pregunta'),
					"respuesta"=>validate::getPostParam('respuesta'),
					"empresa"  =>$this->_empresa,
					"rol"      =>validate::getInt('rol'));

				if(!$this->_usuario->insertar($parametro))
				{
					Logger::errorLog("Error registrando Persona",'ERROR');
					$this->redireccionar('seguridad/usuario/index');
					exit();
				}//fin del if insertar usuario
				else
					{
						$ult_usuario = $this->_usuario->ult_usuario_reg();

						$contraseña = validate::getPostParam('pwd1');

						$clave = array(
						"fcreado"=>date('Y-m-d'),
						"fexpira"=>date('Y-m-d',strtotime ( '+'.TIME_KEY.' day' , strtotime ( date('Y-m-d')) )),
						"estado"=>1,
						"clave"=>Hash::getHash('md5',$contraseña,HASH_KEY),
						"usuario"=>$ult_usuario);
						//creo usuario
						if(!$this->_usuario->incluirUsurioClave($clave))
						{
							$this->_view->error = "Error estableciendo clave de seguridad .....";
							Logger::errorLog("Error registrando contraseña de seguridad",'ERROR');
							$this->_view->renderizar('agregar','seguridad','usuario');
							exit();
						}else
							{
								//$this->getMensaje('confirmacion','Usuario creado Exitosamente ....');
								$this->redireccionar('seguridad/usuario/index');
								exit();
							}
						$this->redireccionar('seguridad/usuario/');
						exit();
				}

			}else
			{
				Logger::errorLog("Error registrando Persona",'ERROR');
				$this->redireccionar('seguridad/usuario/index');
				exit();
				//$this->_view->renderizar('agregar','seguridad','usuario');
				//exit();
			}

        }else
        {
            $this->_view->title = "Agregar Usuario";
            $this->_view->setJs(array('usuario_js'));
            $this->_view->setJsPlugin(array('validaciones'));

            //se carga rol
            $this->_rol = $this->loadModel('role');
            $role = $this->_rol->get("_table");
            //print_r($role);
            $this->_view->rol = $role;

            //se carga estado
            //$this->_estado = $this->loadModel('estado','configuracion');
            //$this->_view->esta = $this->_estado->cargarEstado();

            //se carga pregunta de seguridad
            $this->_pregunta = $this->loadModel('pregunta');
            $pregunta = $this->_pregunta->get("_table");
            $this->_view->pregunta = $pregunta['datos'];



            //$configuracion = $this->_conf->cargarConfiguracion();
            //$this->_view->nro_pregunta = $configuracion['nro_pregunta_seg'];
            //print_r($configuracion);
            $this->_view->renderizar('agregar','seguridad','usuario');
            exit();
        }
    }

    public function editar($id)
    {
        if(validate::getPostParam('guardar')==2)
        {
            $parametro = array(
                "persona" =>validate::getInt('id_persona'),
                "condicion"=>validate::getPostParam('condicion'),
                "correo"=>validate::getPostParam('correo'),
                "alias"=>validate::getPostParam('login_usuario'),
                "role"=>validate::getInt('rol'),
                "id"=>  validate::getInt('usuario')
            );
            if(!$this->_usuario->editar($parametro))
            {
                Logger::errorLog("Error actualizando datos de usuario",'ERROR');

            }

			$pregunta = array(
				   "pregunta"=>validate::getInt('pregunta'),
				   "respuesta"=>validate::getPostParam('respuesta'),
				   "usuario"=> validate::getInt('usuario')
					);
			//print_r($pregunta);exit();
			if($this->_usuario->editarUsuarioPregunta($pregunta))
			{
				$this->redireccionar('seguridad/usuario/index');
				exit();

			}else
			{
				Logger::errorLog("Error registrando Persona",'ERROR');
			}

            $this->redireccionar('seguridad/usuario/index');
            exit();
        }
        else
        {
            $this->_view->title = "Editar Usuario";
            $this->_view->setJs(array('usuario_js'));
            $this->_view->setJsPlugin(array('validaciones'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();
            if($id)
            {
                $this->_rol = $this->loadModel('role');
                $this->_pregunta = $this->loadModel('pregunta');
                $this->_view->usuario = $this->_usuario->buscar($id);
                $this->_view->rol = $this->_rol->cargarRoles();
                $this->_view->pregunta = $this->_pregunta->cargarPregunta(FALSE,$this->_empresa);
                $this->_view->pregunta_usu = $this->_usuario->cargarPreguntaUsuario($id);
                $this->_view->paginacion = $paginador->getView('paginacion','seguridad/usuario/editar');
                $this->_view->renderizar('editar','seguridad');
                exit();
            }
        }
    }

    public function permisoUsuario($usuario)
    {

        // cargo el modelo de role
        $this->_rol = $this->loadModel('role');
        // cargo el modelo permiso
        $this->_permiso = $this->loadModel('permiso');

        //die($usuario);
        $permisos = array();
        if($usuario)
        {
            $arr_usuario =$this->_usuario->buscar($usuario);
            $this->_view->usuario = $arr_usuario;
			//print_r($arr_usuario);exit();
            //==================================================================
            //se carga primero por rol asignado al usuario
            //==================================================================
            $roleUsuario = $arr_usuario['role_id'];

			$_recursosRol = $this->_rol->cargarRecursoRole($roleUsuario);
            $_recursosUsuario = $this->_usuario->cargarRecursoUsuario($usuario);
            //print_r($_recursosRol);exit();
			//print_r($_recursosUsuario);exit();
            for( $i = 0;$i < count($_recursosRol);$i++)
            {
               $datos = array();
               $existe = false;


                        $permisosRol = $this->_permiso->cargarPermisoRecursoRole($_recursosRol[$i]['recurso_id'],$roleUsuario);
                        //print_r($permisosRol);exit();
                        if(count($permisosRol)>0)
                        {
                            $datos = array();
                            for($j=0;$j < count($permisosRol);$j++)
                            {
                                if($permisosRol[$j]['estatus']==9)
                                    {
                                        $datos[] = array("nombre_permiso"=>$permisosRol[$j]['nombre_permiso'],"estatus"=>9);
                                    }  else {
                                        $datos[] = array("nombre_permiso"=>$permisosRol[$j]['nombre_permiso'],"estatus"=>1);
                                    }
                            }
                        }


                    $permisos[] = array(
                            "key"=>$_recursosRol[$i]['clave'],
                            "recurso"=>$_recursosRol[$i]['nombre_recurso'],
                            "permiso"=>$datos,
                            "id_recurso"=>$_recursosRol[$i]['recurso_id'],
                            "ult_fecha"=>$_recursosRol[$i]['fecha_ult_act'],
                            "heredado"=>true
                            );

            }
            //==================================================================
            //ahora cargo los permisos exclusivos del usuario
            //==================================================================
            for( $i = 0;$i < count($_recursosUsuario);$i++)
            {
                $datos = array();
                $existe = FALSE;

                    $permisoUsuario = $this->_permiso->cargarPermisoRecursoUsuario($_recursosUsuario[$i]['recurso_id'],$usuario);

                    if(count($permisoUsuario)>0)
                    {
                        for($x = 0;$x < count($permisoUsuario);$x++)
                        {
                            $datos[] = array("nombre_permiso"=>$permisoUsuario[$x]['nombre_permiso'],"estatus"=>$permisoUsuario[$x]['estatus']);
                        }
                    }

                    $permisos[] = array(
                            "key"=>$_recursosUsuario[$i]['clave'],
                            "recurso"=>$_recursosUsuario[$i]['nombre_recurso'],
                            "permiso"=>$datos,
                            "id_recurso"=>$_recursosUsuario[$i]['recurso_id'],
                            "ult_fecha"=>$_recursosUsuario[$i]['fecha_ult_act'],
                            "heredado"=>FALSE
                            );

                //}

            }

        }
        array_multisort($permisos);
        //print_r($permisos);        exit();
        $this->_view->title = "Permiso de Usuario : ".$arr_usuario['alias_usuario'];
        //$this->_view->lista = $permisoUsuario;
        $this->_view->lista = $permisos;
        $this->_view->renderizar('permiso','seguridad');
        exit();
    }
    // metodo del controlador que carga un formulario para crear un nuevo recurso de usuario
    public function nuevoRecursoUsuario($usuario)
    {

        $this->_view->setJs(array('usuario_js'));
		//cargo modelo recurso
        $_recurso = $this->loadModel('recurso');
        // cargo el modelo permiso
        $this->_permiso = $this->loadModel('permiso');

        $datosUsuario = $this->_usuario->buscar($usuario);
        $recursoUsuario = $_recurso->noRecursoUsuario($usuario,$this->_empresa);
        //$recursoRole = $_recurso->noRecursoRole($datosUsuario['role_id']);

        //$this->_view->recurso = array_merge($recursoUsuario,$recursoRole);
        $this->_view->recurso = $recursoUsuario;
        $permiso = $this->_permiso->cargarPermiso(false,$this->_empresa);

        $this->_view->lista = $permiso;
        $this->_view->nro_permiso = count($permiso);
        $this->_view->usuario = $usuario;
        $this->_view->title = "Asignacion de Recurso";
        $this->_view->renderizar('recurso','seguridad');
        exit();

    }
    // metodo del controlador que carga formulario para editar un permiso de recurso de usuario
    public function editarPermisoRecursoUsuario($usuario,$recurso)
    {
        $this->_view->setJs(array('usuario_js'));
        $_recurso = $this->loadModel('recurso');
        //CARGA TODOS LOS RECURSOS EXISTENTES
        $this->_view->recursos = $_recurso->cargarRecurso();
        //CARGA LOS PERMISOS permitidos del usuario
        $permisos = $this->_permiso->cargarPermisoRecursoUsuario($recurso,$usuario);
        $noPermisos = $this->_permiso->cargarNoPermisoRecursoUsuario($recurso);
        $permiso = array_merge($permisos,$noPermisos);
        $this->_view->lista = $permiso;
        $this->_view->nro_permiso = count($permiso);
        $this->_view->usuario = $usuario;
        $this->_view->recurso = $recurso;
        $this->_view->titulo = "Editar permisos de Usuario";
        $this->_view->renderizar('editarRecurso','seguridad');
        exit();


    }
    //metodos que guarda o actualiza los permisos de recurso de usuario
    public function guardarRecursoUsuario()
    {

        // para el caso de guardar
        if(validate::getInt('guardar')==1)
        {
            //print_r($_POST);exit();
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
                        "estatus"=>validate::getInt('estatus'.$i),
                        "usuario"=>validate::getInt('usuario'),
                        "empresa"=>$this->_empresa
                    );
                }

            }

            //print_r($arrPermiso);exit();
                    // cargo el modelo permiso
			$this->_permiso = $this->loadModel('permiso');

            if($this->_permiso->incluirPermisoRecursoUsuario($arrPermiso))
            {
                $this->redireccionar('seguridad/usuario/permisoUsuario/'.validate::getInt('usuario'));
                exit();
            }else
                {
                    Logger::errorLog("Error registrando recurso de usuario",'ERROR');
                    $this->redireccionar('seguridad/usuario/permisoUsuario/'.validate::getInt('usuario'));
                    exit();
                }

        }

        //para el caso de actializar
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
                        "estatus"=>validate::getInt('estatus'.$i),
                        "usuario"=>validate::getInt('usuario')
                    );
                }

            }
                    // cargo el modelo permiso
			$this->_permiso = $this->loadModel('permiso');
            if($this->_permiso->editarPermisoRecursoUsuario($arrPermiso))
            {
                $this->redireccionar('seguridad/usuario/permisoUsuario/'.validate::getInt('usuario'));
                exit();
            }else
            {
                Logger::errorLog("Error editando recurso de usuario",'ERROR');
                //$this->getMensaje('error','Error Editando Permisos de usuario ....');
                $this->redireccionar('seguridad/usuario/permisoUsuario/'.validate::getInt('usuario'));
            }

        }

    }

	public function eliminarRecursoUsuario($usuario,$recurso)
	{
			$this->_usuario->eliminarRecurso($usuario,$recurso);
			$this->redireccionar('seguridad/usuario/permisoUsuario/'.$usuario);
	}
	public function buscar($item = FALSE)
    {
        $pagina = 1;
        $this->_view->setJs(array('usuario'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();

        if($item)
        {
            $this->_view->datos = $paginador->paginar($this->_usuario->cargarUsuario(validate::getPostParam('buscar')),$pagina);
        }
        if($this->getPostParam('buscar'))
        {
            $this->_view->datos = $paginador->paginar($this->_usuario->cargarUsuario(validate::getPostParam('buscar')),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','seguridad/usuario/index');
        $this->_view->renderizar('index','seguridad');
        exit();

    }

    public function mantenimientoUsuario($id = FALSE)
    {
			if(validate::getInt('guardar')==1)
			{
				$registro = registry::getInstancia();
				//$guachiman = $registro->_guachiman;

				$accion = validate::getInt('accion');
				$id = validate::getInt('id');
				//switch($accion)
				//{
				//	case 1:
				//			$guachiman->blockUsuario($id);
				//			$this->getMensaje("confirmacion","Usuario Bloqueado ........");
				//	break;
				//	case 2:
				//			$guachiman->desblockUsuario($id);
				//			$this->getMensaje("confirmacion","Usuario Desbloqueado .......");
				//	break;
				//	case 3:
				//			$guachiman-> excluirUsuario($id);
				//			$this->getMensaje("confirmacion","Usuario Desconectado .........");

				//	break;

				//}

				//$this->redireccionar('seguridad/usuario/');
				//exit();

			}

			$this->_view->setCssPlugin(array('legend'));
			$this->_view->setJsPlugin(array('chart','legend','validaciones'));
			$this->_view->setJs(array("mantenimiento"));

			if($id)
			{
				 $usuario = $this->_usuario->buscar($id);

			}

			$this->_view->usuario = $usuario;
			$this->_view->titulo = "mantenimiento de Usuario: ".$usuario['pri_nombre_persona'].'-'.$usuario['pri_apellido_persona'] ;
			$this->_view->renderizar('mantenimiento','seguridad');
			exit();
    }

    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    public function eliminarUsuario()
    {
        echo json_encode($this->_usuario->eliminar(Validate::getPostParam('codigo')));
    }

    /// funcionalidad json
    public function buscarUsuario()
    {
        echo json_encode($this->_usuario->buscarUsuario(Validate::getPostParam('cedula'),Validate::getPostParam('tipo')));
    }

    //carga los datos de la persona de forma asincrona
    public function buscarPersona()
    {
        $persona = $this->loadModel('persona');
        echo json_encode($persona->buscarPersona(Validate::getPostParam('cedula'),Validate::getPostParam('tipo')));
    }

    public function comprobarAlias()
    {
        echo json_encode($this->_usuario->buscarAlias(Validate::getPostParam('nombre')));
    }
    public function comprobarCorreo()
    {
        echo json_encode($this->_usuario->buscarCorreo(Validate::getPostParam('correo')));
    }

    public function grafControlAcceso()
    {
            $total =0;
            $valores = array();
            //$colores = array('#FDB45C','#46BFBD','#F7464A','#E08283','#674172','#AEA8D3','#446CB3','#22A7F0','#65C6BB','#2ECC71','#D35400','#BDC3C7','#E9D460');

            // busco los productos de la requisiciones a las cuales tiene acceso el usuario
            $usuario = validate::getPostParam('usuario');
            $meses = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');


            $datos=array();
            for($i = 1 ; $i <= count($meses);$i++)
            {
                            $admicion = $this->_usuario->controlAccesoAdmicion($usuario,$i);
                            $desconexion = $this->_usuario->controlAccesoDesconexion($usuario,$i);

                            $dat_admicion[] = ($admicion['total']>0)?$admicion['total']:1;
                            $dat_desconexion[] = ($desconexion['total']>0)?$desconexion['total']:1;


            }

            $datos[]=array("fillColor"=>"rgba(151,249,190,0.5)","strokeColor"=>"rgba(255,255,255,1)","pointColor"=>"rgba(220,220,220,1)","pointStrokeColor"=>"#fff","data"=>$dat_admicion);
            $datos[]=array("fillColor"=>"rgba(252,147,65,0.5)","strokeColor"=>"rgba(255,255,255,1)","pointColor"=>"rgba(173,173,173,1)","pointStrokeColor"=>"#fff","data"=>$dat_desconexion);


                    $valores=array("labels"=>$meses,"datasets"=>$datos);
            echo json_encode($valores);
    }

}
