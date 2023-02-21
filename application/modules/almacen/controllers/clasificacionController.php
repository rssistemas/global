<?php
class clasificacionController extends almacenController
{
    private $_clasificacion;
    private $_tipo_clasificacion;
    public function __construct() {
        parent::__construct();
        $this->_clasificacion = $this->loadModel('clasificacion');
	$this->_tipo_clasificacion = $this->loadModel('tipoClasificacion');
    }
    
    
    public function index($pagina = 1)
    {
		        
       
        $this->_view->setJs(array('clasificacion'));
        $this->_view->setJsPlugin(array('validaciones'));
        
        $this->getLibrary('paginador');
        
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
        	$lista = $paginador->paginar($this->_clasificacion->buscar($this->getPostParam('busqueda')),$pagina);
            $this->_view->lista = $lista;
        }
        else
        {
        	$lista = $paginador->paginar($this->_clasificacion->listar(),$pagina);
            $this->_view->lista = $lista;
        }
		
		if(count($lista)==0)
			$this->_view->info = "Busqueda sin resultados ....";
		
		
        $this->_view->paginacion = $paginador->getView('paginacion','almacen/clasificacion/index'); 
	$this->_view->tipo = $this->_tipo_clasificacion->listar();
	$this->_view->title = "Clasificaciones";
		
        $this->_view->renderizar('index','almacen','Clasificaciones');
        exit();
    }

    //llama a la inclucion o edicion del registro segun sea el caso
    public function agregar()
    {
        $datos = array( 
            "id"=>$this->getPostParam('id'),
            "descripcion"=>$this->getSql('descripcion'),
            "tipo"=>$this->getInt('tipo'),
            "comentario"=>$this->getSql('comentario'));
			
        if($this->getPostParam('guardar')==1)
        {
            if($this->_clasificacion->incluir($datos))
            {
                $mensaje="CONFIRMACIÓN DE REGITRO - Registro nuevo guardado exitosamente...";
            }
            else
            {
                $mensaje="ERROR al guardar el nuevo registro...";
		$this->_clasificacion->regLog();
		$this->getMensaje("error",$mensaje);               
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {
            if($this->_rubro->modificar($datos))
            {
                $mensaje = "CONFIRMACIÓN DE REGITRO - Registro editado exitosamente...";
            }
            else //sino hubo edicion recibe false
            {
                $mensaje = array("error"=>"Error guardando edicion..." . $this->_rubro->regLog());
            }
        }//FIN DE OPCION 2 para guardar edicion
		
        $this->redireccionar('almacen/clasificacion/');
        exit();
    }
	
	
	
	
    /* llama a la desactivacion del objeto a traves del id devolviendo un valor por json */
    public function estatusRubro()
    {
        echo json_encode($this->_rubro->estatusRubro($this->getInt('valor'),$this->getInt('estatus')));
    }

    //realiza una comprobacion del registro que se incluira o editara
    //  devolviendo si el registro existe 
    public function comprobarRubro()
    {
        echo json_encode($this->_rubro->verificar_existencia($this->getPostParam('valor')));
    }

    /* Realiza una comprobacion de uso del componente en otro*/
    public function comprobarUso()
    {
        echo json_encode($this->_rubro->verificar_uso($this->getPostParam('valor')));
    }
    
    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarClasificacion()
    {
         echo json_encode($this->_clasificacion->buscar($this->getInt('valor')));
    }
	
	public function buscarTipos()
    {
         echo json_encode($this->_tipo_clasificacion->listar());
    }
	
	
    
}  