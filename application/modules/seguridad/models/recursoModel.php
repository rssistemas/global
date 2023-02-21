<?php
class recursoModel extends model
{
    
    public function __construct() {
        parent::__construct();
    }
    //metodo que carga todos los recursos
    public function cargarRecurso()
    {
        $sql = "select * from recurso";
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }
    
    
    public function noRecursoRole($role,$emp=false)
    {
		if(!$emp)
		{
			$sql = "select rec.* from recurso as rec where rec.id_recurso "
                . "not in(select recurso_id from recursorole where role_id = '$role')";
        }else
			{
				$sql = "select rec.* from recurso as rec where rec.empresa_id='$emp' and rec.id_recurso "
                . "not in(select recurso_id from recursorole where role_id = '$role')";	
			}
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }
    
    
    public function noRecursoUsuario($usuario,$emp=0)
    {
		if($emp<= 0)
		{
			$sql = "select rec.* from recurso as rec where rec.id_recurso "
                . "not in(select recurso_id from recursousuario where usuario_id = '$usuario')";
        }else
			{
				$sql = "select rec.* from recurso as rec where rec.empresa_id = '$emp' and rec.id_recurso "
                . "not in(select recurso_id from recursousuario where usuario_id = '$usuario')";
			}
        $res = $this->_db->query($sql);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }        
    
}
