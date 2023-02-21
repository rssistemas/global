<?php 
class premisaController extends ventaController
{
	private $_premisa;
	public function __construct()
	{
		parent::__construct();
		$this->_premisa = $this->loadModel('premisa');
	}
	
	public function index($pagina = 1)
	{
		$this->getLibrary('paginador');
        $this->_view->setJs(array("premisa"));
		
        $paginador = new Paginador();
        if($this->getPostParam('busqueda'))
        {
            $premisa = 	$paginador->paginar($this->_premisa->cargarPremisa($this->getPostParam('busqueda')),$pagina);
             
        }else{
            $premisa =  $paginador->paginar($this->_premisa->cargarPremisa(),$pagina);
        }    
        if(count($premisa)<1)
		{
			$this->_view->info = "Busqueda sin Resultados ...";
		}
		$this->_view->premisa = $premisa;    
        $this->_view->paginacion = $paginador->getView('paginacion','venta/premisa/index');
        $this->_view->title="Premisas";
        $this->_view->renderizar('index');
        exit();
		
	}
	
	public function agregar()
	{
		if($this->getPostParam('guardar')== 1)
		{
			//print_r($_POST);exit();
									
			$datos = array(				
				'nombre'     => $this->getPostParam('nombre'),
				'descripcion'=> $this->getPostParam('descripcion'),
				'porcentaje' => $this->getPostParam('porcentaje'),
				'accion'     => $this->getPostParam('accion'),
				'operador'  => $this->getPostParam('operador'),
				'valor'	     => $this->getPostParam('valor'),
				'tipo'       => $this->getPostParam('tipo')				
				);
			
			//print_r($datos);exit();	
			if($this->_premisa->insertar($datos))
			{
				$this->redireccionar('venta/premisa/index');
				exit();
				
			}else
				{
					$this->_premisa->regLog();
					$this->redireccionar('venta/premisa/index');
					exit();						
				}	
				
			
		}
		if($this->getPostParam('guardar')== 2)
		{
			//print_r($_POST);exit();
									
			$datos = array(				
				'nombre'     => $this->getPostParam('nombre'),
				'descripcion'=> $this->getPostParam('descripcion'),
				'porcentaje' => $this->getPostParam('porcentaje'),
				'accion'     => $this->getPostParam('accion'),
				'condicion'  => $this->getPostParam('condicion'),
				'operador'  => $this->getPostParam('operador'),
				'valor'	     => $this->getPostParam('valor'),
				'tipo'       => $this->getPostParam('tipo'),
				'id'         => $this->getInt('id')	
				);
			
			//print_r($datos);exit();	
			if($this->_premisa->modificar($datos))
			{
				$this->redireccionar('venta/premisa/index');
				exit();
				
			}else
				{
					$this->_premisa->regLog();
					$this->redireccionar('venta/premisa/index');
					exit();						
				}
			
		}	

	}
	
	
	//-----------------------------------------------------------------------------
	//METODO QUE BUSCA UNA PREMISA
	//----------------------------------------------------------------------------
	public function buscarPremisa()
	{
		
		echo json_encode($this->_premisa->buscar((int)$this->getPostParam('valor')));
		
	}	
	//-----------------------------------------------------------------------------
	//METODO QUE BUSCA UNA PREMISA
	//----------------------------------------------------------------------------
	public function comprobarPremisa()
	{
		
		echo json_encode($this->_premisa->buscar($this->getPostParam('valor')));
		
	}	
	//-----------------------------------------------------------------------------
	//METODO QUE ELIMINA UNA PREMISA
	//----------------------------------------------------------------------------
	public function eliminarPremisa()
	{
		
		echo json_encode($this->_premisa->eliminar($this->getPostParam('valor')));
		
	}
	
	//-----------------------------------------------------------------------------
	//METODO QUE BUSCA LAS RELACIONES DE LAS PREMISAS
	//----------------------------------------------------------------------------
	public function relacionPremisa()
	{
		
		echo json_encode($this->_premisa->buscarRelacion($this->getPostParam('valor')));
		
	}

}
