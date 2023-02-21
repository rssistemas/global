<?php
class presentacionController extends almacenController
{
    private $_presentacion;
    private $_uni_med;
    
    public function __construct()  {
        parent::__construct();
        $this->_presentacion= $this->loadModel('presentacion');
        $this->_uni_med = $this->loadModel('unidadMedida','configuracion');
    }
    
   
    public function index($pagina = 1)
    {
       
        
        //carga el archivo JS del maestro
        $this->_view->setJs(array('presentacion'));
        $this->_view->setJsPlugin(array('validaciones'));

        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();
        
        if($this->getPostParam('busqueda'))
        {
        	$lista = $paginador->paginar($this->_presentacion->cargarPresentacion($this->getPostParam('busqueda')),$pagina);
            $this->_view->lista = $lista;
        }
        else      
        {
        	$lista = $paginador->paginar($this->_presentacion->cargarPresentacion(),$pagina);
            $this->_view->lista = $lista;
        }
        
		if(count($lista)==0)
			$this->_view->info = "Busqueda sin resultados ....";
		
		
        $this->_view->paginacion = $paginador->getView('paginacion','almacen/presentacion/index');
        
        $this->_view->medida = $this->_uni_med->cargarUnidadMedida();
        $this->_view->title = "Clasificaciones";
        $this->_view->renderizar('index','almacen','Clasificaciones');
        exit();
    }

    
    public function agregar()
    {
        $datos = array(
            "id"=>$this->getPostParam('id'),
            "descripcion"=>$this->getPostParam('descripcion'),
            "cantidad"=>$this->getPostParam('cantidad'),
            "unidades"=>$this->getPostParam('unidades'),
            "medida"=>$this->getPostParam('medida'));
			
			
			
        if($this->getPostParam('guardar')==1)
        {
           // print_r($datos);exit();
			if($this->_presentacion->incluir($datos))
            {
                $mensaje="Registro nuevo guardado exitosamente...";
				$this->getMensaje("Confirmacion",$mensaje);
            }
            else
            {
				$this->_presentacion->regLog();
                $mensaje="ERROR al guardar el nuevo registro...";
				$this->getMensaje("error",$mensaje);
            }
        }//FIN DE OPCION 1 para guardar nuevo
        if($this->getPostParam('guardar')==2)
        {   //si hubo edicion recibe true
            if($this->_presentacion->modificar($datos))
            {
                $mensaje = "CONFIRMACIÓN DE REGITRO - Registro editado exitosamente...";
				$this->getMensaje("Confirmacion",$mensaje);
            }
            else
            {
				$this->_presentacion->regLog();
                $mensaje = "Error Editando Presentacion...";
				$this->getMensaje("error",$mensaje);
            }
        }//FIN DE OPCION 2 para guardar edicion
        $this->redireccionar('almacen/presentacion/index/','almacen');
		exit();
    } 

    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function estatusPresentacion()
    {
        echo json_encode($this->_presentacion->estatusPresentacion($this->getInt('valor'),$this->getInt('estatus')));
    }

    public function comprobarUso()
    {
        echo json_encode($this->_presentacion->verificar_uso($this->getPostParam('valor')));
    }
    
    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarPresentacion()
    {
        echo json_encode($this->_presentacion->verificar_existencia($this->getPostParam('valor'),$this->getPostParam('unidad'),$this->getPostParam('cant_uni'),$this->getPostParam('cant_pre')));
    }
    
    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarPresentacion()
    {
         echo json_encode($this->_presentacion->buscar($this->getPostParam('valor')));
    }        

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO