<?php 
class clienteController extends ventaController
{
	private $_cliente;
	private $_estado;
	private $_municipio;
	private $_parroquia;
	private $_sector;
	
	public function __construct()
	{
		parent::__construct();
		$this->_cliente = $this->loadModel('cliente');
		$this->_estado = $this->loadModel('estado','configuracion');
		$this->_municipio = $this->loadModel('municipio','configuracion');
		$this->_parroquia = $this->loadModel('parroquia','configuracion');
		$this->_sector = $this->loadModel('sector','configuracion');
	}
	
	
	public function index($pagina = 1)
	{
		$this->getLibrary('paginador');
        $this->_view->setJs(array("cliente"));
		
        $paginador = new Paginador();
        if($this->getPostParam('busqueda'))
        {
            $this->_view->lista =  $paginador->paginar($this->_cliente->cargarCliente($this->getPostParam('busqueda')),$pagina);
        }else{
            $this->_view->lista =  $paginador->paginar($this->_cliente->cargarCliente(),$pagina);
        }    
            
        $this->_view->paginacion = $paginador->getView('paginacion','venta/cliente/index');
        $this->_view->title="Clientes";
        $this->_view->renderizar('index','venta','Clientes');
        exit();
	}
	
	public function agregar()
	{
		
		if($this->getInt('guardar')==1)
		{
			//print_r($_POST);exit();
			
			$nac = $this->getPostParam('nacionalidad');
			$cedula = $this->getInt('cedula');
			
			$rif = $nac.$cedula;
			$razon_social = $this->getSql('razon_social');
			$denominacion = $this->getSql('denominacion');
			$sector = $this->getInt('sector');
			$estado = $this->getInt('estado');
			$municipio = $this->getInt('municipio');
			$parroquia = $this->getInt('parroquia');
			$direccion = $this->getSql('direccion');
			$celular = $this->getPostParam('celular');
			$local = $this->getPostParam('local');
			$correo = $this->getPostParam('correo');
			$tipo = $this->getInt('contribuyente');
			$credito = $this->getInt('credito');
			$limite = $this->getPostParam('limite');
			
			$datos = array(
					"rif"=>$rif,
					"razon_social"=>strtoupper($razon_social),
					"denominacion"=>strtoupper($denominacion),
					"direccion"=>strtoupper($direccion),
					"local"=>$local,
					"celular"=>$celular,
					"correo"=>$correo,
					"tipo"=>$tipo,
					"sector"=>$sector,
					"credito"=>$credito,
					"limite"=>$limite,
					"estado"=>$estado,
					"municipio"=>$municipio,
					"parroquia"=>$parroquia
					);
			
			if($this->_cliente->insertar($datos))
			{		
				$this->redireccionar('venta/cliente/index');
				exit();
			}else{
				$this->_cliente->regLog();
				$this->redireccionar('venta/cliente/index');
				exit();	
			}
		}
		$this->_view->setJs(array("cliente"));
		$this->_view->esta = $this->_estado->cargarEstado();
		$this->_view->title="Nuevo Cliente";
        $this->_view->renderizar('agregar','venta','clientes');
        exit();
	}
	
	public function editar($id=false)
	{
		//$this->_estado = $this->loadModel('estado','configuracion');
		//$this->_municipio = $this->loadModel('municipio','configuracion');
		//$this->_parroquia = $this->loadModel('parroquia','configuracion');
		//$this->_sector = $this->loadModel('sector','configuracion');
		
		
		$this->_view->setJs(array("cliente"));
		if($this->getInt("guardar")==2)
		{
			//print_r($_POST);exit();
			$codigo = $this->getInt('id_cliente');
			//$nac = $this->getPostParam('nacionalidad');
			$cedula = $this->getPostParam('cedula');
			
			$razon_social = $this->getSql('razon_social');
			$denominacion = $this->getSql('denominacion');
			$sector = $this->getInt('sector');
			$estado = $this->getInt('estado');
			$municipio = $this->getInt('municipio');
			$parroquia = $this->getInt('parroquia');
			$direccion = $this->getSql('direccion');
			$celular = $this->getPostParam('celular');
			$local = $this->getPostParam('local');
			$correo = $this->getPostParam('correo');
			$tipo = $this->getInt('tipo');
			$credito = $this->getInt('credito');
			$limite = $this->getPostParam('limite');
			
			$datos = array(
					"rif"=>$cedula,
					"razon_social"=>strtoupper($razon_social),
					"denominacion"=>strtoupper($denominacion),
					"direccion"=>strtoupper($direccion),
					"local"=>$local,
					"celular"=>$celular,
					"correo"=>$correo,
					"tipo"=>$tipo,
					"sector"=>$sector,
					"credito"=>$credito,
					"limite"=>$limite,
					"codigo"=>$codigo,
					"estado"=>$estado,
					"municipio"=>$municipio,
					"parroquia"=>$parroquia
					);
			
			if($this->_cliente->modificar($datos))
			{		
				$this->redireccionar('venta/cliente/index');
				exit();
			}else{
				$this->_view->error = "Error Editando Cliente ".$this->_cliente->regLog();
				//$this->redireccionar('venta/cliente/index');
				//exit();	
			}
	
		}		
		if($id)
		{
			$datos = $this->_cliente->buscar((int)$id);					
		}else{
		
		
		}
		
		$this->_view->estado = $this->_estado->cargarEstado();
		$this->_view->municipio = $this->_municipio->cargarMunicipio();
		$this->_view->parroquia = $this->_parroquia->cargarParroquia();
		$this->_view->sector = $this->_sector->cargarSector();
		
		$this->_view->localidad = $this->_sector->buscarLocalidad($datos[0]['sector_id']);
		
		$this->_view->datos = $datos;
		
		$this->_view->title="Editar Cliente";
        $this->_view->renderizar('editar');
        exit();
		
	}
	
