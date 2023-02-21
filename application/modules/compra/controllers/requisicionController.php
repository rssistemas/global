<?php
class requisicionController extends compraController
{
    private $_requisicion;
    private $_unidad;
    public function __construct() {
        parent::__construct();
        $this->_requisicion = $this->loadModel('requisicion');

       // $this->_marca = $this->loadModel('marca','archivo');
        //$this->_proveedor = $this->loadModel('proveedor','compra');
        $this->_deposito  = $this->loadModel('deposito','almacen');
       // $this->_producto  = $this->loadModel('producto','almacen');
       // $this->_presentacion = $this->loadModel('presentacion','almacen');

		//$this->_despacho  = $this->loadModel('despacho');
        ///$this->_planificacion = $this->loadModel('planificacion','logistica');
        //$this->_solicitud = $this->loadModel('solicitud','logistica');
        //$this->_incidencia = $this->loadModel('incidencia','logistica');
        //$this->_departamento = $this->loadModel('departamento','archivo');
        $this->_unidad  =  $this->loadModel('unidad','archivo');
    }
	//---------------------------------------------------------------------------------
	//
	//---------------------------------------------------------------------------------
    public function index($pagina = 1 )
    {
        $this->_view->setJs(array('requisicion'));
        $this->_view->setJsPlugin(array('jquery-ui'));
        $this->_view->setCssPlugin(array('utilcss'));
        
        $empresa = session::get('actEmp');
        
        $depTrb = $this->_deposito->relacionDepositoAct(session::get('id_usuario'));

        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if(validate::getInt('deposito')>0)
        {
            if(validate::getPostParam('busqueda'))
            {
                $this->_view->lista = $paginador->paginar($this->_requisicion->listarRequisicion(validate::getInt('deposito'),$this->getPostParam('busqueda'),$empresa[0]['id_empresa']),$pagina);
            }else
            {
                 $this->_view->lista = $paginador->paginar($this->_requisicion->listarRequisicion(validate::getInt('deposito'),false,$empresa[0]['id_empresa']),$pagina);
            }

        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_requisicion->listarRequisicion(false,false,$empresa[0]['id_empresa']),$pagina);
        }
            $this->_view->paginacion = $paginador->getView('paginacion','compra/requisicion/index');

        $this->_view->depAct = validate::getInt('deposito');
        $this->_view->depTrb = $depTrb;



        $this->_view->title= "Requisicion de Compra";
        $this->_view->renderizar('index','compra','Requisicion de Compra');
        exit();

    }


    //---------------------------------------------------------------------------------------
    //METODO QUE PERMITE CREAR UNA REQUISICION DIRECTA
    //---------------------------------------------------------------------------------------
    public function agregar()
	{
        if(validate::getInt('guardar')==1)
        {
			
			//buscar el tipo de docmento
            $empresa = session::get('actEmp');
            //print_r($empresa);exit();
            $datos = array(
                    "unidad"=>      validate::getInt('unidad'),
                    "deposito"=>    validate::getInt('almacen'),
                    "plazo"=>       validate::getInt('plazo'),
					"requerida"=>   validate::getPostParam('fecha_requerida'),
                    "tipo_mae"=>    "DIRECTA",
                    "tipo_det"=>    validate::getPostParam('tipo'),     
                    "prioridad"=>   validate::getPostParam('prioridad'),
                    "motivo"=>      validate::getPostParam('motivo'),
                    "comentario"=>  validate::getPostParam('comentario'),
                    "producto"=>    validate::getPostParam('id'),
                    "cantidad"=>    validate::getPostParam('cantidad'),
                    "medida"=>      validate::getPostParam('medida'),
                    "marca"=>       validate::getPostParam('marca'),
                    "usuario"=>     session::get('id_usuario'),
                    "empresa"=>     $empresa[0]['id_empresa']

			);

			//print_r($datos);exit();
            if($this->_requisicion->insertar($datos))
            {
                $this->redireccionar('compra/requisicion/index/');
                exit();
            }else
            {
				$this->_requisicion->regLog();
                $this->_view->error = "Error guardando requisicion .....";
                 //$this->_view->renderizar('a','transaccion');
                 //exit();
            }

        }
		//print_r($_SESSION);exit();
        $this->_view->setJsPlugin(array('jquery-ui','validaciones','funciones'));
        $this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
        $this->_view->setJs(array("requisicion"));
		
        $this->_view->unidad = $this->_unidad->cargarUnidadUsuario(session::get('id_usuario'));

		$this->_view->ced_usuario = session::get('cedula');
		$this->_view->nom_usuario = session::get('nombre');
		$this->_view->ape_usuario = session::get('apellido');
		$this->_view->id_usuario  = session::get('id_usuario');


        $this->_view->title = "Requisicion de Productos";
        $this->_view->renderizar('agregar','compra','Requisicion de Compra');
        exit();

    }
