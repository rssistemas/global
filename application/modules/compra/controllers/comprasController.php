<?php
class comprasController extends compraController
{
    private $_compras;
    private $_unidad;
    private $_proveedor;
    private $_tdoc;
    private $_recepcion;
    private $_orden_compra;
    private $_gasto;
    private $_tmov;
    private $_costo;
    private $_impuesto;
    private $_deposito; 

    public function __construct()
    {
        parent::__construct();
        $this->_compras =  $this->loadModel('compras');
        //$this->_unidad  =  $this->loadModel('unidad','archivo');
        //$this->_tmov    =  $this->loadModel('tipoMovimiento','configuracion');
        //$this->_costo   =  $this->loadModel('costo');
        //$this->_impuesto=  $this->loadModel('impuesto','archivo'); 
        //$this->_deposito=  $this->loadModel('deposito','almacen');
    }

    public function index($pagina = 1)
    {
        
        //$this->_acl->acceso('compra_consultar',105,'resumen-index');
        
        
        $this->_view->title = "Compras ";
        $this->getLibrary('paginador');
        $this->_view->setJs(array('compra'));
        $this->_view->setJsPlugin(array('validaciones','jquery-ui'));
        $this->_view->setCssPlugin(array('jquery-ui'));
        $paginador = new Paginador();

        if(validate::getPostParam('busqueda'))
        {
            $compras = $paginador->paginar($this->_compras->cargarCompras(validate::getPostParam('busqueda')),$pagina,10);         
        }
        else
        {
            $compras =  $paginador->paginar($this->_compras->cargarCompras(),$pagina,10);
        }
        if(count($compras)<1)
        {
            $this->_view->info = "Busqueda sin Reultados .....";			
        }		
        $this->_view->compras = $compras;
        $this->_view->paginacion = $paginador->getView('paginacion','compra/compras/index');	
        $this->_view->renderizar('index','compra','Registro de Compra');
        exit();


    }
//--------------------------------------------------------------------------------------------------
//METODO QUE AGREGA REGISTRO 
//--------------------------------------------------------------------------------------------------	
public function agregar()
{
        $this->_acl->acceso('compra_agregar',105,'compra-compras-index');

        if(validate::getInt('guardar')==1)
        {
            $empresa = session::get('actEmp');
            //print_r($_POST);
            //exit();
            $datos = array(
                "unidad"=>validate::getInt('unidad'),
                "deposito"=> validate::getInt('almacen'),
                "proveedor"=>validate::getInt('proveedor'),
                "empresa"=>$empresa[0]['id_empresa'],
                "recepcion"=>validate::getInt('recepcion'),
                "orden_compra"=> validate::getInt('orden_compra'),
                "tdoc"=>validate::getPostParam('tipo_doc_rec'),
                "ndoc"=>validate::getInt('nro_doc_rec'),
                "cdoc"=> validate::getPostParam('control'),
                "emision"=>validate::getPostParam('emision'),
                "pronto_pago"=> validate::getInt('prontopago'),
                "tasa_pronto_pago"=> validate::getPostParam('tasa_pronto_pago'),
                "plazo_pronto_pago"=> validate::getInt('plazo_pronto_pago'),
                "comentario"=>"prueba de comentario",
                "tipo" => validate::getPostParam('tipo'),
                "vencimiento"=>validate::getPostParam('emision'),

                "producto"=>validate::getPostParam('id'),
                "cantidad"=>validate::getPostParam('cantidad'),
                "precio"=>validate::getPostParam('precio'),
                "tsa_iva"=>validate::getPostParam('tsa_iva'),
                "recibido"=>validate::getPostParam('recibido')

            );

                //print_r($datos);
                //exit();
                if($this->_compras->insertar($datos))
                {
                    //$this->_orden_compra = $this->loadModel('ordencompra');
                    //$this->_orden_compra ->cerrarOrdenRecepcion(validate::getInt('recepcion'));

                    $this->redireccionar('compra/compras/index/','compra');
                    exit();
                }else
                {
                    Logger::errorLog("Error registrando compra",'ERROR');
                    $this->_view->error = "Error guardando registro nuevo .....";
                }	

        }

        $this->_view->title="Nueva Compra"; 
        $this->_view->setJsPlugin(array('jquery-ui','validaciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico','autoSearchCol'));
        $this->_view->setJs(array("compra"));
          $this->_view->setCss(array('factura'));
        //load model
        $this->_tdoc = $this->loadModel('tipoDocumento','configuracion');
        $this->_unidad  =  $this->loadModel('unidad','archivo');
        $this->_impuesto=  $this->loadModel('impuesto','archivo');
        
        $this->_view->documento = $this->_tdoc->documentoSalida();
        $this->_view->unidad = $this->_unidad->cargarUnidadUsuario(session::get('id_usuario'));
        $this->_view->impuesto = $this->_impuesto->buscar('IVA');

        $this->_view->renderizar('agregar','compra','Registro de Compra');
        exit();
        
        
}
	
