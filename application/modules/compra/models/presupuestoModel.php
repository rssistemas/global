<?php 
	class presupuestoModel extends model
	{
	
	
		public function __construct()
		{
			parent::__construct();
		
		}
	
		public function cargarPresupuesto()
		{
			
			$sql ="select * from presupuesto";
			$res = $this->_db->query($sql);
			if($res)
			{
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetchAll();
			}
			else
			{
				return array();
			}			
			
		}
	
	}


?>