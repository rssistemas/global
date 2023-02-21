<?php
class impuestoController extends archivoController
{
	private $_impuesto;
	
	public function __construct()
	{
		parent::__construct();
		$this->_impuesto = $this->loadModel('impuesto');
				
	}
	public function index($pagina = 1)
	{
		//define el titulo de la presente vista
        $this->_view->title = "Impuestos";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('impuesto'));
        
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_impuesto->cargarImpuesto($this->getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_impuesto->cargarImpuesto(),$pagina);
        }
        
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/impuesto/index');
        
        $this->_view->renderizar('index','archivo');
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
				'operador'   => $this->getPostParam('operador'),
				'comparador' => $this->getPostParam('valor'),
				'tipo'       => $this->getPostParam('tipo')				
				);
			
			//print_r($datos);exit();	
			if($this->_impuesto->insertar($datos))
			{
				$this->redireccionar('archivo/impuesto/index');
				exit();
				
			}else
				{
					$this->_impuesto->regLog();
					$this->redireccionar('archivo/impuesto/index');
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
				'operador'   => $this->getPostParam('operador'),
				'comparador' => $this->getPostParam('valor'),
				'tipo'       => $this->getPostParam('tipo'),
				'id'         => $this->getInt('id')	
				);
			
			//print_r($datos);exit();	
			if($this->_impuesto->modificar($datos))
			{
				$this->redireccionar('archivo/impuesto/index');
				exit();
				
			}else
				{
					$this->_impuesto->regLog();
					$this->redireccionar('archivo/impuesto/index');
					exit();						
				}
			
		}	

	}




	//-----------------------------------------------------------------------------
	//METODO QUE BUSCA IMPUESTO 
	//----------------------------------------------------------------------------
	public function buscarImpuesto()
	{
		
		echo json_encode($this->_impuesto->buscar((int)$this->getPostParam('valor')));
		
	}
	
	//-----------------------------------------------------------------------------
	//METODO QUE CARGA IMPUESTO 
	//----------------------------------------------------------------------------
	public function cargarImpuesto()
	{
		
		echo json_encode($this->_impuesto->cargarImpuesto());
		
	}
	
}


