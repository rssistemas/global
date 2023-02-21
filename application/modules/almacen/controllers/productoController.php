<?php
class productoController extends almacenController
{
    private $_producto;
    private $_medida;
    private $_clasificacion;
    private $_grupo;
    private $_presentacion;
    private $_marca;
    
    public function __construct() {
        parent::__construct();
        $this->_producto = $this->loadModel('producto');
        $this->_medida = $this->loadModel('unidadMedida','configuracion');
	$this->_clasificacion = $this->loadModel('clasificacion');
	$this->_grupo = $this->loadModel('grupo');
	$this->_presentacion = $this->loadModel('presentacion');
	$this->_marca = $this->loadModel('marca');
		
    }
    
    public function index($pagina = 1) {

        //$this->_acl->acceso('producto_agregar',5050,'');

        $this->_view->titulo = "Inventario";
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        //$depTrb = $this->_deposito->relacionDeposito(session::get('trabajador'));
        
        
        
        if($this->getPostParam('busqueda'))
        {
            if($this->getInt('deposito')>0)
            {
                $producto = $paginador->paginar($this->_producto->cargarProducto($this->getPostParam('busqueda'),$this->getInt('deposito')),$pagina,10);         
				//$producto = $this->_producto->cargarProducto_valor($this->getPostParam('busqueda'),$this->getInt('deposito'));         
            }else
            {
                $producto = $paginador->paginar($this->_producto->cargarProducto($this->getPostParam('busqueda')),$pagina,30);
				//$producto = $this->_producto->cargarProducto_valor($this->getPostParam('busqueda'));
            }
            
        }else
        {
            if($this->getInt('deposito')>0)
            {
                $producto =  $paginador->paginar($this->_producto->cargarProducto($this->getInt('deposito')),$pagina,10);               
            }else
            {
                $producto =  $paginador->paginar($this->_producto->cargarProducto(),$pagina,10);
            }
            
        }
        
		if(count($producto)== 0)
			$this->_view->info = "Busqueda sin Resultados ......";           
        //print_r($producto);
        //        exit();
        
        $this->_view->depAct = $this->getInt('deposito'); 
        $this->_view->producto = $producto;
        //$this->_view->depTrb = $depTrb; 
       
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/producto/index');	
		
	$this->_view->title = "Inventario";
	$this->_view->renderizar('index','almacen','Productos y Servicios');
        exit();
               
    }
    
