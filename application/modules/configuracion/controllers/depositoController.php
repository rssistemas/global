<?php
class depositoController extends configuracionController
{
    private $_deposito;
    private $_tipoDeposito;
    private $_unidad;
    private $_estado;
    private $_municipio;
    private $_parroquia;
    private $_sector;
    private $_medida;

    private $_empresa;

    public function __construct() {
        parent::__construct();
        $this->_deposito = $this->loadModel('deposito');
        $this->_tipoDeposito = $this->loadModel('tipoDeposito');
		    $this->_unidad = $this->loadModel('unidad');
        $this->_sector = $this->loadModel('sector');
        $this->_municipio = $this->loadModel('municipio');
        $this->_estado = $this->loadModel('estado');

        $this->_medida = $this->loadModel('unidadMedida','configuracion');

        $this->_empresa = session::get('empresa');
    }

    /* Cuando se realiza el llamado al maestro principal
    * http://localhost/pdval/archivo/nombreDeLaVista/index/
    * se ejecuta la funcion index() del controlador del maestro */
    public function index($pagina = 1)
    {
        //carga el archivo JS del maestro
        $this->_view->setJs(array('deposito'));
        $this->_view->setJsPlugin(array('validaciones'));
        //llama a la libreria paginador del framework para
        // ejectutar metodos de paginacion
        $this->getLibrary('paginador');
        //crea el objeto paginador
        $paginador = new Paginador();

        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_deposito->cargarDeposito($this->_empresa,validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista = $paginador->paginar($this->_deposito->cargarDeposito($this->_empresa),$pagina);
        }
        $this->_view->unidad = $this->_unidad->cargarUnidadUsuario(session::get('id_usuario'));

        $this->_view->paginacion = $paginador->getView('paginacion','configuracion/deposito/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        //$this->_view->esta = $this->_estado->cargarEstado();
        $this->_view->title = "Depósitos ";
		    $this->_view->renderizar('index','configuracion','Deposito');
        exit();
    }

    // ----- METODO PARA MOSTRAR LA VISTA DE agregar
    public function agregar()
    {
        if(validate::getInt('guardar')==1)
        {
            $datos = array(
            "tipo"=>    validate::getInt('tipo'),
            "nombre"=>  validate::getPostParam('nombre'),
            "ubicacion"=>  validate::getPostParam('ubicacion'),
            "medida"=>     validate::getPostParam('medida'),
            "telefono"=>   validate::getPostParam('telefono'),
            "fax"=>        validate::getPostParam('fax'),
            "sector"=>     validate::getPostParam('sector'),
            "unidad"=>     validate::getPostParam('unidad'),
            "max"=>        validate::getPostParam('unidad'),
            "min"=>        validate::getPostParam('min'),
            "estado"=>     validate::getInt('estado'),
            "municipio"=>  validate::getInt('municipio'),
            "parroquia"=>  validate::getInt('parroquia')
            );

            if($this->_deposito->incluir($datos))
            {
                $mensaje="CONFIRMACIÓN DE REGITRO - Registro nuevo guardado exitosamente...";
            }
            else
            {
                $this->_deposito->regLog();
                $this->_view->error = "Erroor guardando...";
                $mensaje="CONFIRMACIÓN DE REGISTRO - ERROR al guardar el nuevo registro...";
            }
            $this->redireccionar('almacen/deposito/','almacen');
        }

        $this->_view->title = "Agregar Depósito";
        $this->_view->setJs(array('deposito'));
        $this->_view->setJsPlugin(array('validaciones'));
        $this->_view->tipo = $this->_tipoDeposito->cargarTipoDeposito();
        $this->_view->esta = $this->_estado->cargarEstado();
        //CARGA UNIDADES OPERATIVAS
        $this->_view->unidad = $this->_unidad->cargarUnidad();

        $this->_view->medida = $this->_medida->cargarUnidadMedida();

        $this->_view->renderizar('agregar','deposito','Deposito');
        exit();

    }

    // ----- METODO PARA MOSTRAR LA VISTA DE EDICION Y EJECUTAR LOS CAMBIOS
    public function editar($id = FALSE)
    {
        if(validate::getPostParam('guardar')==2)
        {
            $datos = array(
            "tipo"=>    validate::getInt('tipo'),
            "nombre"=>  validate::getPostParam('nombre'),
            "ubicacion"=>  validate::getPostParam('ubicacion'),
            "medida"=>     validate::getPostParam('medida'),
            "telefono"=>   validate::getPostParam('telefono'),
            "fax"=>        validate::getPostParam('fax'),
            "sector"=>     validate::getPostParam('sector'),
            "unidad"=>     validate::getPostParam('unidad'),
            "max"=>        validate::getPostParam('unidad'),
            "min"=>        validate::getPostParam('min'),
            "estado"=>     validate::getInt('estado'),
            "municipio"=>  validate::getInt('municipio'),
            "parroquia"=>  validate::getInt('parroquia'),
             "id"=> validate::getInt('id')
            );


            if($this->_deposito->modificar($datos))
            {
                $this->_view->error = "Registro editado guardado...";
            }
            else
            {
                $this->_view->error = "Error guardando edicion...".$this->_deposito->regLog();
            }
            $this->redireccionar('almacen/deposito/','configuracion');
        }
        else
        {
            $this->_view->title = "Editar Depósito";
            $this->_view->setJs(array('deposito'));
            $this->_view->setJsPlugin(array('validaciones'));
            $this->getLibrary('paginador');
            $paginador = new Paginador();
            $deposito = $this->_deposito->buscar($id);

            $this->_view->deposito = $deposito;

            $this->_view->tipo = $this->_tipoDeposito->cargarTipoDeposito();

            $this->_view->esta = $this->_estado->cargarEstado();

            $this->_view->muni = $this->_municipio->cargarMunicipio();

            $this->_view->parr = $this->_parroquia->cargarParroquia();

            $this->_view->sec = $this->_sector->cargarSector();

            $this->_view->unidad = $this->_unidad->cargarUnidad();

            $this->_view->medida = $this->_medida->cargarUnidadMedida();

            $this->_view->paginacion = $paginador->getView('paginacion','archivo/deposito/editar');
            $this->_view->renderizar('editar','archivo');
        }
    }

    ///METODO QUE CARGA LOS USUARIOS ASIGNADOS A UN DEPOSITO
    public function trabajadorDeposito($deposito)
    {

        if($this->getInt('guardar')==1)
        {
        //print_r($_POST);
          //          exit();

            $deposito = $this->getPostParam('deposito');
            $trabajador = $this->getInt('trabajador');
            $datos = array("trabajador"=>$trabajador,"deposito"=>$deposito);

            if($this->_deposito->incluirRelacionDeposito($datos))
            {
                $this->getMensaje('confirmacion', 'Trabajador Agregado ');
            }else
            {
                $this->_deposito->regLog();
                $this->getMensaje('error', 'Error agregando Deposito ');
            }
        //$this->redireccionar('archivo/deposito/usuarioDeposito/'.$trabajador);
        //exit();
        }

        $this->_view->setJs(array('relDep'));
        $this->_view->noDeposito = $this->_deposito->noUsuarioDeposito($deposito);

        $this->_view->lista = $this->_deposito->relacionDeposito($deposito);

        $this->_view->deposito = $deposito;
        $this->_view->title = "Usuarios Asignados  ";
        $this->_view->renderizar('trabajador');
        exit();
    }

    //===========================================================================
    //METODO QUE ACTIVA RELACION DE TRABAJADOR - DEPOSITO
    //===========================================================================
    public function activarDepositoUsuario($relacion,$deposito)
    {
        if($relacion)
        {
            if($this->_deposito->activarRelacionDeposito($relacion))
            {
                $this->getMensaje('confirmacion',"Relacion Deposito Activada");
            }else
            {
                $this->getMensaje('error',"Error Activando Relacion Deposito");
            }
            $this->redireccionar('archivo/deposito/usuarioDeposito/'.$deposito);
        }
    }
    //===========================================================================
    //METODO QUE DESACTIVA RELACION DE TRABAJADOR - DEPOSITO
    //===========================================================================
    public function desactivarDepositoUsuario($relacion,$deposito)
    {
        if($relacion)
        {
            if($this->_deposito->desactivarRelacionDeposito($relacion))
            {
                $this->getMensaje('confirmacion',"Relacion Deposito Desactivada");
            }else
            {
                $this->getMensaje('error',"Error Desactivando Relacion Deposito");
            }
            $this->redireccionar('archivo/deposito/usuarioDeposito/'.$deposito);
        }
    }
    /* llama a la desactivacion del objeto a traves del id
     devolviendo un valor por json */
    public function estatusDeposito()
    {
        echo json_encode($this->_deposito->estatusDeposito($this->getInt('valor'),$this->getInt('estatus')));
    }

    public function comprobarUso()
    {
        echo json_encode($this->_deposito->verificar_uso($this->getPostParam('valor')));
    }

    /*realiza una comprobacion del registro que se incluira o editara
     devolviendo si el registro se repite o no */
    public function comprobarDeposito()
    {
        echo json_encode($this->_deposito->verificar_existencia($this->getPostParam('tipo'),$this->getPostParam('nombre')));
    }

    /* realiza busqueda del objeto a traves del id
     por peticion desde el boton editar
     con funcion asincrona retornando json */
    public function buscarDeposito()
    {
        echo json_encode($this->_deposito->buscar($this->getInt('valor')));
    }
    public function buscarDepositoId()
    {
        echo json_encode($this->_deposito->buscarDepId($this->getInt('valor')));
    }
    //==========================================================================
    //METODO QUE CARGA LOS DEPOSITOS QUE SON DIFERENTES AL VALOR PASADO EN PARAMETRO
    //==========================================================================
    public function depositoDestino()
    {
        echo json_encode($this->_deposito->buscarDiferente($this->getInt('valor')));

    }

	public function relacionDeposito()
    {
        echo json_encode($this->_deposito->relacionDepositoAct($this->getInt('valor')));
    }

	public function depositoCDR()
    {
        echo json_encode($this->_deposito->cargarCDR());
    }

}  //FIN DE LA CLASE CONTROLADOR DEL OBJETO
