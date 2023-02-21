<?php
class roleModel extends model
{
    public function __construct() {
        parent::__construct();
    }

    // carga listado de roles del sistema
    public function cargarRoles($ref=FALSE,$emp=FALSE)
    {
      if($emp){
        if($ref)
        {
            $sql = "select * from role where estatus_role='1' and nombre_role like '%$ref%' and empresa_id='$emp' "
            . "order by nombre_role, descripcion_role   ";
        }
        else
        {
            $sql = "select * from role where estatus_role='1' and empresa_id='$emp' "
            . " order by nombre_role, descripcion_role ";
        }
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
      }else {
        return FALSE;
      }
    }
    
    public function getAllRoles()
    {        
       $res = r::findAll("role");
       return $res; 
    } 
    
    //metodo qsue busca un rol por su id
    public function buscarRole($id)
    {
        $sql = "select * from role where id = '$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
        {
            return array();
        }
    }

    //metodo que carga el role de un usuario.
    public function cargarRoleUsuario($usuario)
    {
        $sql = "select role_id from usuario where id = '$usuario'";
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetch();
    }
    //metodo que carga los recursos de un role
    public function cargarRecursoRole($role)
    {
        $sql = "select trr.recurso_id,trec.nombre_recurso,trec.clave,trr.fecha_ult_act from recursorole as trr,recurso as trec where trr.role_id = '$role'"
                . " and trr.recurso_id = trec.id_recurso group by recurso_id order by recurso_id";
        $res = $this->_db->query($sql);
		if($res)
		{
			$res->setFetchMode(PDO::FETCH_ASSOC);
			return $res->fetchAll();
		}else
			return array();
    }



   //===========================================================================
   //METODO QUE PERMITE INSERTAR UN NUEVO REGISTRO EN LA TABLA ROLE
   //===========================================================================
   public function insertar($datos)
   {
       if($datos)
       {
           $sql = "insert into role(nombre_role,descripcion_role,estatus_role,fecha_crea_role,empresa_id)"
                   . "values"
                   . "('". $datos['nombre']."','". $datos['descripcion']."','1',now(),'".$datos['empresa']."')";

           $res = $this->_db->exec($sql);
           if(!$res)
           {
               $error =$this->_db->getError();
				logger::errorLog($error['2'],'DB');
				return FALSE;
           }
           return true;
       }
   }
   //===========================================================================
   public function actualizar($datos)
   {
       if($datos)
       {
           $sql = "update role set nombre_role = '".  $datos['nombre']."',"
                   . "descripcion_role = '". $datos['descripcion']."' where id = '".$datos['id']."'";

           $res = $this->_db->exec($sql);
           if(!$res)
           {
               return false;
           }
           return true;
       }
   }
   public function activar($id)
   {
       $sql = "update role set estatus_role ='1' where id_role='$id'";
       $res = $this->_db->exec($sql);
        if(!$res)
        {
            return false;
        }
        return true;
   }
   public function desactivar($id)
   {
       $sql = "update role set estatus_role ='9' where id_role='$id'";
       $res = $this->_db->exec($sql);
        if(!$res)
        {
            return false;
        }
        return true;
   }
   //para verificar que no se repita un mismo registro
    public function verificar_existencia($ref,$desc)
    {
        $sql = "select count(*)as total from role where nombre_role = '$ref' "
        . "and descripcion_role='$desc' ";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $data = $res->fetch();
            if($data['total'] > 0)
            {
                return $data;
            }
            else
            {
                return array("total" => 0);
            }
        }
        else
        {
            return array("total" => 0);
        }
    }

      //VERIFICAR UTILIZACION
    public function verificar_uso($cod)
    {
        $sql =" select count(*) as total from usuario as usu, recursorole as rec"
            . " where usu.role_id=".$cod." or rec.role_id=".$cod;
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $data = $res->fetch();
            if($data['total'] > 0)
            {
                return $data;
            }
            else
            {
                return array("total" => 0);
            }
        }
        else
        {
            return array("total" => 0);
        }
    }

	public function eliminarRecurso($recurso,$rol)
	{
		$sql = "delete from recursorole where recurso_id = '$recurso' and role_id='$rol' ";
		 $res = $this->_db->exec($sql);
        if(!$res)
        {
            return false;
        }
        return true;

	}

}