	//-----------------------------------------------------------------------------------
	//metodo que agrega nuevo producto 
	//----------------------------------------------------------------------------------
    public function agregar()
    {
        if($this->getInt('guardar')==1)
        {       
            $modelo = $this->getPostParam('modelo');
            $modelo = (empty($modelo))?"N/A":$modelo;
            $file = $_FILES['files'];

            //print_r($_POST);exit();

            $datos = array(
                "codigo"=>  $this->getPostParam('codigo'),
                "nombre"=>  $this->getPostParam('nombre'),
                "marca"=>  $this->getInt('id_marca'),
                "presentacion"=>$this->getPostParam('id_presentacion'),
                "modelo"=>  $modelo,
                "comentario"=>  $this->getPostParam('comentario'),
		"clasificacion"=> $this->getPostParam('id_clasificacion'),
                "grupo"=>  $this->getInt('id_rubro'),
                "medida"=> $this->getPostParam('medida')
                );
			//print_r($datos);exit();	
            if($this->_producto->incluirProducto($datos))
            {
                if($file){
                    $this->getLibrary('uploadFile');
                    $upload = new uploadFile();
                    $upload->setFile($file);
                    $upload->setDirUpload(APP_PATH.'public'.DS.'img'.DS.'producto'.DS);
                    $upload->setRename($this->getPostParam('codigo'));
                    $upload->uploadFile();
                }

		session::destroy('producto');	
                $mensaje = "Producto Exitosamente Agregado.";
                $this->getMensaje("confirmacion",$mensaje);
                $this->redireccionar('almacen/producto/index');
                exit();                
            }else{
                   $this->_producto->regLog();
                   $this->_view->error = "Error guardando producto.";
                   $this->_view->renderizar('agregar','almacen');
                   exit();       
                 }           
                      
        }
		
        $this->_view->setJs(array("producto"));
        $this->_view->setJsPlugin(array('jquery-ui','validaciones','bootstrap-filestyle','funciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
        
        $this->_view->medida = $this->_medida->cargarUnidadMedida();        
        if(session::get('producto'))
		{
			$producto = session::get('producto');
			//$producto = array();
			//print_r($producto);exit();
		}else
		{
			$producto = array();						
		}
		
		if(count($producto))
		{
			if(isset($producto["clasificacion"]))
				$clasificacion = $this->_clasificacion->buscar((int)$producto["clasificacion"]);
			if(isset($producto["grupo"]))
				$grupo = $this->_grupo->buscar($producto['grupo']);
			else
				$grupo = array();
			
			if(isset($producto["presentacion"]))
				$presentacion = $this->_presentacion->buscar($producto['presentacion']);
			else
				$presentacion = array();
			
			if(isset($producto["marca"]))
				$marca = $this->_marca->buscar($producto['marca']);
			else
				$marca = array();
			
		
				//print_r($clasificacion); exit();		
				$this->_view->clasificacion = $clasificacion;
				$this->_view->grupo = $grupo;
				$this->_view->presentacion = $presentacion;
				$this->_view->marca  = $marca;
				
				$this->_view->codigo = (isset($producto['codigo']))?$producto['codigo']:"";
				$this->_view->nombre = (isset($producto['nombre']))?$producto['nombre']:"";
				$this->_view->modelo = (isset($producto['modelo']))?$producto['modelo']:"";
				$this->_view->uni_medida = (isset($producto['medida']))?$producto['medida']:"";
		}
		
	//$this->_view->producto = $producto;
	$this->_view->title = "Agregar Producto";
        $this->_view->renderizar('agregar','archivo');
        exit();
    }        
    
    //--------------------------------------------------------------------------
    //METODO QUE PERMITE EDITAR DATOS DE UN PRODUCTO
    //--------------------------------------------------------------------------
    public function editar($id)
    {
        if($this->getInt('guardar')==1)
        {
            $file = $_FILES['files'];
            //print_r($_FILES);exit();
            $datos = array(
                "id"=>    $this->getInt('id_producto'),
                "id_det"=>$this->getInt('id_det_producto'),
                "codigo"=>$this->getPostParam('codigo'),
                "nombre"=>$this->getPostParam('nombre'),
                "marca"=> $this->getInt('id_marca'),
                "modelo"=>$this->getPostParam('modelo'),
                "comentario"=>  $this->getPostParam('comentario'),
                "presentacion"=>  $this->getInt('id_presentacion'),
                "medida"=> $this->getPostParam('id_uni_med'),
                "clasificacion"=>  $this->getInt('id_clasificacion'),
                "grupo"=>  $this->getInt('id_grupo')
                );
            //print_r($datos);exit();
            if($this->_producto->editar($datos))
            {
                
                if($file){
                    $this->getLibrary('uploadFile');
                    $upload = new uploadFile();
                    $upload->setFile($file);
                    $upload->setDirUpload(APP_PATH.'public'.DS.'img'.DS.'producto'.DS);
                    $upload->setRename($this->getPostParam('codigo'));
                    if($upload->uploadFile())
                        die("bien");
                    else {
                        die("mal");
                    }
                }               

                $mensaje = "Producto editado correctamente.";
                $this->getMensaje("confirmacion",$mensaje);
                $this->redireccionar('almacen/producto/index');
                exit();                
            }else
            {
                $this->_producto->regLog();
                $this->_view->error = "Error editando producto.";
                $this->_view->renderizar('editar','archivo');
                exit();
            }
            
        }
        
        if($id)
        {
           $id=(int)$id; 
           $producto = $this->_producto->buscar($id);
           
           //print_r($producto);
           //exit();
           
           $this->_view->producto = $producto;     
        
           $this->_view->setJs(array("producto"));
           $this->_view->setJsPlugin(array('jquery-ui','validaciones','bootstrap-filestyle','funciones'));
           $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));

            //$this->_view->presentacion = $this->_presentacion->cargarPresentacion();
            //$this->_view->marca = $this->_marca->cargarMarca();            
            //$this->_view->medida = $this->_medida->cargarUnidadMedida();
            
           $this->_view->title = "Editar Producto";
           $this->_view->renderizar('editar','almacen','Registro de Inventario');
           exit();
        }
    }        
    
    
    
    public function detalle($id)
    {
        if($this->getInt('guardar')==1)
        {
                $datos = array(
                    "producto"=>  $this->getInt('producto'),
                    "presentacion"=> $this->getPostParam('nombre')
                    );
                if($this->_producto->incluirDetProducto($datos))
                {
                    $this->getMensaje("confirmacion","Presentación Asignada Correctamente");
                    $this->redireccionar('archivo/producto/index');
                    exit();
                    
                }else
                {
                    $this->_producto->regLog();
                    $this->getMensaje("error","Error guardando detalle de producto .....");
                    $this->_view->renderizar('detalle','archivo');
                    exit();
                }
            
        }
        if($this->getInt('guardar')==2)
        {
                $datos = array(
                    "producto"=>  $this->getInt('producto'),
                    "presentacion"=> $this->getPostParam('nombre'),
                    "id"=> $this->getPostParam('id')
                    );
                if($this->_producto->editarDetProducto($datos))
                {
                    $this->getMensaje("confirmacion","Presentación editada correctamente");
                    $this->redireccionar('archivo/producto/index');
                    exit();
                    
                }else
                {
                    $this->_producto->regLog();
                    $this->getMensaje("error","Error editando detalle de producto.");
                    $this->_view->renderizar('detalle','archivo');
                    exit();
                }
            
        }

        $this->_presentacion = $this->loadModel('presentacion');
        if($id)
        {
            $producto = $this->_producto->buscar($id);

            $detalle = $this->_producto->cargarDetProducto($id);
            $this->_view->producto = $producto;
        }

        $this->_view->setJs(array("relPre"));
        $this->_view->lista = $detalle;
        $this->_view->presentacion = $this->_presentacion->cargarPresentacion();
        
        $this->_view->title = "Detalle de ".ucfirst($producto['nombre_producto']);
        $this->_view->renderizar('detalle','archivo');
        exit();
        
        
    }

