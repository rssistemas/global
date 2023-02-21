<?php
class empresaController extends configuracionController
{
    private $_empresa;
    private $_impuesto;
    private $_moneda;
    public function __construct() {
        parent::__construct();
        $this->_empresa = $this->loadModel('empresa');
        //$this->_impuesto = $this->loadModel('impuesto');
        $this->_moneda = $this->loadModel('moneda');
    }

    public function index($pagina = 1)
    {
    	$this->_acl->acceso('empresa_consultar',105,'archivo-empresa-index');

        //$empresa = session::get('actEmp');

        //define el titulo de la presente vista
        $this->_view->title = "Empresas";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('empresa'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();

        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_empresa->cargarEmpresaUsuario(session::get('id_usuario'),validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_empresa->cargarEmpresaUsuario(session::get("id_usuario")),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/empresa/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','configuracion','Empresas');
        exit();
    }


    // ----- METODO PARA MOSTRAR LA VISTA DE agregar
    public function agregar()
    {

        if(validate::getInt('guardar')==1)
        {
           //$this->_acl->acceso('empresa_agregar',105,'archivo-empresa-index');

		         $datos = array(
                "nombre"   =>  validate::getPostParam('razon_social'),
                "direccion"=>  validate::getPostParam('direccion'),
                "telefono" =>  validate::getPostParam('telefono'),
                "correo"   =>  validate::getPostParam('correo'),
                "tipo_empresa"=>validate::getPostParam('tipo_empresa'),
                "prestaciones"=>validate::getPostParam('Prestaciones'),
                "moneda_principal"=>validate::getInt('moneda'),
                "moneda_secundaria"=>validate::getInt('moneda2'),
                "licencia_actividad"=>validate::getPostParam('licencia_actividad'),
                "rif"               =>validate::getPostParam('rif'),
                "nit"               =>validate::getPostParam('nit'),
                "formato_factura"   =>validate::getPostParam('formato_factura'),
                "agente_retencion"  =>validate::getInt('agente_retencion'),
                //"comentario"=> validate::getPostParam('comentario'),
                "usuario"=> session::get("id_usuario")
                );

            if(!$this->_empresa->incluir($datos))
            {
                Logger::errorLog("Error registrando Empresa",'ERROR');
            }else {
                  if($_FILES['files'])
                  {
                      $tipo_archivo = $_FILES['files']['type'];
                      $ruta_servidor = APP_PATH.'public'.DS.'img'.DS.'documentos'.DS;
                      $tamano_archivo = $_FILES['files']['size'];
                      //die($tipo_archivo);
                      $tipoPermitidos = array('image/jpg', 'image/gif', 'image/png','application/pdf');
                      if (in_array($tipo_archivo, $tiposPermitidos))
                      {
                          $nom = $ruta_servidor.$nombre_archivo.self::extencionFile($tipo_archivo);
                          if(move_uploaded_file($_FILES['files']['tmp_name'],$nom))
            							{
            								//$this->redireccionar('clientes/cliente/proyectoCliente/'.validate::getInt('cliente'));
            								exit();
            							}else
            								{
            									//die('error1');
            									$this->_view->error = "Error cargando archivo a servidor  .....";
            								}
                      }else {
                          $this->_view->error = "Archivo no permitido ....";
                      }
                  }

            }



            $this->redireccionar('configuracion/empresa/index/','configuracion');
            exit();
        }

			//$this->_view->impuesto = $this->_impuesto->cargarImpuesto();
      $this->_view->moneda = $this->_moneda->cargarMoneda();
      $this->_view->setJs(array('empresa'));
      $this->_view->title="Agregar Empresa";
			$this->_view->renderizar('agregar','configuracion','Empresas');
			exit();

    }
    public function editar($id=false)
    {

        if(validate::getInt('guardar')==2)
        {
            //$this->_acl->acceso('empresa_editar',105,'archivo-empresa-index');

            $datos = array(
               "id"=>validate::getInt('id'),
               "nombre"   =>  validate::getPostParam('razon_social'),
               "direccion"=>  validate::getPostParam('direccion'),
               "telefono" =>  validate::getPostParam('telefono'),
               "correo"   =>  validate::getPostParam('correo'),
               "tipo_empresa"=>validate::getPostParam('tipo_empresa'),
               "prestaciones"=>validate::getPostParam('Prestaciones'),
               "moneda_principal"=>validate::getInt('moneda'),
               "moneda_secundaria"=>validate::getInt('moneda2'),
               "licencia_actividad"=>validate::getPostParam('licencia_actividad'),
               "rif"               =>validate::getPostParam('rif'),
               "nit"               =>validate::getPostParam('nit'),
               "formato_factura"   =>validate::getPostParam('formato_factura'),
               "agente_retencion"  =>validate::getInt('agente_retencion'),
               //"comentario"=> validate::getPostParam('comentario'),
               "usuario"=> session::get("id_usuario")
               );

            if(!$this->_empresa->modificar($datos))
            {
                Logger::errorLog("Error Editando Empresa",'ERROR');

            }
            $this->redireccionar('configuracion/empresa/index/','archivo');
            exit();

        }

        $this->_view->setJs(array('empresa'));
        $this->_view->title="Editar Empresa";

        if($id)
        {
            $this->_view->datos = $this->_empresa->buscar($id);

            $this->_view->moneda = $this->_moneda->cargarMoneda();

        }

    	$this->_view->renderizar('editar','configuracion','Empresas');
	    exit();


    }


    private static function extencionFile($valor)
  	{

  		switch($valor)
  		{
  			case $valor == 'image/jpg':
  				return '.jpg';
  			break;
  			case $valor == 'image/png':
  				return '.png';
  			break;
  			case $valor == 'image/gif':
  				return '.gif';
  			break;
  			case $valor == 'application/pdf':
  				return '.pdf';
  			break;
  		}

  	}


    public function personal($pagina = 1)
    {

        $this->_acl->acceso('empresa_consultar',105,'archivo-empresa-index');


        $empresa = session::get('actEmp');


        //define el titulo de la presente vista
        $this->_view->title = "Preferecias de la Empresas";
        //carga el archivo JS del maestro
        //$this->_view->setJs(array('empresa'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();

        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_empresa->cargarUsuarioColaborador(validate::getPostParam('busqueda'),$empresa[0]['id_empresa'] ),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_empresa->cargarUsuarioColaborador(false,$empresa[0]['id_empresa']),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/empresa/personal');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $usuario = $this->loadModel('usuario','seguridad');
        $this->_view->usuarios = $usuario->cargarUsuarioEmpresa($empresa[0]['id_empresa']);

        $this->_view->renderizar('personal','configuracion','Empresas');
        exit();





    }


    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function eliminarDeposito()
    {
        echo json_encode($this->_deposito->desactivar(validate::getInt('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarEmpresa()
    {
        echo json_encode($this->_empresa->comprobarEmpresa(validate::getPostParam('nombre')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarEmpresa()
    {
        echo json_encode($this->_empresa->buscar(validate::getInt('valor')));
    }



}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
?>
