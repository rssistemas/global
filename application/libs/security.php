<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 28/07/2019
 * @time 04:21:38 PM
 */

class security
{
    private $_registry;
    private $_db;
    private $_data;
    
    public function __construct() {
         $this->registry = registry::getInstancia();
         $this->_db = $this->registry->_db;
         
                 
         
    }
    
    public function conection($user,$password)
    {
        if($this->checkUser($user, $password))
        {
            return true;
        }else
            {
                $mensaje = "Error de Acceso al Sistema, usuario: ".$user ." no identificado"  ;
                $this->securityLog($mensaje);
                return false;
            }
    }
    
    public function getUser()
    {
        return $this->_data['user'];
    }
    
    public function getPerson()
    {
        return $this->_data['person'];
    }
    
    private function checkUser($user,$pass)
    {   
        $clave = Hash::getHash('md5',$pass, HASH_KEY);
        $sql = "select u.* from usuario as u,clave as p where u.alias_usuario ='".$user."' "
                . "and  p.usuario_id = u.id  and  p.clave ='".$clave."' and p.estatus_clave = '1' ";
        
        //die($sql);
        $req = $this->_db->sqlQuery($sql);
        if(count($req))
        {
            $this->_data['user'] = $req;
            return true;
        }       
        return false;
    }
    
    public function inUser($usuario)
    {
		$ip = $this->getRealIP();
		$sql="update usuario set fec_ult_ent= now(),condicion_usuario='CONECTADO',ip_ult_ent='".$ip."' where id_usuario ='".$usuario."'";	
		$res = $this->_db->exec($sql);
		if(!$res)
		{
			$error =$this->_db->getError();
			logger::errorLog($error['2'].' Table:Bitacora','DB');
		 //   $this->_db->cancel();
			return false;               
		}
		return true;
		
	}
	
	public function outUser($id)
    {
		$ip = $this->getRealIP();
		$sql="update usuario set fec_ult_ent= now(),condicion_usuario='DESCONECTADO',ip_ult_ent='".$ip."' where id_usuario ='".$id."'";	
		$res = $this->_db->exec($sql);
		if(!$res)
		{
			$error =$this->_db->getError();
			logger::errorLog($error['2'].' Table:Bitacora','DB');
		 //   $this->_db->cancel();
			return false;               
		}
		return true;
		
	}
    
    public function blockUser($id)
    {
		$ip = $this->getRealIP();
		$sql="update usuario set fec_ult_ent= now(),condicion_usuario='BLOQUEADO',ip_ult_ent='".$ip."' where id_usuario ='".$id."'";	
		$res = $this->_db->exec($sql);
		if(!$res)
		{
			$error =$this->_db->getError();
			logger::errorLog($error['2'].' Table:Bitacora','DB');
		 //   $this->_db->cancel();
			return false;               
		}
		return true;	
		
		
	}
      
    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    public function  loadPersonUser($valor)
    {
        $sql = "select * from persona where id_persona ='".$valor."' ";
        
        $req = $this->_db->sqlQuery($sql);
        if(!count($req))
        {
            return false;
        }
        $this->_data['person'] = $req;
        return true;
        
    }
    
    //-------------------------------------------------------------------------
    //method that registers access for user in log
    //-------------------------------------------------------------------------
    public function securityLog($mensaje)
    {      
	   if($log = fopen(LOG_PATH."security.txt","a+"))
	   {
		   if(!empty($mensaje))
		   {
			  fwrite($log, date("F j, Y, g:i a").'  '.$mensaje.'  desde IP: '.$this->getRealIP(). ' mediante :'.$this->getBrowser() .chr(13));
		   }    
		   fclose($log);
		   return TRUE;
	   }
        
    } 
    
    //-------------------------------------------------------------------------
    //method that registers access user in bitacora
    //-------------------------------------------------------------------------
    public function securityBitacora($mensaje,$usuario,$recurso,$accion)
    {      
		if(!empty($mensaje))
		{
			$sql="insert into bitacora(fecha,usuario,recurso_id,accion,contenido,ip,navegador)
			values(now(),'".$usuario."','".$recurso."','".$accion."')";
		  	  
		  
		  fwrite($log, date("F j, Y, g:i a").'  '.$mensaje.'  desde IP'.$this->getRealIP(). ' mediante :'.$this->getBrowser() .chr(13));
		}    
		fclose($log);
		 return TRUE;
	   
        
    }    
	//method that return the IP Client    
    public function getRealIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }  
    
    
    public function getBrowser(){

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if(strpos($user_agent, 'MSIE') !== FALSE)
       return 'Internet explorer';
     elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
       return 'Microsoft Edge';
     elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
        return 'Internet explorer';
     elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
       return "Opera Mini";
     elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
       return "Opera";
     elseif(strpos($user_agent, 'Firefox') !== FALSE)
       return 'Mozilla Firefox';
     elseif(strpos($user_agent, 'Chrome') !== FALSE)
       return 'Google Chrome';
     elseif(strpos($user_agent, 'Safari') !== FALSE)
       return "Safari";
     else
       return 'No hemos podido detectar su navegador';


    }    
}
