<?php
class tipoGastoController extends archivoController
{
    private $_tgasto;
   	
    public function __construct() {
        parent::__construct();
        $this->_tgasto = $this->loadModel('tipoGasto');
        
		
    }
   
    public function index($pagina = 1)
    {
    	$this->_acl->acceso('tgasto_consultar',105,'configuracion-estado-index');
		
        //define el titulo de la presente vista
        $this->_view->title = "Tipo de Gastos";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('tgasto'));
        
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_tgasto->cargarTgasto(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_tgasto->cargarTgasto(),$pagina);
        }
        
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/tipogasto/index');
        
        $this->_view->renderizar('index','configuracion','Tipo de Gasto');
        exit();
    }
    
    // ----- METODO PARA MOSTRAR LA VISTA DE agregar
    public function agregar()
    {
        if(validate::getInt('guardar')==1)
        {
            $this->_acl->acceso('tgasto_agregar',105,'configuracion-tipogasto-index');
            $datos = array(
                "nombre"=>  validate::getPostParam('nombre'),
                "comentario"=>  validate::getPostParam('comentario'));
				
            if($this->_tgasto->incluir($datos))
            {
                $this->redireccionar('archivo/tipogasto/index/','configuracion');
                exit();
            }
            else
            {
		$this->redireccionar('archivo/tipogasto/index/','configuracion');
                exit();
                //$this->_view->error = "Error guardando registro nuevo .....".$this->_tgasto->regLog();
                //$this->_view->renderizar('index','archivo');
                //exit();
            }
        }
	if($this->getInt('guardar')==2)
        {
            //$this->_acl->acceso('tgasto_editar',105,'configuracion-tipogasto-index');
            $datos = array(
                "nombre"=>  validate::getPostParam('nombre'),
                "comentario"=>  validate::getPostParam('comentario'),
				"id"=> validate::getPostParam('id'));
				
            if($this->_tgasto->modificar($datos))
            {
                $this->redireccionar('archivo/tipoGasto/index/','configuracion');
                exit();
            }
            else
            {
		$this->redireccionar('archivo/tipoGasto/index/','configuracion');
                exit();
                //$this->_view->error = "Error guardando registro nuevo .....".$this->_tgasto->regLog();
                //$this->_view->renderizar('index','archivo');
                //exit();
            }
        }
		
        
    }
    
    // ----- METODO PARA MOSTRAR LA VISTA DE EDICION Y EJECUTAR LOS CAMBIOS
    public function editar($id = FALSE)
    {
        if(validate::getPostParam('guardar')=='ed')
        {
             $datos = array(
                "empresa"=>$this->getInt('empresa'),
                "nombre"=>  $this->getPostParam('nombre'),
                "direccion"=>  $this->getPostParam('direccion'),
				"sector"=>  $this->getPostParam('sector'),
                "telefono"=>  $this->getPostParam('telefono'),
                "condicion"=>  $this->getPostParam('condicion'),
				"comentario"=> $this->getPostParam('comentario'),
				"serie" => $this->getPostParam('serie'),
				"fecha"=> $this->getPostParam('fecha'));
				
			//print_r($datos);
			//exit();	
            if($this->_unidad->incluir($datos))
            {
                //$this->_view->error = "Registro nuevo guardado...";
//              $this->redireccionar('archivo/deposito/index');
                $this->redireccionar('archivo/unidad/index/','archivo');
                exit();
            }
            else
            {
				$this->_unidad->regLog();
                $this->_view->error = "Error guardando registro nuevo .....";
                
            }
        }
       
		$this->_view->title = "Nueva unidad operativa";
		$this->_view->setJs(array('unidad'));
		$this->_view->empresa = $this->_empresa->cargarEmpresa();
		$this->_view->estado = $this->_estado->cargarEstado();
	
		$this->_view->renderizar('agregar','archivo');
		exit();
       
    }
	
	
	
    
	//===========================================================================
    //METODO QUE ACTIVA RELACION DE TRABAJADOR - DEPOSITO
    //===========================================================================
    public function activarDepositoUnidad($relacion,$unidad)
    {
        if($relacion)
        {
            if($this->_unidad->activarRelacion($relacion))
            {
                $this->getMensaje('confirmacion',"Relacion Deposito Desactivada"); 
            }else
            {
                $this->getMensaje('error',"Error Desactivando Relacion Deposito");
				
            }
            $this->redireccionar('archivo/unidad/depositoUnidad/'.$unidad);
        }    
        
        
    }
    //===========================================================================
    //METODO QUE DESACTIVA RELACION DE TRABAJADOR - DEPOSITO
    //===========================================================================
    public function desactivarDepositoUnidad($relacion,$unidad)
    {
        if($relacion)
        {
            if($this->_unidad->desactivarRelacion($relacion))
            {
                $this->getMensaje('confirmacion',"Relacion Deposito Desactivada");
            }else
            {
                $this->getMensaje('error',"Error Desactivando Relacion Deposito");
            }
            $this->redireccionar('archivo/unidad/depositoUnidad/'.$unidad);
        }            
    }

    
    public function comprobarTgasto()
    {
        echo json_encode($this->_tgasto->verificar_existencia(validate::getPostParam('valor')));
    }

    
    public function buscarTgasto()
    {
        echo json_encode($this->_tgasto->buscar(validate::getInt('valor')));
    }
    
    //==========================================================================
    //METODO QUE CARGA LOS DEPOSITOS QUE SON DIFERENTES AL VALOR PASADO EN PARAMETRO
    //==========================================================================
    public function depositoDestino()
    {
        echo json_encode($this->_deposito->buscarDiferente(validate::getInt('valor')));
      
    }    

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