	public function distribuirGasto($cpra)
	{
		$tipo = $this->_tmov->buscar('COMPRA');
		$this->_gasto = $this->loadModel('gastos');
		if($this->getInt('guardar')==1)
		{
			//print_r($_POST);
			
			$tipo_documento = $this->getPostParam('tipo_documento');
			$nro_documento  = $this->getPostParam('nro_documento');
			$compra         = $this->getPostParam('compra');
			$gasto          = $this->getPostParam('gasto');
			$tdet           = $this->getPostParam('ndetalle');
			
			if($tdet > 0)
			{
								
				$producto    = $this->getPostParam('producto');
				$porcentaje  = $this->getPostParam('porcentaje');
				$distribucion= $this->getPostParam('distribucion');
				$cantidad    = $this->getPostParam('cantidad_producto');
				
				for($i = 0; $i < $tdet; $i++ )
				{
						
					$prod= array_splice($producto,0,$tdet);
					$dist= array_splice($distribucion,0,$tdet);
					$porc= array_splice($porcentaje,0,$tdet);
					$cant= array_splice($cantidad,0,$tdet);							
					
					$detalle[] = array(
							"tipo_documento"=> $tipo_documento[$i],
							"nro_documento" => $nro_documento[$i],
							"gasto"         => $gasto[$i],
							"compra"        => $compra,
							"producto"      => $prod,
							"cantidad"      => $cant,
							"distribucion"  => $dist,
							"porcentaje"    => $porc
					 	);
										 
				}
				
			}
			
			//print_r($detalle);
			//exit();
			if($this->_gasto->insertarDistribucion($detalle))
			{
					
				$costo = $this->_gasto->calcularCostoGasto($detalle);	
				if(count($costo))
				{
					if($this->_costo->actualizarCosto($costo))
					{
						$this->redireccionar('compra/compras/index/','compra');
                		exit();
					}else
						{
							$this->_costo->regLog();
                			$this->_view->error = "Error guardando costos .....";
						}
				}else
					{
						$this->_costo->regLog();
                		$this->_view->error = "Error calculando costos .....";
					}	
							
				
			}else
				{
					$this->_gasto->regLog();
                	$this->_view->error = "Error guardando registro nuevo .....";	
				}
						
		}
		
				
		$data = array();
		$gto_compra = $this->_gasto->cargarGastoDoc($tipo['id_tipo_movimiento'],$cpra);
		$detalle = $this->_compras->cargarDetCompra($cpra);
		foreach ($gto_compra as $value) {
			//$detalle = $this->_gasto->cargarDetGasto($value['id_gasto']);			
			$data[]= array("maestro"=>$value,"detalle"=>$detalle);
		}
		if(count($data)< 1)
		{
			$this->_view->info = "Busqueda sin resultados ....";
		}
		$this->_view->nro_compra = $cpra;
		$this->_view->gasto = $data;
		$this->_view->title = "Distribucion de Gastos en Compras ";
		$this->_view->renderizar('distribucion','compra');
        exit();
	}	
	
