<?php
	class acl
	{
		private $registry;
		private $db;
		private $id;
		private $role;
		private $recurso;
		private $_guachiman;
		public function __construct($id = false)
		{
			if($id)
				$this->id = (int) $id;
			else {
				if(session::get('id_usuario'))
				{
					$this->id = session::get('id_usuario');
				}else
					{
						$this->id = 2;
					}
			}
                        
                        //die($this->id);
			$this->registry = registry::getInstancia();
			$this->db = $this->registry->_db;
                        $this->_guachiman = $this->registry->_guachiman;

			$this->role = $this->getRole();
			$this->recurso = $this->getRecursosRole();

			$this->compilarAcl();
		}

		public function compilarAcl()
		{
			$this->recurso = array_merge($this->recurso,$this->getRecursosUsuario());
		}
                //metodo que carga el rol del usuario
		public function getRole()
		{
                        $p = "select role_id from usuario where id ='$this->id'";
                        //die($p);
			$sql = $this->db->query($p);
			$role = $sql->fetch();
                       // print_r($role);
                        //exit();
			return $role['role_id'];
		}
		//metodo que carga los id de recursos del rol del usuario
		public function getRecursosRoleid()
		{
			$sql = $this->db->query("select recurso_id from recursorole where role_id = '{$this->role}'");

			$ids = $sql->fetchAll(PDO::FETCH_ASSOC);
			if(count($ids))
			{
				for($i = 0; $i < count($ids);$i++)
				{
					$id[] = $ids[$i]['recurso_id'];
				}
			}else
				$id = array();

			return $id;
		}
		//metodo que carga todo de la table trecursorole segun el role pasado
                //crea un arreglo con los permisos po role
		public function getRecursosRole()
		{
			$sql = $this->db->query("select * from recursorole where role_id = '{$this->role}'");

			$recursos = $sql->fetchAll(PDO::FETCH_ASSOC);
			$data = array();

			for($i = 0; $i < count($recursos);$i++)
			{
				$claveRec = $this->getRecursoClave($recursos[$i]['recurso_id']);
        $clavePer = $this->getPermisoClave($recursos[$i]['permiso_id']);
				if($claveRec==''){continue;}

				if($recursos[$i]['estatus_recurso_role']==1)
              $valor = TRUE;
				else
              $valor = FALSE;
				$clave = $claveRec.'_'.$clavePer;
				$data[$clave] = array(
					'key'=>$clave,
					'recurso'=>$this->getRecursoNombre($recursos[$i]['recurso_id']),
					'valor'=>$valor,
					'heredado'=>TRUE,
					'id'=>$recursos[$i]['recurso_id']);
			}
			return $data;
		}
		// metodo que retorna la clave de un recurso
		public function getRecursoClave($recursoID)
		{
			$recursoID = (int) $recursoID;

			$res = $this->db->query("select clave from recurso where id ='{$recursoID}'");

			$clave = $res->fetch(PDO::FETCH_ASSOC);
			if($clave==FALSE)
			{
				$valor = "";
			}
				else {
					$valor = $clave['clave'];
				}
			//print_r($clave);
			return $valor;
		}
      //metodo que retorna la clave de un permiso
      public function getPermisoClave($permisoID)
      {
          $recursoID = (int) $permisoID;
			$sql = "select clave from permiso where id ='{$permisoID}'";
			$clave = $this->db->query($sql);

			$clave = $clave->fetch();
			return $clave['clave'];

                }
                //	metodo que retorna el nombre de un recurso
		public function getRecursoNombre($recursoID)
		{
			$recursoID = (int) $recursoID;

			$clave = $this->db->query("select nombre_recurso from recurso where id ='{$recursoID}'");

			$clave = $clave->fetch();
			return $clave['nombre_recurso'];
		}

		public function getRecursosUsuario()
		{
			$ids = $this->getRecursosRoleid();

			if(count($ids))
			{
				$sql = "select * from recursousuario where usuario_id='{$this->id}' and recurso_id not in (".implode(",", $ids).")";
				//$sql = "select * from trecursousuario where usuario_id='{$this->id}'";
                                //die($sql);
                                $res = $this->db->query($sql);
				$recursos = $res->fetchAll(PDO::FETCH_ASSOC);
			}else
				{
					$recursos = array();
				}
			$data = array();

			for($i = 0; $i < count($recursos);$i++)
			{
				$claveRec = $this->getRecursoClave($recursos[$i]['recurso_id']);
                $clavePer = $this->getPermisoClave($recursos[$i]['permiso_id']);

				if($claveRec==''){continue;}

				if($recursos[$i]['estatus_recurso_usuario']==1) {
                   $valor = TRUE;
				}else
                    {
                    	$valor = FALSE;
                    }
				$clave = $claveRec.'_'.$clavePer;
				$data[$clave] = array(
					'key'=>$clave,
					'recurso'=>$this->getRecursoNombre($recursos[$i]['recurso_id']),
					'valor'=>$valor,
					'heredado'=>FALSE,
					'id'=>$recursos[$i]['recurso_id']);
			}

			return $data;

		}
		public function permiso($clave)
		{
           // print_r($this->recurso);
           // exit();
			if(array_key_exists($clave, $this->recurso))
			{
				if($this->recurso[$clave]['valor'] == 1)
				{
					return true;
				}
				return FALSE;
			}
            else{
                return false;
            }
            }
            public function acceso($clave,$error=105,$url=FALSE)
            {
                    //session::acceso();
//                if($this->_guachiman->investigarUsuario(session::get('id_usuario'))>1)
//                {
//                    header("location:". BASE_URL ."entrada/index/logout/" );
//                    //error::alerta('archivo','banco','1001');
//                    exit;
//                }else
//                {
                    if($this->permiso($clave))
                    {
                        return true;
                    }else
                    {
                       (int)$error;
                        header("location:". BASE_URL ."error/alerta/".$error.'/'.$url );
                        //error::alerta('archivo','banco','1001');
                        exit;
                    }

                //}

            }
            public function getRecursos()
            {
                    return $this->recurso;
            }
	}



?>
