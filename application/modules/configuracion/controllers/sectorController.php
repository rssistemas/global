<?php
class sectorController extends configuracionController
{
    private $_sector;
    private $_ultimo_registro;
    private $_parroquia;
	  private $_estado;
    private $_municipio;
    private $_empresa;

    public function __construct()  {
        parent::__construct();
        $this->_ultimo_registro = 0;
        $this->_sector= $this->loadModel('sector');
		    $this->_estado = $this->loadModel('estado');
        $this->_municipio = $this->loadModel('municipio');

        $this->_empresa = session::get('empresa');
    }

    public function index($pagina = 1)
    {
    	$this->_acl->acceso('sector_consultar',105,'configuracion-estado-index');

        $this->_view->title = "Localidad";
        $this->_view->setJs(array('sector'));
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_sector->cargarSector(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_sector->cargarSector(),$pagina);
        }
        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/sector/index');
        //SE CARGAN LOS ESTADOS
        $est = $this->_estado->cargarEstado();
		    //print_r($est);exit();
        $this->_view->estado = $est;

        $this->_view->renderizar('index','configuracion','Localidad');
    }


    public function agregar()
    {



        if(validate::getPostParam('guardar')==1)
        {
        	$this->_acl->acceso('sector_agregar',105,'configuracion-sector-index');
            $datos = array(
            "descripcion"=>validate::getPostParam('descripcion'),
            "municipio"=>validate::getPostParam('municipio'),
            "empresa"=>$this->_empresa
          );
            if($this->_sector->insertar($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('configuracion/sector/index/','configuracion');
                exit();
            }


        }

        if(validate::getPostParam('guardar')==2)
        {
        	$this->_acl->acceso('sector_editar',105,'configuracion-sector-index');
            $datos = array(
            "descripcion"=>validate::getPostParam('descripcion'),
            "municipio"=>validate::getPostParam('municipio'),
            "id"=>validate::getPostParam('id'));

            if($this->_sector->modificar($datos))
            {
                $this->_view->error = "Datos Guardados  .....";
                $this->redireccionar('configuracion/sector/index/','configuracion');
                exit();
            }
        }

		$this->redireccionar('configuracion/sector/index/','configuracion');
        exit();

    }

    public function activar($id)
    {
        if($id)
        {
            if($this->_sector->activar($id))
            {
                $this->redireccionar('configuracion/sector/index');
                exit();
            }else
            {
                error::alerta('1002','configuracion/sector/index');
                exit();
            }

        }

    }
    public function desactivar($id)
    {
        if($id)
        {
            if($this->_sector->desactivar($id))
            {
                $this->redireccionar('configuracion/sector/index');
                exit();
            }else
            {/*LLAMADOS A ESTADOS */

            }
        }
    }
    public function buscar($valor)
    {
        if($valor){
            $pagina =1;
            $this->_view->setJs(array('sector'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();

            $this->_view->datos = $paginador->paginar($this->_sector->buscar($valor),$pagina);
            $this->_view->paginacion = $paginador->getView('paginacion','configuracion/sector/index');
            $this->_view->renderizar('index','configuracion/sector/index');
            exit();
        }else
        {
            $this->redireccionar('configuracion/sector/index/');
            exit();
        }
    }



    public function buscarSector()
    {
         echo json_encode($this->_sector->buscarLocalidad(validate::getPostParam('valor')));
    }

    public function comprobarSector()
    {

        echo json_encode($this->_sector->comprobarSector(validate::getInt('municipio'),strtolower(validate::getPostParam('descripcion'))));

    }


	public function eliminarSector()
    {
        echo json_encode($this->_sector->desactivar(validate::getInt('valor')));
    }

	  /*LLAMADOS A SECTORES CORRESPONDIENTES A UNA PARROQUIA*/
    public function buscarSectores()
    {
        echo json_encode($this->_sector->buscarSectores(validate::getPostParam('valor')));
    }
    /*LLAMADOS A ESTADOS */
    public function buscarEstados()
    {
        echo json_encode($this->_estado->cargarEstado());
    }
    /*LLAMADOS A ESTADOS */
    public function buscarMunicipios()
    {
        echo json_encode($this->_municipio->buscarMunicipios(validate::getInt('valor')));
    }

}


?>
