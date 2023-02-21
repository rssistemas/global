<?php
class listaprecioController extends ventaController
{
	private $_producto;
	private $_unidad;
	
	public function __construct()
	{
		parent::__construct();
		$this->_producto = $this->loadModel('producto','almacen');
		$this->_unidad   = $this->loadModel('unidad','archivo');
	}
	
	public function index($pagina = 1)
	{
		$this->getLibrary('paginador');
        $this->_view->setJsPlugin(array('jquery-ui','validaciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico','autoSearchCol'));
        $this->_view->setJs(array("lista"));
		
        $paginador = new Paginador();
		if($this->getInt('unidad'))
		{
			if($this->getInt('deposito'))
			{
				if($this->getPostParam('busqueda'))
		        {
		            $lista =  $paginador->paginar($this->_producto->listaPrecioProducto($this->getPostParam('busqueda'),$this->getInt('unidad'),$this->getInt('deposito')),$pagina);
		        }else{
		            $lista =  $paginador->paginar($this->_producto->listaPrecioProducto(false,$this->getInt('unidad'),$this->getInt('deposito')),$pagina);
		        }
			}else
				{
					$lista =  $paginador->paginar($this->_producto->listaPrecioProducto(false,$this->getInt('unidad')),$pagina);
				}	
		}else
			{
				if($this->getPostParam('busqueda'))
		        {
		            $lista =  $paginador->paginar($this->_producto->listaPrecioProducto($this->getPostParam('busqueda')),$pagina);
		        }else{
		            $lista =  $paginador->paginar($this->_producto->listaPrecioProducto(),$pagina);
		        }
			}
            
        
        if(count($lista)<1)    
        {
        	$this->_view->info = "Busqueda sin Resultados ......";
        }
		
		$this->_view->unidad = $this->_unidad ->cargarUnidadUsuario(session::get('id_usuario'));	
		$this->_view->lista = $lista;
		
		
		$this->_view->title = "Lista de Precio";
		$this->_view->renderizar('index','venta');
		exit();
	}
	
	public function utilidad()
	{
		$datos = array(
			"stock"   => $this->getInt('stock'),
			"utilidad"=> $this->getPostParam('utilidad'),
			"precio"  => $this->getPostParam('precio')		
		);
		
		if(!$this->_producto->actualizarUtilidad($datos))
		{
			$this->_factura->regLog();
			$this->_view->error = "Error guardando Factura de Venta .....";			
			
		}
			$this->redireccionar('venta/listaprecio/index');
			exit();		
	}
	
	//-----------------------------------------------------------------------------------
	//BUSCAR BLOQUEOS DE PRODUCTOS PARA FACTURACION
	//----------------------------------------------------------------------------------
	public function buscarProducto()
    {
		$this->_producto = $this->loadModel('producto','almacen');
        echo json_encode($this->_producto->buscarStockProducto($this->getInt('codigo')));       
    }
	
	
}
