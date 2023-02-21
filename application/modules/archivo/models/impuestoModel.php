<?php
class impuestoModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    // para cargar listado de registros en la vista principal
    public function cargarImpuesto($ref = FALSE)
    {
        if($ref)
        {
            $sql="select * from impuesto where nombre_impuesto like '%$ref%' order by nombre_impuesto";
        }
        else
        {
            $sql="select * from impuesto  order by id_impuesto";
        }
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

	//-------------------------------------------------------------------------------------------------
	//METODO QUE PERMITE BUSCAR IMPUESTO EN TABLA MEDIANTE SU NOMBRE O ID
	//------------------------------------------------------------------------------------------------
    public function buscar($valor)
    {
    	if(is_int($valor))
		{
			$sql="select * from impuesto where id_impuesto = '$valor' ";	
			
		}else
			{
				if(is_string($valor))
				{
					$sql="select * from impuesto where nombre_impuesto = '".strtoupper($valor)."'";
				}
			}
        
       // die($sql);
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
        {
            return array();
        }
    }

    // cargar registros activos para combos de seleccion en otras vistas
    public function cargarUnidadUsuario($usuario)
    {
		
        $sql="select uo.*,emp.nombre_empresa as empresa from unidad_operativa as uo, empresa as emp
			where uo.empresa_id = emp.id_empresa and uo.id_unidad_operativa in
			(select ru.unidad_id from relacion_unidad as ru,relacion_deposito as rd where rd.usuario_id = '$usuario'
			and ru.deposito_id = rd.deposito_id )
			order by nombre_unidad_operativa";
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
   
	//------------------------------------------------------------------
    //METODO QUE INSERTA NUEVO REGISTRO EN LA TABLA IMPUESTO
	//------------------------------------------------------------------
    public function insertar($datos)
    {
        $sql = "insert into impuesto("
			. "fecha_creacion,"
            . "nombre_impuesto,"
            . "tasa_impuesto,"
            . "estatus_impuesto,"
            . "descripcion_impuesto,"
            . "tipo_impuesto,"
            . "operador_impuesto,"
            . "comparador_impuesto"
            . ")values("
			. "now(),"
            . "'".strtoupper($datos['nombre'])."',"
            . "'".$datos['porcentaje']."',"
            . "'1',"
            . "'".strtoupper($datos['descripcion'])."',"
            . "'".strtoupper($datos['tipo'])."',"
            . "'".strtoupper($datos['operador'])."',"
            . "'".strtoupper($datos['comparador'])."')";
		
		//die($sql);		
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;               
        }
        else
        {
            return FALSE;
        }
    }
    //-----------------------------------------------------------------------------
    //para modificar un registro
	//-----------------------------------------------------------------------------
	
    public function modificar($datos)
    {
        $sql = "update tipo_gasto set "
            . "nombre_tipo_gasto = '".strtoupper($datos['nombre'])."',"
            . "comentario_tipo_gasto='".strtoupper($datos['comentario'])."',"
            . " where id_deposito = '".$datos['id']."'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

	//------------------------------------------------------------------------------
    //para eliminar logicamente un registro
	//------------------------------------------------------------------------------
    public function desactivar($id)
    {
        $sql = "update deposito set estatus_deposito = '9' where id_deposito = '$id'";
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
    

    public function verificar_existencia($nombre)
    {
        $sql = "select count(*) as total from tipo_gasto"
            . " where nombre_tipo_gasto = '$nombre' ";
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
    
    
    
    
	 //busca los tipo de gasto por descripcion
    public function buscarAutoTgasto($valor)
    {
		$val= array();
		$datos = $this->cargarTgasto(strtoupper($valor));
           
        if(count($datos)>0)
		{		
            foreach ($datos as $valor)
            {
                $val[] = array(
                    "label"=>$valor['nombre_tipo_gasto'],
                    "value"=>array("nombre"=>$valor['nombre_tipo_gasto'],
                                    "codigo"=>$valor['id_tipo_gasto']
                                    ));
            }
            
            
            return $val;
        }else
            return array();
        
    }	
	
}//FIN DE LA CLASE OBJETO DEL MODELO