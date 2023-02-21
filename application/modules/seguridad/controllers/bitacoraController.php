<?php
class bitacoraController extends seguridadController
{
    private $_bitacora;
    private $_registry;
    protected $_guachiman;
    public function __construct() {
        parent::__construct();
        $this->_registry = registry::getInstancia();
        $this->_guachiman = $this->_registry->_guachiman;
        $this->_bitacora = $this->loadModel('bitacora');
    }
    public function index($pagina = 1)
    {
        
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        $this->_view->lista = $paginador->paginar($this->_bitacora->consultar(),$pagina);
        $this->_view->paginacion = $paginador->getView('paginacion','seguridad/bitacora/index');	
        $this->_view->conectados = $this->_guachiman->usuariosSession();
        $this->_view->titulo = "Bitacora";
        $this->_view->renderizar('index','seguridad');
        exit();
    }        
    
}