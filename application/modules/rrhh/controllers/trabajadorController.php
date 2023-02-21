<?php
class trabajadorController extends rrhhController
{
    private $_trabajador;
    private $_persona;
    private $_cargo;
    private $_estado;
    private $_municipio;
    private $_parroquia;
    private $_sector;
    private $_ultimo_registro;
    private $_deposito;
	private $_dpto;
    public function __construct() {
        parent::__construct();
        $this->_ultimo_registro = 0;
        $this->_persona = $this->loadModel('persona','seguridad');
        $this->_trabajador = $this->loadModel('trabajador','rrhh');
        $this->_cargo    = $this->loadModel('cargo');
        $this->_estado   = $this->loadModel('estado','configuracion');
        $this->_municipio= $this->loadModel('municipio','configuracion');
        $this->_parroquia= $this->loadModel('parroquia','configuracion');
        $this->_sector   = $this->loadModel('sector','configuracion');
        $this->_deposito = $this->loadModel('deposito','almacen');
		$this->_dpto = $this->loadModel('departamento');
		
    }

    /* Cuando se realiza el llamado al maestro principal
     http://localhost/pdval/archivo/nombreDeLaVista/index/
     se ejecuta la funcion index() del controlador del maestro */
    
	public function index($pagina = 1)
    {
		
	   $this->_view->title = "Trabajador";
        $this->_view->setJs(array('trabajador'));
        $this->getLibrary('paginador');                 
        $paginador = new Paginador();
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_trabajador->cargarTrabajador_index($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_trabajador->cargarTrabajador_index(),$pagina);        
        }
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','rrhh/trabajador/index');        
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','rrhh');
		exit();
    }

    // ----- METODO PARA MOSTRAR LA VISTA DE agregar
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            $datos_persona = array(
                "nacionalidad" =>$this->getPostParam('nacionalidad'),
                "cedula"=>$this->getPostParam('cedula'),
                "pri_nom"=>  $this->getPostParam('pri_nombre'),
                "seg_nom"=>  $this->getPostParam('seg_nombre'),
                "pri_ape"=>  $this->getPostParam('pri_apellido'),
                "seg_ape"=>  $this->getPostParam('seg_apellido'),
                "direccion"=>    $this->getPostParam('direccion'),
                "local"=>$this->getPostParam('local'),
                "celular"=>$this->getPostParam('celular'),
                "fecha_nac"=>$this->getPostParam('fecha_nac'),
                "licencia"=>$this->getPostParam('licencia'),
                "sexo"=>$this->getPostParam('sexo'),
                "estado_civil"=>$this->getPostParam('estado_civil'),
                "lugar_nac"=>  $this->getPostParam('lugar_nac'),
                "sector"=>$this->getInt('sector') );
            if($this->_persona->incluir($datos_persona))
            {
                $ult_persona = $this->_persona->ult_persona_reg();
                $licencia = 0;
                if($this->getPostParam('licencia'))
                    $licencia = $this->getPostParam('licencia');
                
                $grado =0; 
                if($this->getPostParam('grado_licencia'))
                        $grado = $this->getPostParam('grado_licencia');
                
                $datos_trabajador = array(
                    "correo"=>$this->getPostParam('correo'),
                    "persona"=> $ult_persona,
                    "cargo"=>$this->getInt('cargo'),
                    "licencia"=>$licencia,
                    "grado"=>$grado,
                    "ubicacion"=>$this->getInt('ubicacion')
                    );
                if($this->_trabajador->incluir($datos_trabajador))
                {
                    $this->redireccionar('archivo/trabajador/index');
                    exit();
                }
                else
                {
                    $this->_view->error = "Error guardando trabajador .....".$this->_trabajador->regLog();
                    $this->_view->renderizar('agregar','archivo');
                    exit();
                }
            }
            else
            {
                $this->_view->error = "Erroor guardando...".$this->_persona->regLog();
//                $this->_view->renderizar('agregar','archivo');
//                exit();
            }
        }
        else
        {
            $this->_view->titulo = "Agregar Trabajador";
            $this->_view->setJs(array('trabajador'));
            $this->_view->tipo = $this->_cargo->cargarCargo();
            $this->_view->esta = $this->_estado->cargarEstado();
            $this->_view->muni = $this->_municipio->cargarMunicipio();
            $this->_view->parr = $this->_parroquia->cargarParroquia();
            $this->_view->sect = $this->_sector->cargarSector();
            $this->_view->dpto = $this->_dpto->cargarDepartamento();
            $this->_view->renderizar('agregar','rrhh');
            exit();
        }
    }
	
	
	
    // ----- METODO PARA MOSTRAR LA VISTA DE EDICION Y EJECUTAR LOS CAMBIOS
    public function editar($id=false)
    {
        if($this->getPostParam('guardar')=='ed')
        {
            $datos_persona = array("id"=>$this->getInt('id'),
                "nacionalidad"=>$this->getPostParam('nacionalidad'),
                "cedula"=> $this->getPostParam('cedula'),
                "pri_nom"=> $this->getPostParam('pri_nombre'),
                "seg_nom"=> $this->getPostParam('seg_nombre'),
                "pri_ape"=> $this->getPostParam('pri_apellido'),
                "seg_ape"=> $this->getPostParam('seg_apellido'),
                "direccion"=> $this->getPostParam('direccion'),
                "local"=>$this->getPostParam('local'),
                "celular"=>$this->getPostParam('celular'),
                "fecha_nac"=>$this->getPostParam('fecha_nac'),
                "licencia"=>$this->getPostParam('licencia'),
                "sexo"=>$this->getPostParam('sexo'),
                "estado_civil"=>$this->getPostParam('estado_civil'),
                "lugar_nac"=>  $this->getPostParam('lugar_nac'),
                "sector"=>$this->getInt('sector'));
            if($this->_persona->modificar($datos_persona))
            {
                $datos_trabajador = array(
                    "correo"=>$this->getPostParam('correo'),
                    "persona"=>$this->getInt('id'),
                    "cargo"=>$this->getPostParam('cargo') );
                if($this->_trabajador->modificar($datos_trabajador))
                {
                    $this->redireccionar('archivo/trabajador/index');
                    exit();
                }
                else
                {
                    $this->_view->error = "Error EDITANDO DATOS DEL TRABAJADOR.".$this->_trabajador->regLog();
                    //$this->_view->renderizar('editar','archivo');
                    $this->redireccionar('archivo/trabajador/index');
                    //exit();
                }
            }
            else
            {  // si falla el insertar persona
                $this->_view->error = "Erroor guardando edicion...".$this->_persona->regLog();
//                $this->_view->renderizar('agregar','archivo');
//                exit();
            }
        }
        else
        {
            $this->_view->title = "Edición de Datos del Trabajador";
            $this->_view->setJs(array('trabajador'));
            $this->getLibrary('paginador');                 
            $paginador = new Paginador();
            $this->_view->sect = $this->_sector->cargarSector();
            $this->_view->parr = $this->_parroquia->cargarParroquia();
            $this->_view->muni = $this->_municipio->cargarMunicipio();
            $this->_view->esta = $this->_estado->cargarEstado();
            $this->_view->tipo = $this->_cargo->cargarCargo();
			$this->_view->dpto = $this->_dpto->cargarDepartamento();
            $this->_view->trabajador = $this->_trabajador->buscar($id);
            $this->_view->paginacion = $paginador->getView('paginacion','archivo/trabajador/editar');        
            $this->_view->renderizar('editar','archivo');
            //exit();
        }
    }
    
    public function ult_persona_reg()
    {
        return $this->_ultimo_registro;
    }

    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function eliminarTrabajador()
    {
        echo json_encode($this->_trabajador->desactivar($this->getInt('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarTrabajador()
    {
        echo json_encode($this->_persona->verificar_existencia($this->getPostParam('tipo'),$this->getPostParam('cedula')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarTrabajador()
    {
         echo json_encode($this->_trabajador->buscar($this->getPostParam('valor')));
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO