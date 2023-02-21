<?php
class municipioController extends configuracionController
{
    private $_municipio;
    private $_ultimo_registro;
    private $_estado;
    public function __construct()  {
        parent::__construct();
        $this->_ultimo_registro = 0;
        $this->_municipio= $this->loadModel('municipio','configuracion');
        $this->_estado = $this->loadModel('estado','configuracion');
    }
    
    public function index($pagina = 1)
    {
    	
		$this->_acl->acceso('municipio_consultar',105,'configuracion-estado-index');
		
        $this->_view->title = "Localidad";
        $this->_view->setJs(array('municipio'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_municipio->cargarMunicipio(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_municipio->cargarMunicipio(),$pagina);
        }
        
        

        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/municipio/index');
        $this->_view->medida = $this->_estado->cargarEstado();
        $this->_view->renderizar('index','configuracion','Localidad');
    }
    
         
    public function agregar()
    {
        if(validate::getPostParam('guardar')==1)
        {
        	$this->_acl->acceso('municipio_agregar',105,'configuracion-municipio-index');
            $datos = array(
            "descripcion"=>validate::getPostParam('descripcion'),
            "estado"=>validate::getPostParam('estado'));
            if($this->_municipio->incluir($datos))
            {
                $this->redireccionar('configuracion/municipio/index/','configuracion');
                exit();
            }
        }    
        if(validate::getPostParam('guardar')==2)
        {
        	$this->_acl->acceso('municipio_editar',105,'configuracion-municipio-index');
            $datos = array(
            "descripcion"=>validate::getPostParam('descripcion'),
            "id"=>validate::getPostParam('id'));
            
            if($this->_municipio->modificar($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('configuracion/municipio/index/','configuracion');
                exit();
            }
        }
		
		$this->redireccionar('configuracion/municipio/index/','configuracion');
		exit();
    } 

    public function activar($id)
    {
        if($id)
        {
            if($this->_municipio->activar($id))
            {
                $this->redireccionar('configuracion/municipio/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/municipio/index');
                exit();
            }
            
        }
            
    }        
    public function desactivar($id)
    {
        if($id)
        {
            if($this->_municipio->desactivar($id))
            {
                $this->redireccionar('configuracion/municipio/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/municipio/index');
                exit();
            }
            
        }
            
    }
    public function buscar($valor)
    {
        if($valor){
            $pagina =1;
            $this->_view->setJs(array('municipio'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();

            $this->_view->datos = $paginador->paginar($this->_municipio->buscar($valor),$pagina);
            $this->_view->paginacion = $paginador->getView('paginacion','configuracion/municipio/index');	
            $this->_view->renderizar('index','configuracion/municipio/index');
            exit();
        }else
        {
            $this->redireccionar('configuracion/municipio/index/');
            exit();
        }
    }
	
	 /*LLAMADOS A MUNICIPIOS CORRESPONDIENTES A UN ESTADO
        Desglosa los municipios que corresponden a un ESTADO */
    public function buscarMunicipios()
    {
        echo json_encode($this->_municipio->buscarMunicipios(validate::getPostParam('valor')));
    }
    
    public function buscarMunicipio()
    {
         echo json_encode($this->_municipio->buscar(validate::getPostParam('valor')));
    }        
    public function comprobarMunicipio()
    {
     
        echo json_encode($this->_municipio->comprobarMunicipio(validate::getInt('estado'),strtolower(validate::getPostParam('descripcion'))));
    
    }
     public function eliminarMunicipio()
    {
        echo json_encode($this->_municipio->desactivar(validate::getInt('valor')));
    }
}


?>
