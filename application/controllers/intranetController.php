<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 25/07/2019
 * @time 12:32:41 AM
 */

class intranetController extends controller
{

    private $_empresa;
    public function __construct() {
        parent::__construct();
        $this->_empresa = $this->loadModel('empresa','configuracion');

    }

    public function index($id = false) {


        $emp = $this->_empresa->cargarEmpresaUsuario(session::get('id_usuario'));
        //print_r($emp);exit();
        if(count($emp)>0)
        {
			if($id > 0)
			{
				$this->_empresa->inactivarEmpresaUsuario(session::get('id'));
				for($i=0;$i < count($emp);$i++)
				{
					$emp[$i]['condicion_empresa']=0;
					if($emp[$i]['id']==$id)
					{
						$this->_empresa->activarEmpresaUsuario(session::get('id'),$id);
						$emp[$i]['condicion_empresa']=1;
					}
				}
			}

            session::set('actEmp',$emp);
        }else
                session::set('actEmp',array());

        session::set('empresa',$emp[0]['id']);

        $this->_view->titulo = "Bienvenido Usuario,".session::get('nombre').' '.session::get('apellido');
        $this->_view->renderizar("index");
    }


}
