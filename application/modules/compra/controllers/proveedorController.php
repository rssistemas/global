<?php
class proveedorController extends compraController
{
    private $_proveedor;
    private $_estado;
    private $_municipio;
    private $_parroquia;
    private $_sector;
    private $_tipo;
	
    public function __construct() {
        parent::__construct();
        $this->_proveedor = $this->loadModel('proveedor');
        $this->_tipo = $this->loadModel('tipoProveedor','archivo');
        $this->_sector = $this->loadModel('sector','configuracion');
        $this->_parroquia = $this->loadModel('parroquia','configuracion');
        $this->_municipio = $this->loadModel('municipio','configuracion');
        $this->_estado = $this->loadModel('estado','configuracion');
    }
    
    /* Cuando se realiza el llamado al maestro principal
    * http://localhost/pdval/archivo/nombreDeLaVista/index/
    * se ejecuta la funcion index() del controlador del maestro */
    public function index($pagina = 1)
    {
        $this->_view->title = "Proveedores ";
        $this->getLibrary('paginador');
        $this->_view->setJs(array('proveedor'));
        $this->_view->setJsPlugin(array('validaciones'));
        $paginador = new Paginador();
        //$paginador->setRango(FALSE);
        if(validate::getPostParam('busqueda'))
        {
            $proveedor = $paginador->paginar($this->_proveedor->cargarProveedor(validate::getPostParam('busqueda')),$pagina,10);         
        }
        else
        {
            $proveedor =  $paginador->paginar($this->_proveedor->cargarProveedor(),$pagina,10);
        }
        $this->_view->proveedor = $proveedor;
        $this->_view->paginacion = $paginador->getView('paginacion','compra/proveedor/index');	
        $this->_view->renderizar('index');
        exit();
        
    }
	//-------------------------------------------------        
    // ----- METODO PARA MOSTRAR LA VISTA DE agregar
    //-------------------------------------------------
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            $datos_proveedor = array(
                "nacionalidad" =>$this->getPostParam('nacionalidad'),
                "cedula"=>$this->getPostParam('cedula'),
                "nom_pro"=>  $this->getPostParam('nombre_pro'),
                "nom_con"=>  $this->getPostParam('nombre_con'),
                "telf_pro"=>  $this->getPostParam('telf_pro'),
                "telf_con"=>  $this->getPostParam('telf_con'),
                "direccion"=>    $this->getPostParam('direccion'),
                "correo_pro"=>$this->getPostParam('correo_pro'),
                "tipo"=>$this->getPostParam('tipo'),
                "sector"=>$this->getInt('sector') );
				
            if($this->_proveedor->incluir($datos_proveedor))
            {
                $mensaje="CONFIRMACIÓN DE REGITRO - Registro nuevo guardado exitosamente...";
            }
            else
            {
                $this->_proveedor->regLog();
                $this->_view->error = "Erroor guardando...";
                $mensaje="CONFIRMACIÓN DE REGISTRO - ERROR al guardar el nuevo registro...";
            }
            $this->redireccionar('compra/proveedor/','compra');
        }
        
            $this->_view->title = "Nuevo Proveedor";
            $this->_view->setJs(array('proveedor'));
            $this->_view->setJsPlugin(array('validaciones'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();
            $this->_view->tipo = $this->_tipo->cargarTipoProveedor();
            $this->_view->esta = $this->_estado->cargarEstado();
            $this->_view->paginacion = $paginador->getView('paginacion','archivo/proveedor/agregar');
            $this->_view->renderizar('agregar','archivo');
        
    }
    // ----- METODO PARA MOSTRAR LA VISTA DE EDICION Y EJECUTAR LOS CAMBIOS
    public function editar($id=false)
    {
        if($this->getPostParam('guardar')==2)
        {
            $datos_proveedor = array(
                "id"=>$this->getInt('id'),
                "nacionalidad" =>$this->getPostParam('nacionalidad'),
                "cedula"=>$this->getPostParam('cedula'),
                "nom_pro"=>  $this->getPostParam('nombre_pro'),
                "nom_con"=>  $this->getPostParam('nombre_con'),
                "telf_pro"=>  $this->getPostParam('telf_pro'),
                "telf_con"=>  $this->getPostParam('telf_con'),
                "direccion"=>    $this->getPostParam('direccion'),
                "correo_pro"=>$this->getPostParam('correo_pro'),
                "tipo"=>$this->getPostParam('tipo'),
                "sector"=>$this->getInt('sector') );
            if($this->_proveedor->modificar($datos_proveedor))
            {
                $this->_view->error = "Registro editado guardado...";
            }
            else
            {  // si falla el insertar proveedor
                $this->_view->error = "Error guardando edicion...".$this->_proveedor->regLog();
            }
            $this->redireccionar('archivo/proveedor/','archivo');
        }
        else
        {
            $this->_view->titulo = "Editar Proveedor";
            $this->_view->setJs(array('proveedor'));
            $this->_view->setJsPlugin(array('validaciones'));
            $this->getLibrary('paginador');                 
            $paginador = new Paginador();
            $this->_view->proveedor = $this->_proveedor->buscar($id);
            $this->_view->cant_uso = $this->_proveedor->verificar_uso($id);
            $this->_view->tipo = $this->_tipo->cargarTipo();
            $this->_view->sect = $this->_sector->cargarSector();
            $this->_view->parr = $this->_parroquia->cargarParroquia();
            $this->_view->muni = $this->_municipio->cargarMunicipio();
            $this->_view->esta = $this->_estado->cargarEstado();
            $this->_view->paginacion = $paginador->getView('paginacion','archivo/proveedor/editar');        
            $this->_view->renderizar('editar','archivo');
        }
    }
    
       /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function estatusProveedor()
    {
        echo json_encode($this->_proveedor->estatusProveedor($this->getInt('valor'),$this->getInt('estatus')));
    }
    //COMPRUEBA SI EL REGISTRO FUE USADO EN OTRAS TABLAS
    public function comprobarUso()
    {
        echo json_encode($this->_proveedor->verificar_uso($this->getPostParam('valor')));
    }
    
   //COMPRUEBA SI EL NUMERO DE RIF YA EXISTE EN PROVEEDOR
    public function comprobarProveedor()
    {
        echo json_encode($this->_proveedor->verificar_existencia($this->getPostParam('tipo'),$this->getPostParam('rif')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarProveedor()
    {
        echo json_encode($this->_proveedor->buscar($this->getInt('valor')));
    }
	
	public function cargarProveedor()
    {
        echo json_encode($this->_proveedor->cargarProveedor($this->getPostParam('valor')));
    }
	
  
}

