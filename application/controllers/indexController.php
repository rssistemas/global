<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 16/07/2019
 * @time 01:25:29 AM
 */

class indexController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->getHelper("validate");
        $this->getHelper("logger");
    }

    public function index()
    {
        $this->_view->titulo = "Prueba";
        $this->_view->renderizar("index");
        exit();
    }


    public function login()
    {
        if(validate::getInt('entrar')==1)
        {
            $this->getLibrary('security');
            $security = new security();
            if($security->conection(validate::getPostParam('usuario'),validate::getPostParam('clave')))
            {
                $usuario = $security->getUser();
                
                //print_r($usuario);
                //exit();

              if(count($usuario))
              {
                            if($usuario[0]['condicion_usuario']=='DESCONECTADO' || $usuario[0]['condicion_usuario']=='CONECTADO' )
                                 {
                                         session::set('autenticado',true);
                                         session::set('id_usuario',$usuario[0]['id']);
                                         session::set('alias',$usuario[0]['alias_usuario']);

                                         session::set('role_id',$usuario[0]['role_id']);
                                         session::set('correo', $usuario[0]['correo_usuario']);
                                         session::set('estatus',$usuario[0]['estatus_usuario']);
                                         session::set('empresa',$usuario[0]['empresa_id']);
                                         session::set('movil_usuario',$usuario[0]['celular_usuario']);
                                         session::set('es_colaborador',$usuario[0]['es_colaborador']);
                                         session::set('tipo_colaborador',$usuario[0]['tipo_colaborador']);

                                         session::set('tiempo',time());

                                         $security->loadPersonUser($usuario[0]['persona_id']);
                                         $person = $security->getPerson();


                                         if(count($person))
                                         {
                                                 session::set('nombre',$person[0]['pri_nombre_persona']);
                                                 session::set('apellido', $person[0]['pri_apellido_persona']);
                                                 session::set('persona_id',$person[0]['persona_id']);
                                                 session::set('cedula',$person[0]['cedula_persona']);
                                                 session::set('nac',$person[0]['nacionalidad_persona']);
                                                 session::set('sexo',$person[0]['sexo_persona']);
                                         }
                                         $mensaje = "Acceso de usuario:".$usuario[0]['alias_usuario']." al sistema,";
                                         $security->securityLog($mensaje);
                                         $security->inUser($usuario[0]['id']);

                                 }else
                                         {

                                                 if($usuario[0]['ip_ult_ent']==$security->getRealIP())
                                                 {
                                                         session::acceso();

                                                 }

                                                 ///se  bloquea el usuario
                                                 if($usuario[0]['ip_ult_ent']!=$security->getRealIP())
                                                 {
                                                         $mensaje = "Intento de acceso denegado con usuario:".$usuario[0]['alias_usuario']." al sistema, Usuario en session ";
                                                         $security->blockUser($usuario[0]['id']);
                                                         $security->securityLog($mensaje);

                                                         $this->_view->titulo = "Bienvenido a GlobalAdm";
                                                         $this->_view->setTemplate('barra');
                                                         $this->_view->renderizar("ingresar");
                                                         exit();

                                                 }



                                         }
                        }
                        $this->redireccionar("intranet");
                        exit();
                    }

        }
        //die("llegue");
        $this->_view->titulo = "Bienvenido a ServiPymes";
        $this->_view->setTemplate('barra');
        $this->_view->renderizar("ingresar");
        exit();
    }

    public function logup($usuario = false)
    {
//                $usuario = base64_decode($usuario);
//                $this->getLibrary('security');
//                $security = new security();
//
//                        if(session::get('id_usuario')==$usuario)
//                        {
//                                if(session::get('autenticado'))
//                                {
//                                        $mensaje = "Egreso de usuario:".$usuario[0]['id_usuario']." al sistema,";
//                                        $security->securityLog($mensaje);
//                                        $security->outUser($usuario[0]['id_usuario']);
//
//                                        session::destroy();
//                                }else
//                                        {
//                                                $mensaje = "ALERTA: Egreso de usuario no autenticado:".$usuario[0]['id_usuario']." al sistema,";
//                                                $security->securityLog($mensaje);
//                                                session::destroy();
//                                        }
//                        }else
//                                {
//                                        $mensaje = "ALERTA: Falla de seguridad session duplicada:".$usuario[0]['id_usuario']." al sistema,";
//                                        $security->securityLog($mensaje);
//                                        session::destroy();
//                                }

	 session::destroy();	
        $this->redireccionar("index/login");
        //exit();
	}

}