    public function activarDetalleProducto($id,$producto)
    {
        if($id)
        {
            $this->_producto->activarDetProducto($id);   
        }
        $this->redireccionar('archivo/producto/detalle/'.$producto);
        exit();   


    }
    
     public function desactivarDetalleProducto($id,$producto)
    {
        if($id)
        {
           $this->_producto->desactivarDetProducto($id);
        }    
        $this->redireccionar('archivo/producto/detalle/'.$producto);
        exit();

    }
    //==========================================================================
    //METODO QUE PERMITE BUSCAR LAS MARCAS POR COMPARACION
    //==========================================================================
    public function buscarMarca()
    {
         echo json_encode($this->_marca->buscarDescMarca($this->getGetParam('term')));       
    }
    //==========================================================================
    //METODO QUE PERMITE VALIDAR LAS MARCAS 
    //==========================================================================	
	public function validarMarca() 
	{
		echo json_encode($this->_marca->buscarMarca($this->getPostParam('valor')));		
	}   
    //==========================================================================
    //METODO QUE PERMITE BUSCAR LOS RUBROS POR COMPARACION
    //==========================================================================
    public function buscarGrupo()
    {
         echo json_encode($this->_grupo->autoGrupo($this->getGetParam('term')));       
    }
	
    //==========================================================================
    //METODO QUE PERMITE VALIDAR LAS RUBROS 
    //==========================================================================	
	public function validarRubro() 
	{
		echo json_encode($this->_grupo->cargarGrupoClasificacion($this->getPostParam('valor'),$this->getInt('ref')));		
	}
	
    //==========================================================================
    //METODO QUE PERMITE BUSCAR LAS PRESENTACIONES POR COMPARACION
    //==========================================================================
    public function buscarPresentacion()
    {
        echo json_encode($this->_presentacion->autoPresentacion($this->getGetParam('term')));       
    }
	
	public function validarPresentacion()
    {
        echo json_encode($this->_presentacion->buscarPresentacion($this->getPostParam('valor')));       
    }
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR LAS UNIDADES DE MEDIDA DE LOS PRODUCTOS
    //==========================================================================
    public function buscarMedida()
    {
        echo json_encode($this->_medida->buscarAutoMedida($this->getGetParam('term')));       
    }
    
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR EL CODIGO DEL PRODUCTO
    //==========================================================================
    public function buscarCodigo()
    {
        echo json_encode($this->_producto->buscarCodProducto($this->getPostParam('codigo')));       
    }

	
    //==========================================================================
    //METODO QUE PERMITE BUSCAR EL PROUCTO POR SU NOMBRE
    //==========================================================================
	public function validarNombreProducto()
	{
		echo json_encode($this->_producto->buscar($this->getPostParam('nombre')));       		
		
	}
    //-----------------------------------------------------------------------------
    //
    //---------------------------------------------------------------------------
	public function cargarProductoClasificacion()
	{
		echo json_encode($this->_producto->cargarProductoCategoria($this->getPostParam('v1'),$this->getPostParam('v2')));		
	}
	
    //==========================================================================
    //METODO QUE PERMITE BUSCAR LAS CLASIFICACIONES POR COMPARACION
    //==========================================================================	
	public function buscarClasificacion()
	{
		echo json_encode($this->_clasificacion->autoClasificacion($this->getGetParam('term')));		
	}
    //==========================================================================
    //METODO QUE PERMITE VALIDAR LAS CLASIFICACIONES 
    //==========================================================================	
	public function validarClasificacion() 
	{
		echo json_encode($this->_clasificacion->buscarClasificacion($this->getPostParam('valor')));		
	}
	
	

        
        public function respaldarProducto()
	{
		$clave = $this->getPostParam('clave');
		$valor = $this->getPostParam('valor');
		
		if(session::get('producto'))
		{
			$producto = session::get('producto');
			if(!array_key_exists($clave,$producto))
			{
				$producto[$clave] = $valor; 				
			}else
			{
				$producto[$clave] = $valor; 	
			//	return false;	
			}	
		}else{
			$producto[$clave] = $valor; 						
		}
		
		session::set("producto",$producto);
		//return true;
	}
	
	
}