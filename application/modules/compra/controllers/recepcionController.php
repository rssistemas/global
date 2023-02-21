<?php
class recepcionController extends almacenController
{
    private $_recepcion;
    private $_marca;
    private $_proveedor;
    private $_deposito;
    private $_producto;
    private $_despacho;
    private $_planificacion;
    private $_solicitud;
    private $_incidencia; 
    private $_departamento;
    private $_presentacion;
    private $_unidad;
    private $_orden;
    private $_documento;
	
    public function __construct() {
        parent::__construct();
        $this->_recepcion = $this->loadModel('recepcion');
       
        $this->_proveedor = $this->loadModel('proveedor','compra');
        $this->_deposito  = $this->loadModel('deposito','almacen');
        $this->_producto  = $this->loadModel('producto','almacen');
        $this->_presentacion = $this->loadModel('presentacion','almacen');
        $this->_unidad  = $this->loadModel('unidad','archivo');
        $this->_documento  = $this->loadModel('tipoDocumento','configuracion');
       // $this->_marca = $this->loadModel('marca','archivo');
       ///$this->_planificacion = $this->loadModel('planificacion','logistica');
       //$this->_solicitud = $this->loadModel('solicitud','logistica');
       //$this->_incidencia = $this->loadModel('incidencia','logistica');
       //$this->_departamento = $this->loadModel('departamento','archivo');
        
    }
    public function index($pagina = 1 )
    {
        $this->_view->setJs(array('recepcion'));
        $this->_view->setJsPlugin(array('jquery-ui'));
        
        $depTrb = $this->_deposito->relacionDepositoAct(session::get('id_usuario'));
        
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if($this->getInt('deposito')>0)
        {
            if($this->getPostParam('busqueda'))
            {
                $this->_view->lista = $paginador->paginar($this->_recepcion->cargarRecepcionProveedor($this->getInt('deposito'),$this->getPostParam('busqueda')),$pagina);
            }else
            {
                 $this->_view->lista = $paginador->paginar($this->_recepcion->cargarRecepcionProveedor($this->getInt('deposito')),$pagina);        
            }
            
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_recepcion->cargarRecepcionProveedor(),$pagina);
        }
        
