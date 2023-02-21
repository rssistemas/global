<?php
class gastosController extends compraController
{
	private $_gasto;
	private $_tgasto;
	private $_unidad;
	private $_tdoc;
	private $_impuesto;
	
	public function __construct()
	{
		parent::__construct();
		$this->_gasto = $this->loadModel('gastos');
		$this->_impuesto=  $this->loadModel('impuesto','archivo'); 
	}
	public function index($pagina = 1)
	{
		$this->_view->title = "Gastos en Compras ";
        $this->getLibrary('paginador');
        $this->_view->setJs(array('gastos'));
        $this->_view->setJsPlugin(array('validaciones','jquery-ui'));
        $paginador = new Paginador();
        //$paginador->setRango(FALSE);
        if($this->getPostParam('busqueda'))
        {
            $gasto = $paginador->paginar($this->_gasto->cargarGasto($this->getPostParam('busqueda')),$pagina,10);         
        }
        else
        {
            $gasto =  $paginador->paginar($this->_gasto->cargarGasto(),$pagina,10);
        }
		if(count($gasto)<1)
		{
			$this->_view->info = "Busqueda sin Resultados ......";
		}
		
        $this->_view->gasto = $gasto;
        $this->_view->paginacion = $paginador->getView('paginacion','compra/gastos/index');	
        $this->_view->renderizar('index','compra','Gastos en Compra');
        exit();
		
		
	}
	
	
	public function agregar()
	{
		if($this->getInt('guardar')==1)
        {
			
			//print_r($_POST);exit();
			
			$datos = array(
				"unidad"=>$this->getInt('unidad'),
				"proveedor"=>$this->getInt('proveedor'),
				"tdoc"=>$this->getInt('tdoc'),
				"ndoc"=>$this->getInt('ndoc'),
				"emision"=>$this->getPostParam('emision'),
				"vencimiento"=>$this->getPostParam('vencimiento'),
				"compra"=>$this->getInt('compra'),
				"comentario"=>$this->getPostParam('comentario'),
				"codigo"=>$this->getPostParam('codigo'),
				"cantidad"=>$this->getPostParam('cantidad'),
				"precio"=>$this->getPostParam('precio'),
				"tsa_iva"=>$this->getPostParam('tsa_iva'),
				"total"=>$this->getPostParam('total')
				
			);
			
			//print_r($datos);
			//exit();
			if($this->_gasto->insertar($datos))
			{					
				$this->redireccionar('compra/gastos/index/','compra');
                exit();
			}else
			{
				$this->_gasto->regLog();
                $this->_view->error = "Error guardando registro nuevo .....";
			}	

		}
		
		$this->_view->title="Nuevo Gastos en Compra"; 
		$this->_view->setJsPlugin(array('jquery-ui','validaciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
        $this->_view->setJs(array("gastos"));
		
		$this->_unidad = $this->loadModel('unidad','archivo');
		$this->_view->unidad = $this->_unidad ->cargarUnidadUsuario(session::get('id_usuario'));
		
		$this->_tdoc = $this->loadModel('tipoDocumento','configuracion');
		$this->_view->tdoc = $this->_tdoc->cargarTipoDocumento();
		$this->_view->impuesto = $this->_impuesto->buscar('IVA');

		$this->_view->renderizar('agregar','compra','Gastos en Compra');
        exit();
		
		
	}
	
	//==========================================================================
    //METODO QUE PERMITE ANULAR GASTO Y HACER REVERSO DE FORMA ASINCRONA
    //==========================================================================
    public function anularGasto()
    { 
		echo json_encode($this->_gasto->anular($this->getPostParam('valor')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR UN GASTO DE FORMA ASINCRONA
    //==========================================================================
    public function buscarGasto()
    { 
		echo json_encode($this->_gasto->buscarGasto($this->getInt('cod')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR UN PROVEEDORE DE FORMA ASINCRONA
    //==========================================================================
    public function buscarTgasto()
    {
        $this->_tgasto = $this->loadModel('tipoGasto','archivo'); 
		echo json_encode($this->_tgasto->buscarAutoTgasto($this->getGetParam('term')));       
    }
 
	//==========================================================================
    //METODO QUE PERMITE BUSCAR LOS TIPO DE GASTOS DE FORMA ASINCRONA
    //==========================================================================
    public function buscarTgasto1()
    {
        $this->_tgasto = $this->loadModel('tipoGasto','archivo'); 
		echo json_encode($this->_tgasto->cargarTgasto($this->getPostParam('cod')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR LOS TIPO DE GASTOS DE FORMA ASINCRONA
    //==========================================================================
    public function buscarTgastoCod()
    {
        $this->_tgasto = $this->loadModel('tipoGasto','archivo'); 
		echo json_encode($this->_tgasto->buscar($this->getInt('cod')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR UN PROVEEDORE DE FORMA ASINCRONA
    //==========================================================================
    public function buscarProveedor()
    {
        $this->_proveedor = $this->loadModel('proveedor'); 
		echo json_encode($this->_proveedor->buscarProvDesc($this->getGetParam('term')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR DOCUMENTOS DE UN PROVEEDORE DE FORMA ASINCRONA
    //==========================================================================
    public function buscarDocProveedor()
    {
        $this->_proveedor = $this->loadModel('proveedor'); 
		echo json_encode($this->_proveedor->cargarDocumentoCpraProveedor($this->getInt('prv'),$this->getInt('tdoc'),$this->getInt('ndoc')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR DETALLE DE INFORME DE RECEPCION
    //==========================================================================
    public function buscarInfRecepcion()
    {
        $this->_recepcion = $this->loadModel('recepcion','almacen'); 
		echo json_encode($this->_recepcion->buscarinfRecepcion($this->getInt('prv'),$this->getInt('doc'),$this->getInt('nro')));       
    }
	
	//==========================================================================
    //METODO QUE PERMITE BUSCAR COMPRAS DE UN PROVEEDOR
    //==========================================================================
    public function buscarCpraProveedor()
    {
        $this->_proveedor = $this->loadModel('proveedor'); 
		echo json_encode($this->_proveedor->buscarCpraProveedor($this->getPostParam('prv')));       
    }
	//----------------------------------------------------------------------
	//METODO QUE CARGA PROVEEDOR POR SU RIF EN FORMATO JSON
	//---------------------------------------------------------------------
	public function cargarProveedor()
    {
    	$this->_proveedor = $this->loadModel('proveedor'); 
        echo json_encode($this->_proveedor->buscarRifProveedor($this->getPostParam('valor')));
    }
}

?>