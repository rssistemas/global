<?php
class indexController extends reporteController
{
    private $_persona;
    public function __construct() {
        parent::__construct();
        $this ->getLibrary('xpdf');
        
       
        $this->_persona = $this->loadModel('persona','seguridad');
    }
    
    public function index() {
        $this->_view->setJs(array('reporte')); 
        $this->_view->renderizar('index','reportes');
        exit();    
    }
    public function catProducto()
    {
        //intancia la clase xpdf que hereda de fpdf
        $this->_pdf = new xpdf('l');
        
        $cls_producto = $this->loadModel('producto','archivo');
        $catalogo = $cls_producto->catalogoProducto();
        
        $ruta_rep = ROOT . 'public' . DS . 'reportes' . DS .'catalogoProducto.php' ;
        require_once $ruta_rep;
		
	$this->_pdf->Output();
    } 
	//---------------------------------------------------------------------------------------------
	//IMPRIME LISTADO DE COMPRAS
	//---------------------------------------------------------------------------------------------
	public function catCompra()
    {
        //intancia la clase xpdf que hereda de fpdf
        $this->_pdf = new xpdf('p');
        
        $cls_compra = $this->loadModel('compras','compra');
		
        $datos = $cls_compra->cargarCompras();
        
        $ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'compras'. DS .'listadoCompra.php' ;
        include_once $ruta_rep;
		
		$this->_pdf->Output();
    }
	
	//---------------------------------------------------------------------------------------------
	//LISTADO DE GASTOS EN COMPRAS
	//---------------------------------------------------------------------------------------------
	public function catGtoCompra()
    {
        //intancia la clase xpdf que hereda de fpdf
        $this->_pdf = new xpdf('p');
        
        $cls_gasto = $this->loadModel('gastos','compra');
		
        $datos = $cls_gasto->cargarGasto();
        
        $ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'compras'. DS .'listadoGasto.php' ;
        include_once $ruta_rep;
		
		$this->_pdf->Output();
    }
	 
	//---------------------------------------------------------------------------------------------
	//LISTADO DE ORDENES COMPRAS
	//---------------------------------------------------------------------------------------------
	public function catOrdCompra()
    {
        //intancia la clase xpdf que hereda de fpdf
        $this->_pdf = new xpdf('p');
        
        $cls_orden = $this->loadModel('ordencompra','compra');
		
        $datos = $cls_orden->cargarOrdenCompra();
        
        $ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'compras'. DS .'listadoOC.php' ;
        include_once $ruta_rep;
		
		$this->_pdf->Output();
    }
	
	//---------------------------------------------------------------------------------------------
	//LISTADO DE ORDENES COMPRAS
	//---------------------------------------------------------------------------------------------
	public function impOC($id)
    {
        //intancia la clase xpdf que hereda de fpdf
        $this->_pdf = new xpdf('p');
        
        $cls_orden = $this->loadModel('ordencompra','compra');
		
        $datos = $cls_orden->buscarOrden($id);
		$empresa = session::get('actEmp');
		
       // print_r($empresa);exit();
        $ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'compras'. DS .'OC.php' ;
		
        include_once $ruta_rep;
		
		$this->_pdf->Output();
		
    }  
	 	 
	 
	 
	//--------------------------------------------------------------------------------------
	//IMPRIME LISTADO DE RECEPCIONES
	//-------------------------------------------------------------------------------------- 
	 public function catRecepcion()
	 {
	 	$this->_pdf = new xpdf('l');
        
        $cls_recepcion = $this->loadModel('recepcion','almacen');
		
		$datos = $cls_recepcion->cargarRecepcionProveedor();
		
		$ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'recepcion'. DS .'recepcionPrdProveedor.php' ;
		include_once $ruta_rep;
		
		$this->_pdf->Output();
	 }
	 
	 
	 //---------------------------------------------------------------------------------------------
	 //IMPRIME INFORME DE RECEPCION
	 //---------------------------------------------------------------------------------------------
	 public function impIR($id)
	 {
	 	$this->_pdf = new xpdf('p');
        
        $cls_recepcion = $this->loadModel('recepcion','almacen');
		
		$datos = $cls_recepcion->buscarRecepcion($id);
		$empresa = session::get('actEmp');
		 
		$ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'recepcion'. DS .'IR.php' ;
		include_once $ruta_rep;
		
		$this->_pdf->Output();
	 }
	 
	 
	 //--------------------------------------------------------------------------------------------
	 //IMPRIME RECIVO DE COMPRA
	 //--------------------------------------------------------------------------------------------
	 public function impRC($id)
	 {
	 	$this->_pdf = new xpdf('L','mm',array(220,135));
        
		//$this->_pdf-> AddPage();
		
        $cls_cobro = $this->loadModel('cobro','administracion');
		
		$datos = $cls_cobro->buscarCobro($id);
		
		//print_r($datos);exit();
		$empresa = session::get('actEmp');
		 
		$ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'admin'. DS .'RC.php' ;
		include_once $ruta_rep;
		
		$this->_pdf->Output();	
		
		
	 } 
	 
         //--------------------------------------------------------------------------------------------
	 //IMPRIME RECIVO DE VENTA
	 //--------------------------------------------------------------------------------------------
	 public function impFV($id)
	 {
	 	$this->_pdf = new xpdf('L','mm',array(220,135));
        
		//$this->_pdf-> AddPage();
		
                $cls_venta = $this->loadModel('factura','venta');
		
		$mae = $cls_venta->buscarFactura($id);
		$det = $cls_venta->buscarDetFactura($id);
		//print_r($datos);exit();
		$empresa = session::get('actEmp');
		 
		$ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'venta'. DS .'FV.php' ;
		include_once $ruta_rep;
		
		$this->_pdf->Output();	
		
		
	 }
         
         
         
