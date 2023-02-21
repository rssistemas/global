<?php
class ordencompraController extends compraController
{
	private $_orden;
	private $_proveedor;
	private $_producto;
	private $_unidad;
	private $_impuesto;
	private $_tdoc;
	public function __construct()
	{
		parent::__construct();
		$this->_orden = $this->loadModel('ordencompra');
		$this->_proveedor = $this->loadModel('proveedor');
		$this->_unidad = $this->loadModel('unidad','archivo');
		$this->_impuesto=  $this->loadModel('impuesto','archivo'); 
		$this->_tdoc = $this->loadModel('tipoDocumento','configuracion');
	}
	public function index($pagina = 1)
	{
		
		$this->_view->title = "Ordenes de Compra ";
                $this->getLibrary('paginador');
        
		$this->_view->setJsPlugin(array('jquery-ui','validaciones'));
                $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
                $this->_view->setJs(array('ordencompra'));
		$paginador = new Paginador();
                //$paginador->setRango(FALSE);
                if($this->getPostParam('busqueda'))
                {
                    $orden = $paginador->paginar($this->_orden->cargarOrdenCompra($this->getPostParam('busqueda')),$pagina,10);         
                }
                else
                {
                    $orden =  $paginador->paginar($this->_orden->cargarOrdenCompra(),$pagina,10);
                }

                        $this->_view->orden = $orden;
                $this->_view->paginacion = $paginador->getView('paginacion','compra/ordencompra/index');	
                $this->_view->renderizar('index','compra','Orden de Compra');
                exit();
		
		
	}
	
	
	public function agregar()
	{
		if($this->getInt('guardar')==1)
        {
			
			//print_r($_POST); exit();
			$datos = array(
			
				"unidad"=>$this->getInt('unidad'),
				"proveedor"=>$this->getInt('proveedor'),
				"tdoc"=>$this->getInt('tdoc'),
				"ndoc"=>$this->getInt('presupuesto'),
				"emision"=>$this->getPostParam('emision'),
				"vencimiento"=>$this->getPostParam('vencimiento'),
				//"recepcion"=>$this->getInt('recepcion'),
				"comentario"=>$this->getPostParam('comentario'),
				"producto"=>$this->getPostParam('id'),
				"cantidad"=>$this->getPostParam('cantidad'),
				"precio"=>$this->getPostParam('precio'),
				"tsa_iva"=>$this->getPostParam('tsa_iva'),
				//"recibido"=>$this->getPostParam('recibido'),
				"tipo" => $this->getPostParam('tipo'),
				"deposito"=> $this->getInt('almacen')
				
				);
			//print_r($datos); exit();	
			if($this->_orden->incluir($datos))
			{
				$this->redireccionar('compra/ordencompra/index/','compra');
				$this->mensaje = "Orden de Compra Creada .....";
                exit();
				
			}else
			{
				$this->_orden->regLog();
                $this->_view->error = "Error guardando registro nuevo .....";
			}
	
		}
		
		$this->_view->title="Nueva Orden de Compra"; 
		$this->_view->setJsPlugin(array('jquery-ui','validaciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
        $this->_view->setJs(array('ordencompra'));
		
		$this->_view->documento = $this->_tdoc->buscarDocumento('FACTURA');
		$this->_view->unidad = $this->_unidad ->cargarUnidadUsuario(session::get('id_usuario'));
		$this->_view->impuesto = $this->_impuesto->buscar('IVA');
		$this->_view->renderizar('agregar','compra','Orden de Compra');
        exit();
	}
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR REGISTRO DE SOLICITUD POR SU ID EN FORMATO JSON
    //==========================================================================	
    public function buscarProductoCatalogo()
    {
		$this->_producto = $this->loadModel('producto','almacen');
         echo json_encode($this->_producto->buscarAutoProducto($this->getGetParam('term')));       
    }
	//-----------------------------------------------------------------------------------
	//BUSCAR PRODUCTO PARA ORDEN DE COMPRA
	//----------------------------------------------------------------------------------
	public function buscarProducto()
    {
		$this->_producto = $this->loadModel('producto','almacen');
         echo json_encode($this->_producto->buscar($this->getInt('cod')));       
    }
	
	//----------------------------------------------------------------------
	//METODO QUE CARGA PROVEEDOR POR SU RIF EN FORMATO JSON
	//---------------------------------------------------------------------
	public function cargarProveedor()
    {
    	//$this->_proveedor = $this->loadModel('proveedor'); 
        echo json_encode($this->_proveedor->buscarRifProveedor($this->getPostParam('valor')));
    }
	
	public function buscarProveedor()
	{
		echo json_encode($this->_proveedor->buscarProveedor($this->getPostParam('tipo').$this->getPostParam('rif')));		
	}
	//----------------------------------------------------------------------
        //METODO QUE BUSCA LA ORDEN DE COMPRA EL MAESTRO Y EL DETALLE
        //----------------------------------------------------------------------
	public function buscarOrdenCompra()
	{
            echo json_encode($this->_orden->buscarOrden($this->getPostParam('codigo')));		
	}
}

?>