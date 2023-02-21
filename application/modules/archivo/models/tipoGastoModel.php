<?php
class tipoGastoModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    // para cargar listado de registros en la vista principal
    public function cargarTgasto($ref = FALSE)
    {
        if($ref)
        {
            $sql="select * from tipogasto where nombre_tipo_gasto like '%$ref%' order by nombre_tipo_gasto";
        }
        else
        {
            $sql="select * from tipogasto  order by nombre_tipo_gasto";
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
    //para incluir un nuevo registro DE UNIDAD OPERATIVA
	//------------------------------------------------------------------
    public function incluir($datos)
    {
        $sql = "insert into tipogasto("
            . "fecha_creacion,"
            . "nombre_tipo_gasto,"
            . "condicion_tipo_gasto,"
            . "estatus_tipo_gasto,"
            . "comentario_tipo_gasto"
            . ")values("
            . "now(),"
            . "'".strtoupper($datos['nombre'])."',"
            . "'ACTIVO',"
            . "'1',"
            . "'".strtoupper($datos['comentario'])."')";
		
		//die($sql);		
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;               
        }
        else
        {
            $this->regLog();
            return FALSE;
        }
    }
    //-----------------------------------------------------------------------------
    //para modificar un registro
	//-----------------------------------------------------------------------------
	
    public function modificar($datos)
    {
        $sql = "update tipogasto set "
            . "nombre_tipo_gasto = '".strtoupper($datos['nombre'])."',"
            . "comentario_tipo_gasto='".strtoupper($datos['comentario'])."',"
            . " where id_tipo_gasto = '".$datos['id']."'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            $this->regLog();
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
        $sql = "select count(*) as total from tipogasto"
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
    
    
    //para consultar un registro por el id
    public function buscar($id)
    {
        $sql="select * from tipogasto where id_tipo_gasto = '$id' ";
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