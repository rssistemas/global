<?php
class bitacoraModel extends model{
    
    public function __construct() {
        parent::__construct();
    } 
    
    public function consultar()
    {
       $sql = "select  bit.*,usu.alias_usuario,rec.nombre_recurso from bitacora as bit,usuario as usu,recurso as rec where"
               . " rec.id_recurso = bit.recurso_id and usu.id_usuario = bit.usuario_id order by bit.fecha ";
       //die($sql);
       $res = $this->_db->query($sql);
       if($res)
       {
           return $res->fetchAll(); 
       }else
           return array();
             
    }
	
	public function insertarBitacora($datos)
	{
		
		
		
		
	}
    
            
    
    
    
}