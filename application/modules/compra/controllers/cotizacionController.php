<?php
class cotizacionController extends compraController
{
    private $_cotizacion;
    private $_requisicion;
    private $_unidad;
    private $_empresa;
    
    public function __construct()
    {
        parent::__construct();
        $this->_cotizacion = $this->loadModel('cotizacion');
        $this->_requisicion = $this->loadModel('requisicion','almacen');
        $this->_unidad = $this->loadModel('unidad','archivo');
        $this->_empresa = session::get('actEmp');
    }        
    
    public function index($pagina = 1)
    {

            $this->_view->title = "Cotizacion de Compra ";
            $this->getLibrary('paginador');

            $this->_view->setJsPlugin(array('jquery-ui','validaciones'));
            //$this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
            //$this->_view->setJs(array('cotizacion'));
            $paginador = new Paginador();
            //$paginador->setRango(FALSE);
            if($this->getPostParam('busqueda'))
            {
                $datos = $paginador->paginar($this->_requisicion->cargarSolicitud($this->getPostParam('busqueda')),$pagina,10);         
            }
            else
            {
                $datos =  $paginador->paginar($this->_cotizacion->cargarSolicitud(),$pagina,10);
            }

            $this->_view->cotizacion = $datos;
            $this->_view->paginacion = $paginador->getView('paginacion','compra/cotizacion/index');	
            $this->_view->renderizar('index','compra','Cotizacion de Compra');
            exit();

    }
    
    
    public function agregar()
    {
        $this->_view->setJs(array('cotizacion'));
        
        
        $this->_view->title = "Cotizacion de Compra ";
        $this->_view->unidad = $this->_unidad ->cargarUnidadUsuario(session::get('id_usuario'));
        $this->_view->empresa = $this->_empresa;
        
        $this->_view->renderizar('agregar','compra','Cotizacion de Compra');
        exit();
    }        
            
    
    //--------------------------------------------------------------------------------
    //METODO QUE BUSCA DATOS DE REQUISITO TIPO PRODUCTO, CON CONDICION POR COTIZAR 
    //--------------------------------------------------------------------------------
    public function buscarProducto()
    {   
        echo json_encode($this->_cotizacion->buscarRequisitoProducto($this->getInt('empresa'),$this->getInt('unidad'),$this->getPostParam('valor')));               
  
    }
    
    public function cargarProducto()
    {   
        echo json_encode($this->_cotizacion->requisitoProducto($this->getInt('empresa'),$this->getInt('unidad'),$this->getPostParam('valor')));               
  
    }
    
    
     //--------------------------------------------------------------------------------
    //METODO QUE BUSCA DATOS DE REQUISITO TIPO PRODUCTO, CON CONDICION POR COTIZAR 
    //--------------------------------------------------------------------------------
    public function buscarServicio()
    {
        echo json_encode($this->_cotizacion->buscarRequisitoServicio($this->getInt('empresa'),$this->getInt('unidad'),$this->getPostParam('valor')));        
    }
    
    public function cargarServicio()
    {
        echo json_encode($this->_cotizacion->requisitoServicio($this->getInt('empresa'),$this->getInt('unidad'),$this->getPostParam('valor')));        
    }
        
}