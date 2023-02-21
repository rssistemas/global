<?php
class errorController extends controller
{
	private $_error;
		
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		
	}
	public function alerta($codigo,$url=false)
	{
		$this->getLibrary('alerta');	
		$alerta = new alerta();
		if(!$url)
			$url="perfil/index";
		else
			$url = str_replace('-','/',$url);
				
		$msj = $alerta->getMensajes('alerta',(int) $codigo);
		//$msj = "prueba";
		$this->_view->valor = $msj;
		$this->_view->destino = $url;
		$this->_view->title = "Gestor de Mensajes";
		$this->_view->renderizar('alertabox');
        exit();
	}
	
	public function error()
	{
		
		
		
	}
	
	public function aviso()
	{
		
		
		
	}
	
}
