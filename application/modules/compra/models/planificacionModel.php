<?php
class planificacionModel extends model
{
	public function __construct()
	{
		parent::__construct();
	
	}	
	//----------------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------------
	public function cargarPln($pln = false)
	{
		if($pln)
		{
			$sql = "select pc.*,cp.descripcion_concepto as concepto, uo.nombre_unidad_operativa as unidad,
				dpto.descripcion_departamento as departamento
				from pln_compra as pc,concepto_pln as cp,unidad_operativa as uo,departamento as dpto 
				where pc.estatus_pln ='1' and pc.id_planificacion='$pln' and pc.objetivo_pln = cp.id_concepto_pln 
				and uo.id_unidad_operativa = unidad_operativa_id and pc.departamento_id = dpto.id_departamento
					";
		}else	
		{
			$sql = "select pc.*,cp.descripcion_concepto as concepto, uo.nombre_unidad_operativa as unidad,
				dpto.descripcion_departamento as departamento
				from pln_compra as pc,concepto_pln as cp,unidad_operativa as uo,departamento as dpto 
				where pc.estatus_pln ='1' and pc.objetivo_pln = cp.id_concepto_pln 
				and uo.id_unidad_operativa = unidad_operativa_id and pc.departamento_id = dpto.id_departamento
					";	
		}
		
		//die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
		
		
	}
	//------------------------------------------------------------------------------------------------
	//
	//------------------------------------------------------------------------------------------------
	public function buscarPlanificacion($pln)
	{
		
		if($pln)
		{
			$sql ="select pln.*,con.descripcion_concepto,  
			from pln_compra as pln,concepto_pln as con,det_pln_compra as dpln 
			where id_planificacion = '$pln' and id_concepto_pln = objetivo_pln";
			
			$res = $this->_db->query($sql);
			if($res){
				$res->setFetchMode(PDO::FETCH_ASSOC);
				return $res->fetch();
			}else
				return array();
		}	
		
		
	}	
	
	
	//-------------------------------------------------------------------------------
	//METODO QUE INSERTA UN NUEVO REGISTRO DE PLANIFICACION "SOLO EL MAESTRO "
	//-------------------------------------------------------------------------------
	public function insertar($datos)
	{
		
		$this->iniciar();
		
		$sql = "insert into pln_compra("
				."fecha_creacion,"
				."fecha_inicio,"
				."fecha_fin,"
				."estatus_pln,"
				."condicion_pln,"
				."comentario_pln,"
				."usuario_pln,"
				."empresa_id,"
				."unidad_operativa_id,"
				."departamento_id,"
				."objetivo_pln,"
				."ref_contable)values("
				."now(),"
				."'".$datos['inicio']."',"
				."'".$datos['fin']."',"
				."'1',"
				."'POR EVALUAR',"
				."'".$datos['comentario']."',"
				."'".$datos['usuario']."',"
				."'".$datos['empresa']."',"
				."'".$datos['unidad']."',"
				."'".$datos['dependencia']."',"
				."'".$datos['objetivo']."',"
				."'".$datos['ref_contable']."')";
				
		$res = $this->_db->exec($sql);
        if(!$res)
        {
			$this->cancelar();
            return false;               
        }
        else
        {		
			
			$id_pln = $this->ultimo();
			
			if(count($datos['codigo'])>0)
			{
				$codigo      = $datos['codigo'];
				$descripcion = $datos['descripcion'];
				$cantidad    = $datos['cantidad'];
				$plazo       = $datos['plazo'];
				$prioridad   = $datos['prioridad'];
				$tipo        = $datos['tipo'];
				
				for($i=0; $i < count($datos['codigo']);$i++ )
				{
					$sql ="insert into det_pln_compra("
					. "pln_compra_id,"
					. "tipo_requisito,"
					. "prioridad_requisito,"
					. "plazo_ejecucion,"
					. "id_requisito,"
					. "cantidad_requisito,"
					. "estatus_det_pln_compra,"
					. "condicion_requisito,"
					. "fecha_creacion,"
					. "usuario_creador)values("
					. "'".$id_pln."',"
					. "'".$tipo[$i]."',"
					. "'".$prioridad[$i]."',"
					. "'".$plazo[$i]."',"
					. "'".$codigo[$i]."',"
					. "'".$cantidad[$i]."',"
					. "'1',"
					. "'POR EVALUAR',"
					. "now(),"
					. "'".$datos['usuario']."'"			
					
					. ")";			
					
					$res = $this->_db->exec($sql);
					if(!$res)
					{
						$this->cancelar();
						return false;               
					}
					
				}
				$this->confirmar();
				return true;	
			}	
	
			
		
		}
		
		
	}
	
