<?php
class bancoController extends archivoController
{
	private $_banco;
	public function __construct()
	{
		parent::__construct();
		
		$this->_banco = $this->loadModel('banco');
	}
	
	public function index($pagina = 1)
	{
		$this->getLibrary('paginador');
        $paginador = new Paginador();
		
		$this->_view->setJs(array('banco'));
		
		
		if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_banco->cargarBancos($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_banco->cargarBancos(),$pagina);
        }
		
		$this->_view->title = "Listado de Bancos ";
		$this->_view->renderizar('index');
		exit();
		
	}
	
	public function agregar()
    {
        if($this->getInt('guardar')==1)
        {
            $datos = array(
                "nombre"   =>  $this->getPostParam('nombre'),
                "direccion"=>  $this->getPostParam('direccion'),
				"telefono" =>  $this->getPostParam('telefono'),
				"correo"   =>  $this->getPostParam('correo')				
				);
				
            if($this->_banco->insertarBanco($datos))
            {
                $this->redireccionar('archivo/banco/index/','archivo');
                exit();
            }
            else
            {
                $this->_view->error = "Error guardando registro nuevo .....".$this->_empresa->regLog();
                //$this->_view->renderizar('agregar','archivo');
                //exit();
            }
        }
        else
        {
            
            exit();
        }
    }



	public function comprobarBanco()
	{
		echo json_encode($this->_banco->contarBanco($this->getPostParam('valor')));  		
	}
	
	public function cargarBanco()
	{
		echo json_encode($this->_banco->cargarBancos());  		
	}
	
} 