    //==========================================================================
    //METODO QUE PERMITE BUSCAR UN PROVEEDORE DE FORMA ASINCRONA
    //==========================================================================
    public function buscarProveedor()
    {
        $this->_proveedor = $this->loadModel('proveedor'); 
	echo json_encode($this->_proveedor->buscarProvDesc(validate::getGetParam('term')));       
    }
	
    //==========================================================================
    //METODO QUE PERMITE BUSCAR DOCUMENTOS DE UN PROVEEDORE DE FORMA ASINCRONA
    //==========================================================================
    public function buscarDocProveedor()
    {
	echo json_encode($this->_compras->checkDocProveedor(validate::getInt('prv'),validate::getInt('doc'),validate::getInt('nro')));       
    }
	
    //==========================================================================
    //METODO QUE PERMITE BUSCAR DETALLE DE INFORME DE RECEPCION MEDIANTE
    //ID DEL PROVEEDOR,NUMERO DE FACTURA
    //==========================================================================
    public function buscarInfRecepcion()
    {
        $this->_recepcion = $this->loadModel('recepcion','almacen'); 
	echo json_encode($this->_recepcion->buscarinfRecepcion(validate::getInt('prv'),validate::getInt('doc'),validate::getInt('nro')));       
    }
	
    //--------------------------------------------------------------------------
    //METODO QUE BUSCA RECEPCION POR SU ID
    //--------------------------------------------------------------------------
    public function buscarRecepcion()
    {
        $this->_recepcion = $this->loadModel('recepcion','almacen'); 
	echo json_encode($this->_recepcion->buscarRecepcion(validate::getInt('codigo')));       
    }
    //----------------------------------------------------------------------
    //METODO QUE CARGA PROVEEDOR POR SU RIF EN FORMATO JSON
    //---------------------------------------------------------------------
    public function cargarProveedor()
    {
    	$this->_proveedor = $this->loadModel('proveedor'); 
        echo json_encode($this->_proveedor->buscarRifProveedor(validate::getPostParam('valor')));
    }
	
    //----------------------------------------------------------------------
    //METODO QUE CARGA LISTADO DE PRODUCTO POR SU NOMBRE EN FORMATO JSON
    //---------------------------------------------------------------------
    public function cargarProducto()
    {
    	$producto = $this->loadModel('producto','almacen'); 
        echo json_encode($producto->cargarProducto(validate::getPostParam('valor')));
    }
    //----------------------------------------------------------------------
    //METODO QUE BUSCA DE PRODUCTO POR SU ID EN FORMATO JSON
    //---------------------------------------------------------------------
    public function buscarProducto()
    {
        $producto = $this->loadModel('producto','almacen'); 
        echo json_encode($producto->buscar(validate::getInt('valor')));
    }
	
    //----------------------------------------------------------------------
    //METODO QUE BUSCA DE COMPRA POR SU ID EN FORMATO JSON
    //---------------------------------------------------------------------
    public function buscarCompra()
    {
       if(validate::getInt('proveedor'))
          echo json_encode($this->_compras->buscarCompra(validate::getInt('valor'),validate::getInt('proveedor')));
        else
           echo json_encode($this->_compras->buscarCompra(validate::getInt('valor')));
    }
	
    //----------------------------------------------------------------------
    //METODO QUE BUSCA DE COMPRA POR SU numero y proveedor EN FORMATO JSON
    //---------------------------------------------------------------------
    public function buscarCompraProveedor()
    {

            echo json_encode($this->_compras->buscarCompraProveedor(validate::getInt('valor'),validate::getInt('proveedor')));

    }

    //-------------------------------------------------------------------------
    //METODO QUE ELIMINA UNA COMPRA
    //-------------------------------------------------------------------------
    public function eliminarCompra()
    {

        echo json_encode($this->_compras->eliminar(validate::getInt('valor')));
    }

    //------------------------------------------------------------------------
    //METODO QUE BUSCA DEPOSITOS RELACIONADOS A UNA UNIDAD OPERATIVA
    //------------------------------------------------------------------------
    public function cargarDeposito()
    {
        $this->_deposito=  $this->loadModel('deposito','almacen');
        echo json_encode($this->_deposito->relacionDepositoAct(validate::getInt('valor')));		
    }
	
}

?>