//---------------------------------------------------------------------------------------
//METODO QUE ANALIZA LAS REQUISICIONES
//---------------------------------------------------------------------------------------
    public function control($pagina = 1)
    {

        $this->_view->setJsPlugin(array('jquery-ui','Chart1'));
        $this->_view->setJs(array('requisicion','control','grafestreq'));

		$empresa = session::get('actEmp');
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if(validate::getInt('deposito')>0)
        {
            if(validate::getPostParam('busqueda'))
            {
                $this->_view->lista = $paginador->paginar($this->_requisicion->analisis(validate::getInt('deposito'),validate::getPostParam('busqueda')),$pagina);
            }else
            {
                 $this->_view->lista = $paginador->paginar($this->_requisicion->analisis(validate::getInt('deposito')),$pagina);
            }

        }
        else
            {
                    //$this->_view->lista = $paginador->paginar($this->_requisicion->analisis(),$pagina);
                    $lista = $paginador->paginar($this->_requisicion->analisis(),$pagina);
                    //print_r($lista);exit();
            }
        $this->_view->lista = $lista;

        $this->_view->paginacion = $paginador->getView('paginacion','compra/requisicion/index');

        $this->_view->unidad = $this->_unidad->cargarUnidadUsuario(session::get('id_usuario'));
        $this->_view->depTrb = $this->_deposito->relacionDepositoAct(session::get('id_usuario'));


        $this->_view->title = "Requisiciones de Compra";
        $this->_view->renderizar('control','compra','Requisicion de Compra');
        exit();

    }
    //--------------------------------------------------------------------------
    //METODO ELIMINAR, desactiva un registro de requisicion de compra 
    //--------------------------------------------------------------------------
    public function eliminar()
    {





    }        
	
	
	
    //---------------------------------------------------------------------------------
    //METODO QUE BUSCA DATOS mESTRO  / DETALLE DE UNA REQUISICION DADA
    //----------------------------------------------------------------------------------
    public function buscarRequisicion()
    {	
	echo json_encode($this->_requisicion->buscarRequisicion(validate::getInt('valor')));		
    }
	
	//----------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------
	public function buscarTipoMovimiento()
    {
        $inventario = $this->loadModel('inventario');
        echo json_encode($inventario->buscarTipoMovimiento(validate::getInt('valor')));
    }

    //==========================================================================
    //METODO QUE PERMITE BUSCAR UN PRODUCTO POR NOMBRE EN EL CATALOGO GENERAL
    //==========================================================================
    public function buscarProducto()
    {
	$producto = $this->loadModel('producto','almacen');
	 echo json_encode($producto->buscar(validate::getInt('valor')));
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
    //METODO QUE CARGA LISTADO DE SERVICIO POR SU NOMBRE EN FORMATO JSON
    //---------------------------------------------------------------------
    public function cargarServicio()
    {
    	$servicio = $this->loadModel('servicio','archivo');
        echo json_encode($servicio->cargarServicio(validate::getPostParam('valor')));
    }

    //==========================================================================
    //METODO QUE PERMITE BUSCAR UN PRODUCTO POR NOMBRE EN EL CATALOGO GENERAL
    //==========================================================================
    public function buscarServicio()
    {
		$servicio = $this->loadModel('servicio','archivo');
		 echo json_encode($servicio->buscar(validate::getInt('valor')));
    }
    //-------------------------------------------------------------------------
    //METODO QUE CARGA LOS DATOS DE ANALISIS PARA LA APROBACION DE REQUISITOS
    // UTILIZADO EN EVALUACION Y CONTROL DEL MODULO REQUISICION 18-06-2018
    //-------------------------------------------------------------------------
    public function cargarAnalisis()
    {
        echo json_encode($this->_requisicion->analisis(validate::getInt('req')));
    }
    //--------------------------------------------------------------------------
    //METODO QUE GUARDA EVALUACION DE REQUISITO
    //--------------------------------------------------------------------------
    public function evaluarRequisito()
    {
        $usuario = session::get('id_usuario');
        echo json_encode($this->_requisicion->evaluar(validate::getInt('codigo'),validate::getPostParam('comentario'),validate::getPostParam('valor'),$usuario));
    }
    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    public function eliminarSolicitud()
    {
       echo json_encode($this->_requisicion->desactivar($this->getInt('req')));  
        
    }        
    
}