	 //--------------------------------------------------------------------------------------------
	 //IMPRIME CATALOGO DE COBROS
	 //--------------------------------------------------------------------------------------------
	 public function catCobro()
	 {
	 	$this->_pdf = new xpdf('p');
		
		$cls_cobro = $this->loadModel('cobro','administracion');
		
		$datos = $cls_cobro->cargarCobro();
		
		
		$ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'admin'. DS .'listadoCobro.php' ;
		
        include_once $ruta_rep;
		
		$this->_pdf->Output();
		
		
		
	 } 
	 
	 	 
	//---------------------------------------------------------------------------------------------
	//LISTADO DE PROVEEDORES
	//---------------------------------------------------------------------------------------------
	public function catProveedor()
    {
        //intancia la clase xpdf que hereda de fpdf
        $this->_pdf = new xpdf('L');
        
        $cls_prv = $this->loadModel('proveedor','compra');
		
        $datos = $cls_prv->cargarProveedor();
        
        $ruta_rep = APP_PATH . 'public' . DS . 'reportes' . DS .'compras'. DS .'listadoProveedor.php' ;
		
        include_once $ruta_rep;
		
		$this->_pdf->Output();
    } 
	 
	
	
	
	
	
	
	
	
	
	
	
	
	       
    public function print_planilla_pago($nro = false)
    {
        //formato media carta
         $this->_pdf = new xpdf('l', 'mm', array(215,139));
        
        //die($nro);
        $reca = $this->loadModel('gestionrecaudacion','transaccion');
        $tributo = $this->loadModel('tributo','transaccion');
        
        $contribuyente = $this->loadModel('contribuyente','archivo');
        
        $datos = $reca->buscarRecaudacion($nro);       
        $datosTributo = $tributo->buscarTributo($datos[0]['tributo_id']); 
        
        $datosContribuyente = $contribuyente->buscar($datos[0]['contribuyente_id']);
        //print_r($datosContribuyente);exit();
        
       
                
        $ruta_rep = ROOT . 'public' . DS . 'reportes' . DS .'ppago.php' ;
	
	require_once $ruta_rep;
		
	$this->_pdf->Output();
    }
    public function procesarReporte()
    {
        $ruta =ROOT . 'public' . DS . 'reportes' . DS; 
       // print_r($_POST);exit();
        if($this->getPostParam('reporte')>0)
        {
             
             
            $reporte = $this->getPostParam('reporte');
            $desde = $this->getPostParam('inicio');
            $hasta = $this->getPostParam('final');
            switch ($reporte)
            {
                case 1 :
                    $this->_pdf = new xpdf('p', 'mm', array(216,279));
                    $reporte = 'ingresoContribuyente';
                    $contribuyente = $this->loadModel('contribuyente','archivo');
                    $datos = $contribuyente ->recaudacionContribuyente($desde,$hasta);
                    
                break;    
                case 2:
                    $this->_pdf = new xpdf('p', 'mm', array(216,279));
                    $reporte = 'ingresoRecaudacion';
                    $recaudacion = $this->loadModel('gestionrecaudacion','transaccion');
                    $datos = $recaudacion->recaudacionTipo($desde,$hasta);
                    
                    //print_r($datos);exit();
                break;
                case 3:
                    $this->_pdf = new xpdf('l', 'mm', array(216,279));
                    $reporte = 'licenciasEmitidas';
                    $licencia = $this->loadModel('gestionlicencia','transaccion');
                    $datos = $licencia->licenciaRango($desde,$hasta);
                break;
                case 4:
                    $this->_pdf = $this->_pdf = new xpdf('l', 'mm', array(216,279));
                    $reporte = 'multasEmitidas';
                    $sancion = $this->loadModel('gestionsancion','transaccion');
                    $datos = $sancion->multaRango($desde,$hasta);
                    
                break;     
            
            }
            
            require_once $ruta.$reporte.'.php';
            $this->_pdf->Output();
            
        } 
    }
    public function print_patente($nro)
    {
        $this->_pdf = new xpdf('l', 'mm', array(215,139));
        
        $licencia = $this->loadModel('gestionlicencia','transaccion');
        $contribuyente = $this->loadModel('contribuyente','archivo');
        $actividad = $this->loadModel('clasificador','archivo');
        
        $csr_licencia = $licencia->consultarLicencia($nro);
        $csr_actividad = $actividad->buscar((int)$csr_licencia[0]['clasificador_id']);
        
        $ruta_rep = ROOT . 'public' . DS . 'reportes' . DS .'patente.php' ;
		
	require_once $ruta_rep;
		
	$this->_pdf->Output();
    }
    public function print_AM_sancion($id)
    {
        $this->_pdf = new xpdf('p', 'mm', array(216,279));
        
        
        $sancion = $this->loadModel('gestionsancion','transaccion');
        $usuario = $this->loadModel('usuario','seguridad');
        
        $csr_sancion = $sancion->consultar($id);
        $csr_am = $sancion->consultarActoMotivado($csr_sancion[0]['id_sancion']);
        $fiscal = $usuario->buscar($csr_sancion[0]['fiscal']);
        
       // print_r($csr_sancion);
       // exit();
        
        $ruta_rep = ROOT . 'public' . DS . 'reportes' . DS .'am_sancion.php' ;
		
	require_once $ruta_rep;
		
	$this->_pdf->Output();
    }        
    
}
?>