<?php
class premisaModel extends model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function cargarPremisa($ref = false,$tipo = FALSE)
	{
		if(!$ref)
		{
			if(!$tipo)
			{
				$sql = "select * from premisa";	
			}else
				{
					$sql = "select * from premisa WHERE tipo_premisa = '$tipo' order by id_premisa";	
				}		
		}else
			{
				if($tipo)
				{
					$sql = "select * from premisa where nombre_premisa like '%$ref%' 
					and tipo_premisa = '$tipo' order by id_premisa,nombre_premisa";		
				}else
					{
						$sql = "select * from premisa WHERE nombre_premisa like '%$ref%' order by id_premisa";
					}
			}
					
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
	//---------------------------------------------------------------------------------
	//METODO QUE BUSCA PREMISA  PR ID O POR COMPARACION
	//---------------------------------------------------------------------------------
	public function buscar($valor)
	{
		if(is_int($valor))
		{
			$sql = "select * from premisa where id_premisa = '$valor'";			
		}else
			{
				$sql = "select * from premisa where nombre_premisa = '$valor' ";
				
			}
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
	
	public function insertar($datos)
	{
			
		$sql = "insert into premisa("
			."fecha_creacion,"
			."nombre_premisa,"
			."descripcion_premisa,"
			."accion_premisa,"
			."porcentaje_premisa,"
			."estatus_premisa,"
			."operador_premisa,"
			."comparador_premisa,"
			."tipo_premisa)"
			."values("
			."now(),"
			."'".strtoupper($datos['nombre'])."',"
			."'".strtoupper($datos['descripcion'])."',"
			."'".strtoupper($datos['accion'])."',"
			."'".$datos['porcentaje']."',"
			."'1',"
			."'".$datos['operador']."',"
			."'".$datos['valor']."',"
			."'".$datos['tipo']."')";
		
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
		$sql = "update premisa set "
			."nombre_premisa = '".strtoupper($datos['nombre'])."',"
			."descripcion_premisa = '".strtoupper($datos['descripcion'])."',"
			."accion_premisa = '".strtoupper($datos['accion'])."',"
			."porcentaje_premisa = '".$datos['porcentaje']."',"
			."comparador_premisa = '".$datos['valor']."',"
			."tipo_premisa = '".$datos['valor']."', "
			."operador_premisa = '".$datos['operador']."'"
			." where id_premisa =  '".$datos['id']."'";		
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
	
	public function eliminar($id)
	{
		$sql = "update premisa set estatus_premisa = '9' where id_premisa = '$id'";
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
	
	public function buscarRelacion($id,$tipo=false)
	{
		
		switch ($tipo) {
			case 'VENTA':
				$sql = "select dcli.*,pre.* from det_cliente as dcli,premisa as pre  
				where premisa_id = '$id' and pre.id_premisa = dcli.premisa_id order by id_premisa ";			
				break;
			case 'COMPRA':
					$sql = "select * from det_cliente where premisa_id = '$id'";
				break;
			case 'COMICION':
					$sql = "select * from det_cliente where premisa_id = '$id'";
				break;	
			default:
					$sql = "select * from det_cliente where premisa_id = '$id'";
				break;
		}
		//die($sql);
		///$sql = "select * from det_cliente where premisa_id = '$id'";
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchall();
        }else
            {
                return array();
   		 }
		
		
	}
	
}
