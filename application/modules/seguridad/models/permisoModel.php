<?php
class permisoModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    //metodo que carga todos los permisos
    public function cargarPermiso($ref=FALSE,$emp)
    {
        if($ref)
        {
            $sql = "select * from permiso where estatus_permiso='1' and empresa_id='$emp' and nombre_permiso like '%$ref%' order by nombre_permiso ASC";
        }
        else
        {   
            $sql = "select * from permiso where estatus_permiso='1' and empresa_id='$emp' order by nombre_permiso ASC";
        }
        
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }
    
    //para verificar que no se repita un mismo registro
    public function verificar_existencia($ref,$desc)
    {
        $sql = "select count(*)as total from permiso where nombre_permiso = '$ref' "
        . "and clave='$desc' ";
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
        $sql =" select count(*) as total from recursousuario as usu, recursorole as rec"
            . " where usu.permiso_id=".$cod." and rec.permiso_id=".$cod;
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
    
    public function incluirPermiso($datos)
    {
        if($datos)
        {
            $sql = "insert into permiso("
                    . "nombre_permiso,"
                    . "descripcion_permiso,"
                    . "estatus_permiso,"
                    . "fecha_crea_permiso,"
                    . "clave,"
                    . "empresa_id"
                    . ")"
                    . "values("
                    . "'".  $datos['nombre']."',"
                    . "'".  $datos['descripcion']."',"
                    . "'1',"
                    . "now(),"
                    . "'".$datos['clave']."',"
                    . "'".$datos['empresa']."'"
                    .")";
            $res = $this->_db->exec($sql);
            if(!$res)
            {
                return FALSE;               
            }  else {
                return TRUE;
            }
        }
            
        
    }
    public function modificarPermiso($datos)
    {
        $sql = "update permiso set nombre_permiso = '".  $datos['nombre']."',"
                . "descripcion_permiso = '".  $datos['descripcion']."',clave = '".$datos['clave']."' "
                . "where id_permiso = '".$datos['id']."'";
        $res = $this->_db->exec($sql);
            if(!$res)
            {
                return FALSE;               
            }  else {
                return TRUE;
            }
        
    }
            
    
    public function activar($permiso)
    {
        $sql = "update permiso  set estatus_permiso = '1' where id_permiso='$permiso'";
        $res = $this->_db->exec($sql);
            if(!$res)
                return FALSE;               
            else
                return TRUE;
    }        
    
    public function desactivar($permiso)
    {
        $sql = "update permiso  set estatus_permiso = '9' where id_permiso='$permiso'";
        $res = $this->_db->exec($sql);
            if(!$res)
                return FALSE;               
            else
                return TRUE;
    }
    public function buscar($id)
    {
        $sql="select * from permiso where id_permiso = '$id' ";
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
    //--------------------------------------------------------------------------
    // metodos para permisos de role
    //--------------------------------------------------------------------------

    //metodo que permite buscar un permiso de un recurso
    public function buscarPermisoRecursoRole($recurso,$permiso,$role)
    {
        $sql = "select * from  recursorole where role_id = '$role' and recurso_id = '$recurso' and permiso_id = '$permiso'";
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetch();
    }        
            
    // metodo que carga los permiso de un recurso dado.
    public function cargarPermisoRecursoRole($recurso,$role)
    {
        $sql = "select tper.id_permiso,tper.nombre_permiso,tper.descripcion_permiso,trr.estatus_recurso_role as estatus from recursorole as trr,permiso as tper "
                . "where trr.recurso_id = '$recurso' and trr.permiso_id = tper.id_permiso and trr.role_id = '$role' order by tper.id_permiso";
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();   
    }
    // metodo que permite cargar los permisos no asignados a un recurso
    public function cargarNoPermisoRecursoRole($recurso,$role)
    {
         $sql = "select tper.id_permiso,tper.nombre_permiso,tper.descripcion_permiso,9 as estatus from permiso as tper "
                . "where tper.id_permiso not in(select permiso_id from recursorole where recurso_id = '$recurso' and role_id = '$role')"
                 . " order by tper.id_permiso";
        
        // die($sql);
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
        
    }        
    // metodo que inserta los permisos de un recurso
    public function incluirPermisoRecursoRole($datos)
    {
        if(count($datos))
        {   
            
            for($i=0;$i < count($datos);$i++)
            {
                $sql ="insert into recursorole "
                . "(recurso_id,role_id,permiso_id,estatus_recurso_role,fecha_ult_act)"
                . "values('".$datos[$i]['recurso']."','".$datos[$i]['role']."','".$datos[$i]['permiso']."','1',now())";
                
                
                $res = $this->_db->exec($sql);
                if(!$res)
                {
                    $error =$this->_db->getError();
					logger::errorLog($error['2'],'DB');
					return false;
                }
        
            }
            
            return true;
            
        }        
    } 
    // metodo que edita los permiso existentes de un recurso o incluye 
    public function editarPermisoRecursoRole($datos)
    {
        if(count($datos))
        {
            //$this->iniciar();
            foreach ($datos as $val)
            {
                //se busca si existe el recurso que se quiere editar
                $recurso = $this->buscarPermisoRecursoRole($val['recurso'],$val['permiso'],$val['role']);
                //print_r($recurso); exit();
                if(isset($recurso['id_recurso_role']))
                {
                    if($val['valor']==9)
                    {    
                        $sql = "update recursorole set estatus_recurso_role = '".$val['valor']."',fecha_ult_act=now()"
                        . " where role_id = '".$val['role']."' and recurso_id = '".$val['recurso']."'"
                        . " and permiso_id = '".$val['permiso']."'  ";
                    }else
                    {
                           $sql = "update recursorole set estatus_recurso_role = '".$val['valor']."',fecha_ult_act=now()"
                            . " where role_id = '".$val['role']."' and recurso_id = '".$val['recurso']."'"
                            . " and permiso_id = '".$val['permiso']."'  ";
                    }
                }else
                {
                    $sql ="insert into recursorole "
                    . "(recurso_id,role_id,permiso_id,estatus_recurso_role,fecha_ult_act)"
                    . "values('".$val['recurso']."','".$val['role']."','".$val['permiso']."','1',now())";
                
                    //die($sql);
                }
                
                $res = $this->_db->exec($sql);
                if(!$res)
                {
                    $error =$this->_db->getError();
                    logger::errorLog($error['2'].'Table : permiso','DB');
                }
            }    
            //$this->confirmar();
            return true;
        }   
            
    } 
    
    public function desactivarPermisoRecursoRole($ref)
    {
        $sql = "update recursorole  set estatus_recurso_role = '9' where id_recurso_role='$ref'";
        $res = $this->_db->exec($sql);
            if(!$res)
                return FALSE;               
            else
                return TRUE;
    }
    
    //--------------------------------------------------------------------------
    // metodos para permisos de usuario
    //--------------------------------------------------------------------------
    
    //metodo que permite buscar un permiso de un recurso
    public function buscarPermisoRecursoUsuario($recurso,$permiso,$usuario)
    {
        $sql = "select * from  recursousuario where usuario_id = '$usuario' and recurso_id = '$recurso' and permiso_id = '$permiso'";
        
        $res = $this->_db->query($sql);
        if($res)
            return $res->fetch();
        else
            return array();
    }
    
    public function cargarPermisoRecursoUsuario($recurso,$usuario)
    {
        $sql = "select tper.id_permiso, tper.nombre_permiso, tper.descripcion_permiso,"
            . "tru.estatus_recurso_usuario as estatus "
            . "from recursousuario as tru, permiso as tper "
            . "where tru.recurso_id = '$recurso' and tru.permiso_id = tper.id_permiso "
            . "and tru.usuario_id = '$usuario' order by tper.id_permiso";
        //die($sql);
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
        
    }
    // metodo que permite cargar los permisos no asignados a un recurso
    public function cargarNoPermisoRecursoUsuario($recurso)
    {
         $sql = "select tper.id_permiso,tper.nombre_permiso,tper.descripcion_permiso,2 as estatus from permiso as tper "
                . "where tper.id_permiso not in(select permiso_id from recursousuario where recurso_id = '$recurso') order by tper.id_permiso";
        
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
        
    }
    // metodo que inserta los permisos de un recurso de usuario
    public function incluirPermisoRecursoUsuario($datos)
    {
        if(count($datos))
        {   
            //$this->iniciar();
            for($i=0;$i < count($datos);$i++)
            {
                $sql ="insert into recursousuario"
                . "(recurso_id,usuario_id,permiso_id,estatus_recurso_usuario,fecha_ult_act,empresa_id)"
                . "values('".$datos[$i]['recurso']."','".$datos[$i]['usuario']."','".$datos[$i]['permiso']."','".$datos[$i]['estatus']."',now(),'".$datos[$i]['empresa']."')";
                
                
                //die($sql);
                $res = $this->_db->exec($sql);
                if(!$res)
                {
                    //$this->cancelar();
                    return FALSE;
                }
        
            }
            //$this->confirmar();
            return true;
            
        }        
    }
    
    // metodo que edita los permiso existentes de un recurso o incluye 
    public function editarPermisoRecursoUsuario($datos)
    {
        
        if(count($datos))
        {
            $this->iniciar();
            for($i=0;$i < count($datos);$i++)
            {
                  if($datos[$i]['estatus']!=2)
                  {
                        $permisoAct = $this->buscarPermisoRecursoUsuario($datos[$i]['recurso'],$datos[$i]['permiso'],$datos[$i]['usuario']);
                            
                        if($permisoAct)
                        {
                            $sql = "update recursousuario set estatus_recurso_usuario = '".$datos[$i]['estatus']."',fecha_ult_act=now()"
                            . " where usuario_id = '".$datos[$i]['usuario']."' and recurso_id = '".$datos[$i]['recurso']."'"
                            . " and permiso_id = '".$datos[$i]['permiso']."'  ";
                            $res = $this->_db->exec($sql);  
                            //die($sql);
                        }else
                            {
                                $sql ="insert into recursousuario"
                                . "(recurso_id,usuario_id,permiso_id,estatus_recurso_usuario,fecha_ult_act)"
                                . "values('".$datos[$i]['recurso']."','".$datos[$i]['usuario']."','".$datos[$i]['permiso']."','".$datos[$i]['estatus']."',now())"; 
                                //die($sql);
                                $res = $this->_db->exec($sql);
                                if(!$res)
                                {
                                    $this->cancelar();
                                    return FALSE;
                                }
                            }
                    
                        
                  }

            }
            $this->confirmar();
            return true;
            
        }   
            
    }        
    
    
    
}
