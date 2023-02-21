<?php 
	class presupuestoController extends compraController
	{
		
		private $_presupuesto;
		
		
		public function __construct()
		{
			parent::__construct();
			$this->_presupuesto = $this->loadModel('presupuesto');
			
		}
		
		
		public function index($pagina = 1)
		{
			
			
			$this->_view->title = "Presupuesto de Compras ";
			$this->getLibrary('paginador');
			$this->_view->setJs(array('presupuesto'));
			$this->_view->setJsPlugin(array('validaciones','jquery-ui'));
			$paginador = new Paginador();
			
			
			if($this->getPostParam('busqueda'))
			{
				$presupuesto = $paginador->paginar($this->_presupuesto->cargarPresupuesto($this->getPostParam('busqueda')),$pagina,10);         
			}
			else
			{
				$presupuesto =  $paginador->paginar($this->_presupuesto->cargarPresupuesto(),$pagina,10);
			}
			if(count($presupuesto)<1)
			{
				$this->_view->info = "Busqueda sin Resultados ......";
			}
			
			$this->_view->presupuesto = $presupuesto;
			$this->_view->paginacion = $paginador->getView('paginacion','compra/presupuesto/index');	
			$this->_view->renderizar('index','compra','Presupuesto de Compra');
			exit();
			
			
			
			
		}
	
	}
	
?>	