        $this->_view->depAct = $this->getInt('deposito');
        $this->_view->depTrb = $depTrb; 
        $this->_view->title= "Recepción de Almacen";
        $this->_view->renderizar('index','almacen','Recepción');
        exit();
        
    }
    
    
    public function recepcionInterna($pagina = 1)
    {
        $this->_view->setJs(array('recepcion'));
        $this->_view->setJsPlugin(array('jquery-ui'));
        $depTrb = $this->_deposito->relacionDepositoAct(session::get('trabajador'));
      
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if($this->getInt('deposito')>0)
        {
            if($this->getPostParam('busqueda'))
            {
                $this->_view->lista = $paginador->paginar($this->_recepcion->cargarRecepcionInterna($this->getInt('deposito'),$this->getPostParam('busqueda')),$pagina);
            }else
            {
                 $this->_view->lista = $paginador->paginar($this->_recepcion->cargarRecepcionInterna($this->getInt('deposito')),$pagina);        
            }
            
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_recepcion->cargarRecepcionInterna(),$pagina);
        }
        
        $this->_view->depAct = $this->getInt('deposito');
        $this->_view->depTrb = $depTrb; 
        $this->_view->titulo = "Recepción de Productos";
        $this->_view->renderizar('recepcionInterna','transaccion');
        exit();
    }
    
    
    
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            //print_r($_POST);exit();
            //buscar el tipo de docmento 
            
            $tdoc = $this->loadModel('tipoDocumento','configuracion');
            $aTipodoc = $tdoc->buscarDocumento($this->getPostParam('tdoc'));
			
            if(stripos($this->getPostParam('orden_compra'),",")!==false)
            {
                $orden_compra = array_filter(explode(",",$this->getPostParam('orden_compra')));
            }else
                    $orden_compra = 0;
            
            $datos = array(
                    "unidad"=>      $this->getInt('unidad'),
                    "deposito"=>    $this->getInt('almacen'),
                    "operacion"=>   $this->getPostParam('operacion'),
                    "orden_compra"=>$this->getPostParam('ord_cpra'),
                    "id_origen"=>   $this->getInt('id_origen'),                    
                    "tdoc"=>        $this->getInt('tdoc'),
                    "ndoc"=>        $this->getInt('nro_doc'),                    
                    "condicion_doc"=>$this->getPostParam('condicion_doc'),
                    "comentario"=>$this->getPostParam('comentario_rec'),
                    "producto"=>    $this->getPostParam('id'),
                    "cantidad"=>    $this->getPostParam('cantidad'),
                    "precio"=>      $this->getPostParam('precio'),
                    "tsa_iva"=>     $this->getPostParam('tsa_iva'),
                    "recibido"=>    $this->getPostParam('recibido'),
                    "usuario"=>     session::get('id_usuario')
                );
				
	//print_r($datos);exit();	
            if($this->_recepcion->insertar($datos))
            {
                $this->redireccionar('almacen/recepcion/index/');
                exit();
            }else
            {
				$this->_recepcion->regLog();
                $this->_view->error = "Error guardando recepcion a proveedores .....";
                 //$this->_view->renderizar('a','transaccion');
                 //exit();   
            }
            
            
            
        }
		//print_r($_SESSION);exit();
        $this->_view->setJsPlugin(array('jquery-ui','validaciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
        $this->_view->setJs(array("recepcion"));
       
        $this->_view->unidad = $this->_unidad->cargarUnidadUsuario(session::get('id_usuario'));
        $this->_view->_documentos = $this->_documento->cargarTipoDocumento();
       
        $this->_view->title = "Recepción de Almacen";
        $this->_view->renderizar('agregar','almacen','Recepción');
        exit();
        
    }
     
    
    
   
    public function buscarTipoMovimiento()
    {
        $inventario = $this->loadModel('inventario');
        echo json_encode($inventario->buscarTipoMovimiento($this->getInt('valor')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE CARGAR LAS ORDENES DE COMPRA DE UN PROVEEDOR
    //==========================================================================
    public function buscarOrden()
    {
		$this->_orden  = $this->loadModel('ordencompra','compra');
        echo json_encode($this->_orden->buscarOrdenProveedor($this->getInt('proveedor')));      
    }
	
	
	
	//==========================================================================
    //METODO QUE PERMITE CARGAR los depositos DE UN PROVEEDOR
    //==========================================================================
    public function buscarDepositoUnidad()
    {	
         echo json_encode($this->_deposito->relacionDepositoAct($this->getInt('unidad')));      
    }
	
    //==========================================================================
    //METODO QUE PERMITE CARGAR LAS MARCAS
    //==========================================================================
    public function cargarMarca()
    {
         echo json_encode($this->_marca->cargarMarca());       
    }
     //==========================================================================
    //METODO QUE PERMITE CARGAR LAS MARCAS
    //==========================================================================
    public function cargarPresentacion()
    {
         echo json_encode($this->_presentacion->buscarPresentacionProducto($this->getInt('producto')));       
    }
    //==========================================================================
    //METODO QUE PERMITE BUSCAR LAS MARCAS POR COMPARACION
    //==========================================================================
    public function buscarMarca()
    {
         echo json_encode($this->_marca->cargarMarca($this->getGetParam('term')));       
    }
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR UN PROVEEDORE DE FORMA ASINCRONA
    //==========================================================================
    public function buscarProveedor()
    {
         echo json_encode($this->_proveedor->buscarProvDesc($this->getGetParam('term')));       
    }
    //==========================================================================
    //PERMITE BUSCAR UN DOCUMENTO DE PROVEEDOR POR SU NUMERO Y TIPO DE DOCUMENTO 
    //==========================================================================
    public function buscarDocumento()
    {
         echo json_encode($this->_recepcion->buscarDocRecep($this->getPostParam('prov'),$this->getPostParam('tdoc'),$this->getPostParam('nro_doc')));       
    }
    //==========================================================================
    //METODO QUE PERMITE BUSCAR UN PRODUCTO POR SU CODIGO
    //==========================================================================
    public function buscarProductoCatalogo()
    {
         echo json_encode($this->_producto->cargarProducto($this->getPostParam('item')));       
    }
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR UN PRODUCTO POR NOMBRE EN EL CATALOGO GENERAL
    //==========================================================================
    public function buscarProducto()
    {
         echo json_encode($this->_producto->buscarDetProducto($this->getPostParam('codigo')));       
    }
    //==========================================================================
    //METODO QUE PERMITE BUSCAR REGISTRO DE SOLICITUD POR SU ID EN FORMATO JSON
    //==========================================================================
    public function  buscarSolicitud()
    {
        echo json_encode($this->_despacho->buscarSolicitud($this->getPostParam('sol')));        
    }
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR REGISTRO DE RECEPCION POR SU ID EN FORMATO JSON
    //==========================================================================
    public function  buscarRecepcion()
    {
        echo json_encode($this->_recepcion->buscarRecepcion($this->getPostParam('codigo')));        
    }
    //==========================================================================
    //METODO QUE PERMITE BUSCAR REGISTRO DE DESPACHO POR SU ID EN FORMATO JSON
    //==========================================================================
    public function  buscarDespacho()
    {
        echo json_encode($this->_despacho->buscarDespacho($this->getPostParam('codigo')));        
    } 
    public function  buscarDespachoId()
    {
        echo json_encode($this->_despacho->buscarDespachoId($this->getPostParam('codigo')));        
    }  
    //==========================================================================
    //METODO QUE PERMITE BUSCAR REGISTRO DE DESPACHO POR SU ID EN FORMATO JSON
    //==========================================================================
    public function  buscarDespachoDeposito()
    {
        echo json_encode($this->_despacho->despachoDeposito($this->getPostParam('deposito')));        
    }
    //==========================================================================
    //METODO QUE PERMITE BUSCAR DETALLES DE LA SOLICITUD POR SU ID EN FORMATO JSON
    //==========================================================================
    
}