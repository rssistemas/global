<?php 
class clienteModel extends model
{
	
	public function __construct()
	{
		parent::__construct();		
	}
	
	public function cargarCliente($valor = false)
	{
		if($valor)
			$sql = "select * from cliente where estatus_cliente !='9' and razon_social_cliente like '%$valor%' order by rif_cliente,razon_social_cliente";
		else
			$sql = "select * from cliente where estatus_cliente !='9' order by rif_cliente,razon_social_cliente";
		
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            {
                return array();
            }   
		
		
	}
	
	
	public function insertar($datos)
	{
		
		$sql = "insert into cliente(
				fecha_creacion,
				rif_cliente,
				razon_social_cliente,
				denominacion_cliente,
				direccion_fiscal_cliente,
				telefono_cliente,
				celular_cliente,
				correo_cliente,
				tipo_cliente,
				sector_id,
				credito_cliente,
				limite_credito_cliente,
				estatus_cliente,
				estado_id,
				municipio_id,
				parroquia_id
				)values(
				now(),
				'".$datos['rif']."',
				'".$datos['razon_social']."',
				'".$datos['denominacion']."',
				'".$datos['direccion']."',
				'".$datos['local']."',
				'".$datos['celular']."',
				'".$datos['correo']."',
				'".$datos['tipo']."',
				'".$datos['sector']."',
				'".$datos['credito']."',
				'".$datos['limite']."',
				'1',
				'".$datos['estado']."',
				'".$datos['municipio']."',
				'".$datos['parroquia']."'	
				)";
		
		//die($sql);		
		$res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
        {
            return false;
        }		
		
	}
	
	
	public function modificar($datos)
	{
		$sql = "update cliente set
				rif_cliente = '".$datos['rif']."',
				razon_social_cliente = '".$datos['razon_social']."',
				denominacion_cliente = '".$datos['denominacion']."',
				direccion_fiscal_cliente = '".$datos['direccion']."',
				telefono_cliente = '".$datos['local']."',
				celular_cliente = '".$datos['celular']."',
				correo_cliente = '".$datos['correo']."',
				tipo_cliente = '".$datos['tipo']."',
				sector_id = '".$datos['sector']."',
				credito_cliente = '".$datos['credito']."',
				limite_credito_cliente = '".$datos['limite']."',
				estado_id =  '".$datos['estado']."',
				municipio_id =  '".$datos['municipio']."',
				parroquia_id =  '".$datos['parroquia']."' 
				where id_cliente ='".$datos['codigo']."' ";
				
				
		$res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
        {
            return false;
        }		
		
	}
	
	public function eliminar($id)
	{
		
		$sql="update cliente set estatus_cliente='9' where id_cliente = '$id'";
		$res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;	
        }
        else
        {
            return false;
        }
		
		
	}
	
	public function buscar($valor)
	{
		
		if($valor)
		{
			//si $valor es entero 
			if(is_int($valor))
			{
				$sql = "select * from cliente where id_cliente = '$valor' ";
			}else
			{
				$sql = "select * from cliente where rif_cliente = '$valor' order by id_cliente";
				
			}
			//die($sql);
			$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetchAll();
			}else
				return array();	
				
			
		}else
		{
			return false;
		}
		
	}
	
	public function buscarRifCliente($tip,$valor)
	{
			$rif = $tip.$valor;
				
			$sql = "select * from cliente where rif_cliente = '$rif'";
			//die($sql);
			$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetch();
			}else
				return array();	
				
			
		
		
	}
	
 public function buscarAutoCliente($valor)
    {        
        $val = array();
		$sql = "select * from cliente where razon_social_cliente like '%$valor%'";
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);            
            $datos = $res->fetchAll();
            foreach ($datos as $valor)
            {
                $val[] = array(
                    "label"=>  ucfirst($valor['razon_social_cliente']),
                    "value"=>array("nombre"=>  ucfirst($valor['razon_social_cliente']),
                                    "id"=>$valor['id_cliente']));
            }
            
            return $val;
            
        }else
            return array();
    }
	
	
	public function asignarPremisa($datos)
	{
		if($datos)
		{
			
			$sql = "insert into det_cliente(
				cliente_id,
				premisa_id,
				estatus_det_cliente,
				fecha_creacion)values("
				."'".$datos['cliente']."',"				
				."'".$datos['premisa']."',"
				."'1',"
				."now())";
			$res = $this->_db->exec($sql);
	        if($res)
	        {
	            return TRUE;	
	        }
	        else
	        {
	            return false;
	        }	
				
				
			}
	}
	
	//-----------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA  PREMISAS DEL CLIENTE 
	//-----------------------------------------------------------------------------------------------------
	public function buscarPremisaCliente($id,$premisa)
	{
		
		$sql = "select * from det_cliente as dcli,premisa as pre where dcli.cliente_id = '$id'
		 and dcli.premisa_id = pre.id_premisa and pre.id_premisa = '$premisa' ";
		//die($sql); 
		$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetch();
			}else
			 	return array();
		
		
	}
	
	//-----------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA  PREMISAS DEL CLIENTE 
	//-----------------------------------------------------------------------------------------------------
	public function buscarCorreoCliente($correo)
	{
		
		$sql = "select * from cliente where correo_cliente = '$correo'";
		//die($sql); 
		$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetch();
			}else
			 	return array();
		
		
	}
	
	//-----------------------------------------------------------------------------------------------------
	//METODO QUE CARGA TODAS LAS PREMISAS DEL CLIENTE 
	//-----------------------------------------------------------------------------------------------------
	public function cargarPremisaCliente($cliente)
	{
		
		$sql = "select * from det_cliente as dcli,premisa as pre where dcli.cliente_id = '$cliente'
		 and dcli.premisa_id = pre.id_premisa order by dcli.id_det_cliente,dcli.premisa_id ";
		//die($sql); 
		$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetchAll();
			}else
			 	return array();
		
		
	}

}