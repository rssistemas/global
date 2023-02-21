<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 26/02/2019
 * @time 11:31:39 PM
 */

class organizacionController extends configuracionController
{
    private $_organizacion;
    private $_empresa;
    private $_estado;
    
    public function __construct() {
        parent::__construct();
        $this->_organizacion = $this->loadModel('organizacion');
        $this->_estado = $this->loadModel("estado");
    }
    
    public function index($pagina =1) {
        
        $this->_view->title = "Organizacion";
        //$this->_view->setJs(array('modulo'));
        $this->_view->setJsPlugin(array('validaciones','jquery-ui'));
		$this->_view->setCssPlugin(array('jquery-ui'));
        
        $this->getLibrary('paginador');
        $paginador = new Paginador();
        if(validate::getPostParam('busqueda'))
        {
            $this->_view->lista = $paginador->paginar($this->_organizacion->listar(validate::getPostParam('busqueda')),$pagina);
        }
        else
        {
            $this->_view->lista =  $paginador->paginar($this->_organizacion->listar(),$pagina);
        } 
        /* Ejecutara la vista a traves del metodo paginacion
         *  direccionando la vista index  */
        $this->_view->paginacion = $paginador->getView('paginacion','archivo/organizacion/index');
        /* Ubicara el menu especificado para ser visible desde el maestro  */
        $this->_view->renderizar('index','archivo');
        exit();
        
        
        
    }
    
    public function agregar()
    {
        if(validate::getInt('guardar')==1)
        {
            //print_r($_POST);
            //exit();
            
            $datos = array(
                ":empresa_id"=>             "'".validate::getInt('emp')."'",
                ":nombre_organizacion"=>    "'".validate::getPostParam('nombre')."'",
                ":descripcion_organizacion"=>"'".validate::getPostParam('descripcion')."'",
                ":tipo_organizacion"=>       "'".validate::getPostParam('tipo')."'",
                ":condicion_organizacion"=>  "'".validate::getPostParam('condicion')."'",
                ":direccion_organizacion"=>  "'".validate::getPostParam('direccion')."'",
                ":telefono_organizacion"=>   "'".validate::getPostParam('local')."'",
                ":correo_organizacion"  =>   "'".validate::getPostParam('correo')."'",
                ":estado_ubi_organizacion"=> "'".validate::getInt('estado')."'",
                ":municipio_ubi_organizacion"=>"'".validate::getInt('municipio')."'",
                ":parroquia_ubi_organizacion"=> "'".validate::getInt('parroquia')."'",
                ":sector_ubi_organizacion"=>  "'".validate::getInt('sector')."'",     
                ":fecha_creado" =>           "'".date('Ymd')."'",
                ":estatus_organizacion"=>    '1',
                ":usuario_creador" =>       "'".session::get('id_usuario')."'"
                
            );
            //$field = $this->_organizacion->getFields();
            //$datos = $this->getParam_forModel($this->_organizacion);
            //print_r($field);
            //print_r($datos);
            
            //exit();
                
            if($this->_organizacion->sInsert($datos))
            {
                 $this->redireccionar('configuracion/organizacion/index/');
                 exit();
            }else
            {
                logger::errorLog("Error registrando organizacion...",'ERROR');
                $this->_view->error = "Error registrando organizacion... Revizar LOG";
            }
            
            
        }
        $this->_empresa = $this->loadModel('empresa');
        $this->_view->emp = $this->_empresa->cargarEmpresa();
        $this->_view->esta = $this->_estado->cargarEstado();
     
        
        $this->_view->setJs(array('organizacion'));
        $this->_view->title = "Agregar Organizacion";        
        $this->_view->renderizar('agregar','archivo');
        exit();
    }
    
    
    
}