	//----------------------------------------------------------------------------------------------	
	//METODO QUE CARGA DETALLE DE PLANIFICACION $pln(utilizado en met. cargarCotrol,)
	//----------------------------------------------------------------------------------------------
	public function cargarDetalle($pln = false)
	{
		
		if($pln)
		{
			//$sql = "select * from det_pln_compra where pln_compra_id = '$pln'";	
			
			$sql="select dpc.*,prd.nombre_producto as requisito from det_pln_compra as dpc,producto as prd,det_producto as dprd
				where dpc.pln_compra_id = '".$pln."' and dpc.id_requisito = dprd.id_det_producto and prd.id_producto = dprd.producto_id and tipo_requisito='PRODUCTO' 
				union
				select dpc.*,svr.nombre_servicio as requisito from det_pln_compra as dpc,servicio as svr
				where dpc.pln_compra_id = '".$pln."' and dpc.id_requisito = svr.id_servicio and tipo_requisito='SERVICIO' ;";
			
		}else
			$sql = "select * from det_pln_compra ";	
			
			
				
		//die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
		
	}
	
	//------------------------------------------------------------------------------------
	//METODO QUE CARGA LISTADO DE PLANIFICACION CON DETALLE utilizado en(index,)
	//------------------------------------------------------------------------------------
	public function cargarControl($pln = false)
	{
		$data = array();
		if(!$pln)
		{
			$planificacion = $this->cargarPln();
			if(count($planificacion)>0)
			{
				foreach($planificacion as $p)
				{
					$detalle = $this->cargarDetalle($p['id_planificacion']);
									
					
					
					$data[] = array(
								"id"=>$p['id_planificacion'],
								"creacion"=>$p['fecha_creacion'],
								"inicio"=>$p['fecha_inicio'],
								"fin"=>$p['fecha_fin'],
								"usuario"=>$p['usuario_pln'],
								"estatus"=>$p['estatus_pln'],
								"condicion"=>$p['condicion_pln'],
								"comentario"=>$p['comentario_pln'],
								"unidad"=>$p['unidad'],
								"dpto"=>$p['departamento'],
								"concepto"=>$p['concepto'],
								"detalle"=>$detalle
								);
				}
				
			}	
			
		}else
		{
			$planificacion = $this->cargarPln($pln);
			if(count($planificacion)>0)
			{
				foreach($planificacion as $p)
				{
					$detalle = $this->cargarDetalle($p['id_planificacion']);
														
					$data[] = array(
								"id"=>$p['id_planificacion'],
								"creacion"=>$p['fecha_creacion'],
								"inicio"=>$p['fecha_inicio'],
								"fin"=>$p['fecha_fin'],
								"usuario"=>$p['usuario_pln'],
								"estatus"=>$p['estatus_pln'],
								"condicion"=>$p['condicion_pln'],
								"comentario"=>$p['comentario_pln'],
								"unidad"=>$p['unidad'],
								"dpto"=>$p['departamento'],
								"concepto"=>$p['concepto'],
								"detalle"=>$detalle
								);
				}
				
			}
			
			
		}	
		
		return $data;
		
	}
	
	
	 
    //========================================================================== 
	//METODO QUE CUENTA LAS CONDICIONES DE UNA PLANIFICACION
	//==========================================================================	
    public function contarCondPln($pln)
	{
		if($pln)
		{
			$sql = "select count(*)as total,condicion_requisito 
			from det_pln_compra where pln_compra_id='$pln' group by condicion_requisito";
		}
		//die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();	
	}
	
}

?>