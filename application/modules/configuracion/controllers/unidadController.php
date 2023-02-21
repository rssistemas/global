<?php
class unidadController extends configuracionController
{
    private $_unidad;
    private $_empresa;
	  private $_estado;
    private $_municipio;
    private $_sector;

    public function __construct() {
        parent::__construct();
        $this->_unidad = $this->loadModel('unidad');
        $this->_empresa = $this->loadModel('empresa');
		    $this->_estado = $this->loadModel('estado');
        $this->_municipio = $this->loadModel('municipio');
        $this->_sector = $this->loadModel('sector');
    }

    public function index($pagina = 1)
    {
    	  //$this->_acl->acceso('unidadOperativa_consultar',105,'configuracion-estado-index');
        //define el titulo de la presente vista
        $this->_view->title = "Unidad Operativa";
        //carga el archivo JS del maestro
        $this->_view->setJs(array('unidad'));

        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();

        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_unidad->cargarUnidad(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_unidad->cargarUnidad(),$pagina);
        }
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/unidad/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','configuracion','Unidad Operativa');
        exit();
    }

    // ----- METODO PARA MOSTRAR LA VISTA DE agregar
    public function agregar()
    {
    	   //$this->_acl->acceso('unidadOperativa_agregar',105,'configuracion-unidad-index');
        if(validate::getPostParam('guardar')==1)
        {
            $datos = array(
                "empresa"=>validate::getInt('empresa'),
                "nombre"=>  validate::getPostParam('nombre'),
                "direccion"=>  validate::getPostParam('direccion'),
                "estado"   => validate::getInt('estado'),
                "municipio"=> validate::getInt('municipio'),
				        "sector"=>  validate::getPostParam('sector'),
                "telefono"=>  validate::getPostParam('telefono'),
                "condicion"=>  validate::getPostParam('condicion'),
				        "comentario"=> validate::getPostParam('comentario'),
				        "serie" => validate::getPostParam('serie'),
				        "fecha"=> validate::getPostParam('fecha'));

			//print_r($datos);
			//exit();
            if($this->_unidad->incluir($datos))
            {
                //$this->_view->error = "Registro nuevo guardado...";
//              $this->redireccionar('archivo/deposito/index');
                $this->redireccionar('configuracion/unidad/index/','archivo');
                exit();
            }
            else
            {
				        //$this->_unidad->regLog();
                $this->_view->error = "Error guardando registro nuevo .....";

            }
        }

    		$this->_view->title = "Nueva unidad operativa";
    		$this->_view->setJs(array('unidad'));
    		$this->_view->empresa = $this->_empresa->cargarEmpresa();
    		$this->_view->estado = $this->_estado->cargarEstado();

    		$this->_view->renderizar('agregar','configuracion','Unidad Operativa');
    		exit();

    }

    // ----- METODO PARA MOSTRAR LA VISTA DE EDICION Y EJECUTAR LOS CAMBIOS
    public function editar($id = FALSE)
    {
    	  $this->_acl->acceso('unidadOperativa_editar',105,'configuracion-unidad-index');
        if(validate::getPostParam('guardar')=='2')
        {
             $datos = array(
                "empresa"  =>validate::getInt('empresa'),
                "nombre"   =>validate::getPostParam('nombre'),
                "direccion"=>validate::getPostParam('direccion'),
                "estado"   => validate::getInt('estado'),
                "municipio"=> validate::getInt('municipio'),
				        "sector"   =>validate::getPostParam('sector'),
                "telefono" =>validate::getPostParam('telefono'),
                "condicion"=>validate::getPostParam('condicion'),
				        "comentario"=>validate::getPostParam('comentario'),
				        "serie"     =>validate::getPostParam('serie'),
				        "fecha"     =>validate::getPostParam('fecha'),
                "id" =>validate::getInt('id')
              );

            if($this->_unidad->modificar($datos))
            {
                //$this->_view->error = "Registro nuevo guardado...";
                //$this->redireccionar('archivo/deposito/index');
                $this->redireccionar('configuracion/unidad/index/','archivo');
                exit();
            }
            else
            {
				        //$this->_unidad->regLog();
                $this->_view->error = "Error guardando registro nuevo .....";

            }
        }

    		$this->_view->title = "Editar unidad operativa";
    		$this->_view->setJs(array('unidad'));
        if($id)
        {
            $uni = $this->_unidad->buscar($id);
            //print_r($uni);
            $this->_view->data = $uni;
        }
        $this->_view->empresa = $this->_empresa->cargarEmpresa();
    		$this->_view->estado = $this->_estado->cargarEstado();
        $this->_view->muni = $this->_municipio->cargarMunicipio();
        $this->_view->sec = $this->_sector->cargarSector();

    		$this->_view->renderizar('editar','configuracion','Unidad Operativa');
    		exit();

    }


	public function depositoUnidad($unidad)
	{

		if(validate::getInt('guardar')==1)
        {
            //print_r($_POST);
              //          exit();

            $unidad = validate::getInt('unidad');
            $deposito = validate::getInt('deposito');
            $datos = array("deposito"=>$deposito,"unidad"=>$unidad);

            if($this->_unidad->incluirRelacionUnidad($datos))
            {
               // $this->redireccionar('archivo/unidad/depositoUnidad/','archivo');
               // exit();
            }else
            {
                $this->_unidad->regLog();
                $this->_view->error = "Error guardando registro nuevo .....";
            }
            //$this->redireccionar('archivo/deposito/usuarioDeposito/'.$trabajador);
            //exit();
        }

        $this->_view->setJs(array('relUni'));
        $this->_view->noDeposito = $this->_unidad->noDepositoUnidad($unidad);

		$this->_view->lista = $this->_unidad->cargarDepositoUnidad($unidad);

        $this->_view->unidad = $unidad;
        $this->_view->title = "Depositos Asignados";

        $this->_view->renderizar('deposito','archivo');
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

    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function eliminarDeposito()
    {
        echo json_encode($this->_deposito->desactivar($this->getInt('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarDeposito()
    {
        echo json_encode($this->_deposito->verificar_existencia(validate::getPostParam('tipo'),validate::getPostParam('nombre')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarDeposito()
    {
        echo json_encode($this->_deposito->buscar(validate::getInt('valor')));
    }

    //==========================================================================
    //METODO QUE CARGA LOS DEPOSITOS QUE SON DIFERENTES AL VALOR PASADO EN PARAMETRO
    //==========================================================================
    public function depositoDestino()
    {
        echo json_encode($this->_deposito->buscarDiferente(validate::getInt('valor')));

    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
