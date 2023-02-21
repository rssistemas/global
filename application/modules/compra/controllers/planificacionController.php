<?php
	class planificacionController extends compraController
	{
		private $_pln;
		private $_deposito; 
		private $_unidad;	
		private $_concepto;
		private $_servicio;
		private $_dpto;
		private $_trabajador;
		private $_producto;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->_pln       = $this->loadModel('planificacion');
			$this->_deposito  = $this->loadModel('deposito','almacen');
			$this->_unidad  =   $this->loadModel('unidad','archivo');
			$this->_concepto =  $this->loadModel('concepto');
			$this->_servicio =  $this->loadModel('servicio','archivo');
			$this->_dpto =      $this->loadModel('departamento','rrhh');
			
		}
	
		
		
		public function index($pagina = 1)
		{
			
			$this->_view->setJs(array('planificacion'));
			$this->_view->setJsPlugin(array('jquery-ui'));
			$depTrb = $this->_deposito->relacionDepositoAct(session::get('id_usuario'));
			
			$this->getLibrary('paginador');
			$paginador = new Paginador();
			if($this->getInt('deposito')>0)
			{
				if($this->getPostParam('busqueda'))
				{
					$this->_view->lista = $paginador->paginar($this->_pln->cargarPln($this->getInt('deposito'),$this->getPostParam('busqueda')),$pagina);
				}else
				{
					 $this->_view->lista = $paginador->paginar($this->_pln->cargarPln($this->getInt('deposito')),$pagina);        
				}
				
			}
			else
			{
				$data = $paginador->paginar($this->_pln->cargarPln(),$pagina);
			}
				$this->_view->paginacion = $paginador->getView('paginacion','compra/planificacion/index'); 
			
			//print_r($data); exit();
			
			$this->_view->lista = $data;
			
			$this->_view->title= "Planificacion de Compra";
			$this->_view->renderizar('index','compra','Planificacion de Compra');
			exit();
				
		}
		
		//-------------------------------------------------------------------------
		//METODO QUE AGREGA UNA PLANIFICACION DE COMPRAS
		//-------------------------------------------------------------------------
		public function agregar()
		{
						
			if($this->getInt('guardar')==1)
			{
				//print_r($_POST);exit();
				//buscar el tipo de docmento 
				$empresa = session::get('empresa');
				
				//print_r($empresa);exit();
				
				$datos = array(
					"unidad"=>      $this->getInt('unidad'),
					"dependencia"=> $this->getInt('dependencia'),
					"inicio"=>   	$this->getPostParam('inicio'),
					"fin"=>   		$this->getPostParam('fin'),
					"objetivo"=>    $this->getPostParam('objetivo'),
					"ref_contable"=>$this->getPostParam('ref_contable'),
					"comentario"=>  $this->getPostParam('comentario'),
					"usuario"=>     session::get('id_usuario'),
					"empresa"=>     $empresa[0]['id_empresa'],
					"codigo" =>     $this->getPostParam('codigo'),
					"descripcion"=>	$this->getPostParam('descripcion'),
					"cantidad"   => $this->getPostParam('cantidad'),
					"tipo"       => $this->getPostParam('tipo'),
					"prioridad"  => $this->getPostParam('prioridad'),
					"plazo"      => $this->getPostParam('plazo')
					);
					
				//print_r($datos);exit();	
				if($this->_pln->insertar($datos))
				{
					$this->redireccionar('compra/planificacion/index/');
					exit();
				}else
				{
					$this->_pln->regLog();
					 $this->_view->error = "Error guardando Planificacion .....";
					 //$this->_view->renderizar('a','transaccion');
					 //exit();   
				}
								
			}
					
			
			$this->_view->setJs(array('planificacion'));
			$this->_view->setJsPlugin(array('jquery-ui','validaciones'));
			$this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));
			
			
			$this->_view->ced_usuario = session::get('cedula');
			$this->_view->nom_usuario = session::get('nombre');
			$this->_view->ape_usuario = session::get('apellido');
			$this->_view->id_usuario  = session::get('id_usuario');
			//cargo unidades operativas
			$this->_view->unidad = $this->_unidad->cargarUnidadUsuario(session::get('id_usuario'));
			//cargo departamentos
			$this->_view->dpto = $this->_dpto->cargarDepartamento();
			//carga concepto
			$this->_view->concepto = $this->_concepto->cargarConcepto();
			
			
			
			$this->_view->title= "Nueva Planificacion de Compra";
			$this->_view->renderizar('agregar','compra','Requisicion de Compra');
			exit();
		}
		
		//------------------------------------------------------------------------------------
		//METODO DE CONTROL Y SEGUIMIENTO DE LA PLANIFICACION
		//------------------------------------------------------------------------------------
		public function control($pagina = 1)
		{
			if($this->getInt('guardar') == 1 )
			{	
			
			}
                        
                        $this->_view->setJsPlugin(array('jquery-ui'));
			
			$this->_view->setJsPlugin(array('Chart1'));
                        $this->_view->setCssPlugin(array('legend'));
			$this->_view->setJs(array('control','grafestpln'));
			
			
			
			
			$this->getLibrary('paginador');
			$paginador = new Paginador();
			
			if($this->getInt('deposito')>0)
			{
				if($this->getPostParam('busqueda'))
				{
					$this->_view->lista = $paginador->paginar($this->_pln->cargarPln($this->getInt('deposito'),$this->getPostParam('busqueda')),$pagina);
				}else
				{
					 $this->_view->lista = $paginador->paginar($this->_pln->cargarPln($this->getInt('deposito')),$pagina);        
				}
				
			}
			else
			{
				$data = $paginador->paginar($this->_pln->cargarControl(),$pagina,5);
			}
				$this->_view->paginacion = $paginador->getView('paginacion','compra/planificacion/control'); 
			
			
			$this->_view->lista = $data;
			//print_r($data);exit();
			
			$this->_view->title= "Planificacion de Compra";
			$this->_view->renderizar('control','compra','Planificacion de Compra');
			exit();
			
		}		
		
		
		
		//----------------------------------------------------------------------------------------------
		//METODO QUE GESTIONA LOS CONCEPTOS DE PLANIFICACION
		//----------------------------------------------------------------------------------------------
		public function concepto($pagina = 1)
		{
			if($this->getInt('guardar')==1)
			{
				$datos = array("descripcion"=>$this->getPostParam('descripcion'),
							   "comentario"=>$this->getPostParam('comentario'),
							   "usuario"=>session::get('id_usuario'));
							   
				if($this->_concepto->insertar($datos))
				{
					$this->redireccionar('compra/planificacion/index/');
                    exit(); 
									
				}else
				{
					$this->_view->error = "Error guardando Concepto .....".$this->_concepto->regLog();                    
				}	
			
			}
			
			if($this->getInt('guardar')==2)
			{
				$datos = array("descripcion"=>$this->getPostParam('descripcion'),
							   "comentario"=>$this->getPostParam('comentario'),
							   "id"=>getInt('id'));
				if($this->_concepto->modificar($datos))
				{
					$this->redireccionar('compra/planificacion/index/');
                    exit(); 
									
				}else
				{
					$this->_view->error = "Error guardando Concepto .....".$this->_concepto->regLog();                    
				}			   
							   
							   
			}
			
			
			$this->_view->setCssPlugin(array('jquery-ui','elementoDinamico'));			
			$this->_view->setJs(array('concepto'));
			
			$this->getLibrary('paginador');
			$paginador = new Paginador();
			
			if($this->getPostParam('busqueda'))
			{
				$this->_view->lista = $paginador->paginar($this->_concepto->cargarConcepto($this->getPostParam('busqueda'),$pagina));
			}else
			{
				 $this->_view->lista = $paginador->paginar($this->_concepto->cargarConcepto(),$pagina);        
			}
			$this->_view->paginacion = $paginador->getView('paginacion','compra/planificacion/concepto'); 	
			
			
			
			$this->_view->title= "Planificacion de Compra";
			$this->_view->renderizar('concepto','compra','Planificacion de Compra');
			exit();
			
			
		}
		
		
		//----------------------------------------------------------------------------------------------
		//METODO QUE COMPRUEBA SI EXISTE UN CONCEPTO
		//----------------------------------------------------------------------------------------------
		public function comprobarConcepto()
		{
			 echo json_encode($this->_concepto->verificarConcepto(strtolower($this->getPostParam('descripcion'))));
			
		}
		//----------------------------------------------------------------------------------------------
		//METODO QUE  BUSCA UN CONCEPTO
		//----------------------------------------------------------------------------------------------
		public function buscarConcepto()
		{
			 echo json_encode($this->_concepto->buscarConcepto($this->getInt('valor')));
			
		}
		//----------------------------------------------------------------------------------------------
		//METODO QUE ELIMINA O DESACTIVA CONCEPTOS
		//----------------------------------------------------------------------------------------------
		public function eliminarConcepto()
		{
			echo json_encode($this->_concepto->desactivar($this->getInt('valor')));    
		}
		
		
		
		
		
		//----------------------------------------------------------------------------------------------
		//METODO QUE CARGA PLANIFICACION
		//----------------------------------------------------------------------------------------------
		public function buscarPlanificacion()
		{
			 echo json_encode($this->_pln->cargarControl($this->getInt('valor')));
			
		}
		
		//==========================================================================
		//METODO QUE PERMITE BUSCAR REQUISITOS DE FORMA ASINCRONA SEGUN SU TIPO(PRODUCTO,SERVICIO)
		//==========================================================================
		public function buscarRequisito()
		{
			
			if($this->getPostParam('tipo')=='PRODUCTO')
			{	
				$this->_producto = $this->loadModel('producto','almacen');
				echo json_encode($this->_producto->cargarProducto($this->getPostParam('valor')));			
			}else
				echo json_encode($this->_servicio->cargarServicio($this->getPostParam('valor')));	
		}
		//--------------------------------------------------------------------------
		public function buscarRequisitoId()
		{
			
			if($this->getPostParam('tipo')=='PRODUCTO')
				echo json_encode($this->_producto->buscarId($this->getPostParam('valor')));
			else
				echo json_encode($this->_servicio->buscarId($this->getInt('valor')));	
		}
		
		//----------------------------------------------------------------------------------------
		//METODO QUE CARGA DETALLE DE PLANIFICACION ()
		//----------------------------------------------------------------------------------------
		public function buscarDetPlanificacion()
		{
			 echo json_encode($this->_pln->cargarDetalle($this->getInt('valor')));
			
		}
		
		
		
		//----------------------------------------------------------------------------------------------
		//METODO QUE CARGA LOS USUARIOS
		//----------------------------------------------------------------------------------------------
		public function cargarUsuario()
		{
			 echo json_encode($this->_concepto->verificarConcepto(strtolower($this->getPostParam('descripcion'))));
			
		}
		
		//----------------------------------------------------------------------------------------------
		//METODO QUE CARGA TRABAJADORES SEGUN DEPARTAMENTO 
		//----------------------------------------------------------------------------------------------
		public function cargarTrabajadores()
		{
			$this->_trabajador = $this->loadModel('trabajador','rrhh');
			echo json_encode($this->_trabajador->trabajadorDpto($this->getInt('valor')));    
		}
		
		
		
		//----------------------------------------------------------------------------------------------
		//METODO QUE CARGA DATOS PARA GENERACION DE GRAFICO DE ESTADO DE PLANIFICACION
		//----------------------------------------------------------------------------------------------
		public function grafCondSol()
		{
			$total =0;
			$valores = array();
			
			$colores = array("POR EVALUAR"=>'#FFA500',"RECHAZADO"=> '#FF0000',"POR COTIZAR"=>'#48D1CC'
			,"CERRADO"=>'#B8860B',"POR APROBAR"=>'#A9A9A9',"APROBADO"=>'#556B2F',
			"POR RECIBIR"=>'#9932CC',"RECIBIDO"=>'#32CD32');
			
			$datos = $this->_pln->contarCondSol($this->getPostParam('condicion'));
			if(count($datos)>1)
			{
				$col = array_rand($colores,count($datos));
				foreach($datos as $val)
				{
					$total = $total + $val['total'];
				}
				for($i = 0 ; $i < count($datos);$i++)
				{
						$porcentaje = round(($datos[$i]['total']*100)/$total,2);
						$valores[]=array("value"=>$porcentaje,"color"=>$colores[$col[$i]],"label"=>$datos[$i]['condicion_solicitud']);
				}
			}    
			echo json_encode($valores);       
		}
	
		
	
	}
?>