	public function eliminar()
	{
		echo json_encode($this->_cliente->eliminar($this->getInt('valor')));	
	}
	
	public function detalle($id)
	{
		
		
		$this->_view->id = $id;
		$this->_view->title="Detalle de Cliente";
        $this->_view->renderizar('detalle');
        exit();
	}
	public function premisaCliente($id = false)
	{
			
		if($this->getPostParam('guardar')==1)	
		{
			$id = $this->getInt('cliente');
			$datos = array(
				"premisa"=>$this->getInt('premisa'),
				"cliente"=>$id
			);
			if($this->_cliente->asignarPremisa($datos))
			{
				$this->redireccionar('venta/cliente/premisaClinete/'.$id);
				exit();
			}else
				{
					$this->_cliente->regLog();
					$this->_view->error = "Error asignando premisa ...";
				}
			
		}	
			
		$premisa = $this->loadModel('premisa');	
		//listado de premisas asignadas
		$this->_view->lista = $premisa->buscarRelacion($id,'VENTA');
		
		//lista de premisas por asignar
		$this->_view->premisa = $premisa->cargarPremisa(FALSE,'VENTA');
		
		
		
		$this->_view->cliente = $id;
		$this->_view->setJs(array("premisa"));
		$this->_view->title="Detalle de Cliente";
        $this->_view->renderizar('premisa');
        exit();
		
	}
	
	
	
	public function buscarCliente()
	{
		 echo json_encode($this->_cliente->buscarRifCliente($this->getPostParam('tipo'),$this->getPostParam('cedula')));		
	} 
	
	public function buscarPremisaCliente()
	{
		 echo json_encode($this->_cliente->buscarPremisaCliente($this->getPostParam('cliente'),$this->getPostParam('premisa')));		
	}
	
	//----------------------------------------------------------------------------
	//METODO QUE COMPRUEBA SI UN CORREO YA ESTA USADO POR UN CLIENTE
	//----------------------------------------------------------------------------
	public function comprobarCorreo()
	{
		 echo json_encode($this->_cliente->buscarMunicipios($this->getInt('valor')));		
	}
	//-------------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA DESCUENTOS DEL CLIENTE EN PREMISAS 
	//-------------------------------------------------------------------------------------------------------
	public function buscarDescuentoCliente()
	{
		$descuento = 0;
		$monto   =  $this->getPostParam('monto');
		$premisa = 	$this->_cliente->cargarPremisaCliente($this->getInt('cliente'));
		if(count($premisa)>0)
		{
			foreach($premisa as $pre)
			{
				
				if($pre['accion_premisa']=='RESTA')
				{
					if($pre['operador_premisa'] =='MAYOR')
					{
						$descuento = ($monto > $pre['comparador_premisa'])?$descuento + $pre['porcentaje_premisa']: $descuento;										 	
					}
					if($pre['operador_premisa'] =='MENOR')
					{
						$descuento = ($monto < $pre['comparador_premisa'])?$descuento + $pre['porcentaje_premisa']: $descuento;										 	
					}
					if($pre['operador_premisa'] =='IGUAL')
					{
						$descuento = ($monto == $pre['comparador_premisa'])?$descuento + $pre['porcentaje_premisa']: $descuento;										 	
					}
				}
				
			}
			
		}
		//$descuento = 5.2;
		 echo json_encode(array("descuento"=>$descuento));
		//echo json_encode(die($condicion));		
	}
	
	//----------------------------------------------------------------------------
	//CARGAR MUNICIPIOS DE UN ESTADO PASADO POR PARAMETROS
	//----------------------------------------------------------------------------
	public function buscarMunicipioEstado()
	{
		 echo json_encode($this->_municipio->buscarMunicipios($this->getInt('valor')));		
	}
	
	//----------------------------------------------------------------------------
	//CARGAR PARROQUIAS DE UN MUNICIPIO PASADO POR PARAMETROS
	//----------------------------------------------------------------------------
	public function buscarParroquiaMunicipio()
	{
		 echo json_encode($this->_parroquia->buscarParroquias($this->getInt('valor')));		
	}
	
	//----------------------------------------------------------------------------
	//CARGAR SETORES DE UNA PARROQUIA PASADA POR PARAMETRO
	//----------------------------------------------------------------------------
	public function buscarSectorParroquia()
	{
		 echo json_encode($this->_sector->buscarSectores($this->getInt('valor')));		
	}
}

