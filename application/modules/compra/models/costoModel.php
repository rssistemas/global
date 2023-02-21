<?php 
class costoModel extends  model
{
	public function __construct()
	{
		parent::__construct();
		
	}
	
	public function cargarCosto($producto)
	{
		
		
	}	
	//------------------------------------------------------------------------
	//METODO QUE CALCULA EL COSTO DE UN PRODUCTO
	//------------------------------------------------------------------------
	public function calcularCosto($datos)
	{
					
		
	}
	//------------------------------------------------------------------------
	//METODO QUE ACTUALIZA EL COSTO DEL PRODUCTO(se acondiciona la data para hacer las nserciones)
	//------------------------------------------------------------------------
	public function actualizarCosto($datos)
	{	$costo = array();
		if(count($datos))
		{
			foreach($datos as $val)
			{
				$producto = $val['producto'];
				$monto    = $val['monto'];
				if(in_array($producto, $costo))
				{
					$key = array_search($producto, array_column($costo, 'producto'));
					$costo[$key]['monto'] = $costo[$key]['monto'] + $monto;  
					
				}else
					{
						$costo[] = array("producto"=> $producto,"monto"=>$monto,"compra"=>$val['compra']);
					}
				
			}
			
			if(count($costo))
			{	
				foreach($costo as $cto)
				{
					
					$sql = "update stock set costo_stock = costo_stock + '".$cto['monto']."'  
					where compra_id = '".$cto['compra']."' and producto_id = '".$cto['producto']."' ";
					
					die($sql);
					$res = $this->_db->exec($sql);
					if(!$res)
					{
						//$this->cancelar();
						return false;               
					}	
				}
				//$this->confirmar();
				
				return true;
			}else
				{
					return false;
				}
		}
		
		
	}
	
	
}

