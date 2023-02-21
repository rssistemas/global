<?php

class Menu
{
	private $_registry;
	private $_modulos;
	private $_recursos;
	private $_db;
	private $_menu;
	private $_submenu;
	
	public function __construct()
	{
		$this->_menu=array();
		$this->_submenu=array();
		
		$this->registry = registry::getInstancia();
		$this->_db = $this->registry->_db;	
	}
	
	private function getModulos()
	{
		$sql="select * from modulo where estatus_modulo='1' order by posicion_modulo";
		//die($sql);
		$datos=$this->_db->query($sql);
		
		$this->_modulos = $datos->fetchall(); 
	}
	
	private function getRecurso($modulo)
	{
		$sql="select * from recurso where estatus_recurso = 1 and modulo_id = '$modulo' order by posicion_recurso";
		//die($sql);
		$datos=$this->_db->query($sql);
		$this->_recursos = $datos->fetchAll();
		
	}
	private function getRecursoUsuario($modulo)
	{
            $empresa=0;$emp = array();
            $usuario = session::get('id_usuario');
            $role =    session::get('role_id');
            $emp =     (session::get('actEmp'))?session::get('actEmp'):array();
        //print_r($emp);exit();
        
        //$emp=session::get('actEmp');
        if(count($emp)>0)
        {
			foreach($emp as $val)
			{
				if($val['condicion_empresa']==1)
				{
					$empresa = $val['id'];
				}	
			}
					
                                    $sql="select rec.* from recurso as rec where rec.estatus_recurso = 1 "
					. "and modulo_id = '$modulo' and "
					. "rec.id in (select recurso_id from recursousuario "
					. "where usuario_id='$usuario' and empresa_id = '$empresa') "
					. " UNION "
					. "select rec.* from recurso as rec where rec.estatus_recurso = 1 "
					. "and modulo_id = '$modulo' and "
					. "rec.id in (select recurso_id from recursorole "
					. "where role_id='$role' and empresa_id = '$empresa') order by posicion_recurso";
			
			//die($sql);
			$datos=$this->_db->query($sql);
			$this->_recursos = $datos->fetchAll();
		}else
			return array();
			
	}
	public function getMenu()
	{
            $menu = array();
            $this->getModulos();
            if(count($this->_modulos))
            {
                foreach ($this->_modulos as $value)
                {
                    $this->_menu[] = array(
                                    'id'=>$value['clave_modulo'],
                                    'titulo'=>$value['nombre_modulo'],
                                    'enlace'=>BASE_URL.$value['url_modulo'],
                                    'imagen'=>$value['icon_modulo'],
                                    'clave'=>$value['id']    
                                    );

                }

            }
		
		
		return $this->_menu;
	}
	public function getSubmenu($modulo)
	{
            $this->_submenu=array();
            $modu = $this->getModulo($modulo);
            //print_r($modu);
            if($modu)
            {
                   // $this->getRecurso($modu['id_modulo']);
                    $this->getRecursoUsuario($modu['id']);
            }
            if(count($this->_recursos))
            {
                foreach ($this->_recursos as $value) {
                        $this->_submenu[] = array(
                                'id'=>$value['nombre_recurso'],
                                'titulo'=>$value['nombre_recurso'],
                                'enlace'=>BASE_URL . $value['url_recurso'],
                                'imagen'=>$value['icon_recurso'],
                                'clave' =>$value['id']
                        );
                }
            }
            //print_r($this->_submenu);exit();
            return $this->_submenu;
	}
	// carga los datos de un modulo en espesifico
	private function getModulo($modulo)
	{
		$sql="select * from modulo where clave_modulo='$modulo'";
		//die($sql);
		$datos=$this->_db->query($sql);
		return $datos->fetch();
	}
	
	public function menu()
	{
		if(session::get('actEmp'))
		{
            $menu = array();
            $listaMenu = $this->getMenu();

            if(count($listaMenu)>0)
            {
                foreach($listaMenu as $value)
                {
                    $submenu = $this->getSubmenu($value['id']);
                    $menu[] = array(
                            'id'=>$value['id'],
                            'titulo'=>$value['titulo'],
                            'enlace'=>$value['enlace'],
                            'imagen'=>$value['imagen'],
                            'clave' =>$value['clave'],
                            'submenu'=>	$submenu
                            );	

                }

            }

            return $menu;
			
		}else
			{
				return array();	
			}
	}
	
}


?>
