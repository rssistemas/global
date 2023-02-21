<?php
class facturaController extends ventaController
{
    private $_factura;
    private $_cliente;
    private $_producto;
    private $_unidad;
    private $_impuesto;
	
    public function __construct() {
        parent::__construct();
        $this->_factura = $this->loadModel('venta');
        $this->_cliente = $this->loadModel('cliente');
	$this->_producto = $this->loadModel('producto','almacen');
	$this->_unidad = $this->loadModel('unidad','archivo');
	$this->_impuesto = $this->loadModel('impuesto','archivo');
    }
    public function index($pagina = 1) {
		
		
	$this->_view->setJs(array('factura22'));
        $this->getLibrary('paginador');
        
        $paginador = new Paginador();
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista =  $paginador->paginar($this->_factura->cargarFactura(validate::getPostParam('busqueda')),$pagina);
        }else{
            $this->_view->lista =  $paginador->paginar($this->_factura->cargarFactura(),$pagina);
        }    
        		    
        $this->_view->paginacion = $paginador->getView('paginacion','venta/factura/index');
        $this->_view->title="Facturacion";
        $this->_view->renderizar('index','venta','Facturacion');
        exit();
        
        
    }
    
    public function agregar()
    {
       
        if(validate::getInt('guardar')==1)
        {
            //print_r($_POST);
            $plazo = (validate::getPostParam('plazo') > 0 )? validate::getPostParam('plazo') : 1;
            $tsa_iva = (validate::getPostParam('tsa_iva')>0)?validate::getPostParam('tsa_iva') : validate::getPostParam('default_iva'); 
            $datos = array(
                
                'cliente' => validate::getInt('cliente'),
                'unidad'  => validate::getInt('unidad'),
                'pedido'  => validate::getPostParam('pedido'),
                'tipo'    => validate::getPostParam('tipo_doc'),
                'plazo'   => $plazo,
                'vendedor'=> validate::getInt('vendedor'),
                'tsa_desc'=> validate::getGetParam('tsa_desc'),
                'tsa_iva' => $tsa_iva,
                'producto'=> validate::getPostParam('id'),
                'cantidad'=> validate::getPostParam('cantidad'),
                'precio'  => validate::getPostParam('precio'),
                'stock'   => validate::getPostParam('stock'),
                'impuesto'=> validate::getPostParam('imp'),
                'control' => validate::getPostParam('control'),
                'total'   => validate::getPostParam('total')			

            );

            //print_r($datos);
            //exit();

            if($this->_factura->insertar($datos))
            {
                $this->redireccionar('venta/factura/index');
                exit();

            }else
                {
                    $this->_factura->regLog();
                    $this->_view->error = "Error guardando Factura de Venta .....";
                    //exit();	

                }
			            
        }
        		
	$this->_view->setJsPlugin(array('jquery-ui','validaciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico','autoSearchCol'));
	$this->_view->setJs(array('factura22'));
        $this->_view->setCss(array('factura'));
		
	
        
        $this->_view->unidad = $this->_unidad ->cargarUnidadUsuario(session::get('id_usuario'));
	$trabajador = $this->loadModel('trabajador','rrhh');
	$this->_view->vendedor = $trabajador->trabajadorCargo('VENDEDOR');
	$this->_view->impuesto = $this->_impuesto->buscar('IVA');
		
	$this->_view->title= "Nueva Factura";
        $this->_view->renderizar('agregar22','venta','Facturacion');
        exit();
    }




    public function anular($id)
    {
        if($id)
        {
            if($this->getInt('guardar')==1)
            {


            }


            $this->_view->title= "Anulacion de  Factura";
            $this->_view->renderizar('anular','venta','Facturacion');
            exit();
        }	

    }        
    
    public function buscarCliente()
    {
        echo json_encode($this->_cliente->buscar(validate::getPostParam('rif')));       
    } 

	public  function autoBusCliente()
	{
		echo json_encode($this->_cliente->buscarAutoCliente(validate::getGetParam('term')));
	}
	//==========================================================================
    //METODO QUE PERMITE BUSCAR REGISTRO DE SOLICITUD POR SU ID EN FORMATO JSON
    //==========================================================================	
	public function buscarProductoCatalogo()
    {
		$this->_producto = $this->loadModel('producto','almacen');
         echo json_encode($this->_producto->buscarProductoVenta(validate::getGetParam('term')));       
    }
	//-----------------------------------------------------------------------------------
	//BUSCAR PRODUCTO PARA FACTURACION
	//----------------------------------------------------------------------------------
	public function buscarProducto()
    {
		$this->_producto = $this->loadModel('producto','almacen');
         echo json_encode($this->_producto->buscarStockProducto(validate::getInt('codigo')));       
    }
	//-----------------------------------------------------------------------------------
	//BUSCAR BLOQUEOS DE PRODUCTOS PARA FACTURACION
	//----------------------------------------------------------------------------------
	public function buscarBloqueos()
    {
		$this->_producto = $this->loadModel('producto','almacen');
        echo json_encode($this->_producto->buscarDisponibilidadProducto(validate::getInt('codigo')));       
    }
    //-----------------------------------------------------------------------------------
	//METODO QUE PÈRMITE BLOQUEAR PRODUCTOS PARA LA FACTURACION 
	//----------------------------------------------------------------------------------
	public function bloquearproducto()
    {
		$this->_producto = $this->loadModel('producto','almacen');
        echo json_encode($this->_producto->bloquearProducto($this->getInt('stock'),$this->getInt('producto'),$this->getInt('cantidad')));       
    }
	//-----------------------------------------------------------------------------------
	//METODO QUE PÈRMITE DESBLOQUEAR PRODUCTOS PARA LA FACTURACION 
	//----------------------------------------------------------------------------------
	public function desbloquearproducto()
    {
		$this->_producto = $this->loadModel('producto','almacen');
        echo json_encode($this->_producto->desbloquearProducto($this->getInt('stock')));       
    }
	
	//-----------------------------------------------------------------------------------
	//METODO QUE PÈRMITE FACTURA MEDIANTO SU ID 
	//----------------------------------------------------------------------------------
	public function buscarFactura()
    {
        echo json_encode($this->_factura->buscarFactura($this->getInt('valor')));       
    }
	
	//-----------------------------------------------------------------------------------
	//METODO QUE PÈRMITE BUSCAR DETALLE DE FACTURA MEDIANTE SU ID 
	//----------------------------------------------------------------------------------
	public function buscarDetFactura()
    {
        echo json_encode($this->_factura->buscarDetFactura($this->getInt('valor')));       
    }
	
//-----------------------------------------------------------------------------------
//METODO QUE PÈRMITE BUSCAR IMPUESTOS A MOSTRAR EN TABLA DE DETALLE DE FACTURA 
//----------------------------------------------------------------------------------
    public function buscarImpuesto()
    {
        echo json_encode($this->_impuesto->cargarImpuesto());       
    }
}