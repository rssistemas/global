<?php
class unidadModel extends model
{
    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarUnidad($ref = FALSE)
    {
        if($ref)
        {
            if(is_int($ref))
            {
                $sql="select * from unidad_operativa as uo, empresa as emp  where
			             uo.empresa_id = emp.id_empresa and uo.id_unidad='$ref' order by nombre_unidad_operativa";
            } else {
                $sql="select * from unidad_operativa as uo, empresa as emp  where
		              uo.empresa_id = emp.id_empresa and	nombre_unidad_operativa like '%$ref%' order by nombre_unidad_operativa";
            }

        }
        else
        {
            $sql="select uo.*,emp.nombre_empresa as empresa from unidad_operativa as uo, empresa as emp
			         where uo.empresa_id = emp.id_empresa order by nombre_unidad_operativa";
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
    //-----------------------------------------------------------------------------------
	//METODO QUE CARGA TODAS LAS RELACIONES DE DEPOSITO UNIDAD OPERATIVA
	//-----------------------------------------------------------------------------------
    public function cargarDepositoUnidad($unidad)
    {

        $sql="select dep.*,ru.*  from unidad_operativa as uo, deposito as dep,relacion_unidad as ru
			where uo.id_unidad_operativa = ru.unidad_id and ru.deposito_id = dep.id_deposito and
			ru.unidad_id='$unidad' group by dep.id_deposito	order by nombre_deposito";
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
	//--------------------------------------------------------------------------------
	//METODO QUE CARGA LOS DEPOSITOS QUE NO ESTAN ASIGNADOS A LA UNIDAD OPERATIVA
	//--------------------------------------------------------------------------------
	public function noDepositoUnidad($unidad)
    {

        $sql="select  dep.* from  deposito as dep where  dep.estatus_deposito ='1'
		and id_deposito not in(select deposito_id from relacion_unidad where unidad_id ='$unidad') ";
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
        $sql = "insert into unidad_operativa("
			      . "fecha_creacion,"
            . "nombre_unidad_operativa,"
            . "direccion_unidad_operativa,"
            . "telefono_unidad_operativa,"
            . "estado_id,"
            . "municipio_id,"
            . "sector_id,"
            . "estatus_unidad_operativa,"
            . "condicion_unidad_operativa,"
            . "comentario_unidad_operativa,"
            . "empresa_id,"
			      . "serie_factura,"
			      . "fecha_trabajo"
            . ")values("
			      . "now(),"
            . "'".strtoupper($datos['nombre'])."',"
            . "'".strtoupper($datos['direccion'])."',"
            . "'".$datos['telefono']."',"
            . "'".$datos['estado']."',"
            . "'".$datos['municipio']."',"
            . "'".$datos['sector']."',"
            . "'1',"
            . "'".strtoupper($datos['condicion'])."',"
            . "'".strtoupper($datos['comentario'])."',"
            . "'".$datos['empresa']."',"
			      . "'".strtoupper($datos['serie'])."',"
			      . "'".$datos['fecha']."')";


        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            $error = $this->_db->getError();
            logger::errorLog('TABLE:UnidadOperativa: '.$error['2'],'DB');
            return FALSE;
        }
    }
    //-----------------------------------------------------------------------------
    //para modificar un registro
	//-----------------------------------------------------------------------------

    public function modificar($datos)
    {
        $sql = "update unidad_operativa set "
            . "nombre_unidad_operativa = '".$datos['nombre']."',"
            . "direccion_unidad_operativa='".$datos['direccion']."',"
            . "telefono_unidad_operativa = '".$datos['telefono']."',"
            . "estado_id = '".$datos['estado']."',"
            . "municipio_id = '".$datos['municipio']."',"
            . "sector_id = '".$datos['sector']."',"
            . "condicion_unidad_operativa = '".$datos['condicion']."', "
            . "comentario_unidad_operativa = '".$datos['comentario']."', "
            . "empresa_id = '".$datos['empresa']."',"
            . "serie_factura = '".$datos['serie']."' "
            . " where id_unidad_operativa = '".$datos['id']."'";
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            $error = $this->_db->getError();
            logger::errorLog('TABLE:UnidadOperativa: '.$error['2'],'DB');
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


    public function verificar_existencia($tipo,$nombre)
    {
        $sql = "select count(*) as total from deposito"
            . " where nombre_deposito = '$nombre' and"
            . " tipo_deposito= '". $tipo ."'";
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
        $sql="select * from unidad_operativa as uo where  uo.id_unidad_operativa = '$id' ";
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
    //==========================================================================
    //METODO QUE CARGA LA RELACION DE DEPOSITO DE UN TRABAJADOR
    //==========================================================================
    public function relacionUnidad($trabajador)
    {
        $sql = "select * from relacion_deposito where trabajador_id = '$trabajador' and estatus_relacion = '1'";
        //die($sql);
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
    //==========================================================================
    //CARGA LOS DEPOSITOS DIFERENTES AL PASADO POR PARAMETRO
    //==========================================================================
    public function buscarDiferente($deposito)
    {
        $sql = "select  id_deposito,nombre_deposito from deposito "
                . "where id_deposito != '$deposito' and estatus_deposito = '1'";
        //die($sql);
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
    //--------------------------------------------------------------------------
	//METODO QUE INCLUYE NUEVAS RELACIONES DE UNIDAD OPERATIVA
	//--------------------------------------------------------------------------
    public function incluirRelacionUnidad($datos)
	{

		$sql="insert into relacion_unidad(
			unidad_id,deposito_id,fecha_creacion,estatus_relacion)
			values('".$datos['unidad']."','".$datos['deposito']."',now(),'1')";

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
    //para eliminar logicamente un registro de relacion de unidad operativa y deposito
	//------------------------------------------------------------------------------
    public function desactivarRelacion($id)
    {
        $sql = "update relacion_unidad set estatus_relacion = '9' where id_relacion_unidad = '$id'";
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

    public function activarRelacion($id)
    {
        $sql = "update relacion_unidad set estatus_relacion = '1' where id_relacion_unidad = '$id'";
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

}//FIN DE LA CLASE OBJETO DEL MODELO
