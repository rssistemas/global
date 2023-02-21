<?php
class depositoModel extends model
{
    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarDeposito($empresa,$ref = FALSE)
    {
        if($ref)
        {
            $sql="select dep.*,uo.nombre_unidad_operativa,td.nombre_tipo_deposito as tipo"
            ." from deposito as dep, unidad_operativa as uo,tipodeposito as td"
            ." where   dep.unidad_operativa_id = uo.id_unidad_operativa and td.id_tipo_deposito = dep.tipo_deposito
            and dep.nombre_deposito like '%$ref%' and dep.empresa_id='$empresa' order by dep.nombre_deposito";
        }
        else
        {
            $sql="select dep.*,uo.nombre_unidad_operativa,td.nombre_tipo_deposito  as tipo "
            ." from deposito as dep,  unidad_operativa as uo,tipodeposito as td
              where  dep.unidad_operativa_id = uo.id_unidad_operativa and dep.empresa_id='$empresa' and td.id_tipo_deposito = dep.tipo_deposito
              order by dep.nombre_deposito";
        }
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

    // cargar registros activos para combos de seleccion en otras vistas
    public function cargarDeposito2()
    {
        $sql="select dep.*, tip_dep.nombre_tipo_deposito as tipo "
        ." from deposito as dep, tipo_deposito as tip_dep "
        ." where dep.tipo_deposito=tip_dep.id_tipo_deposito"
        . " and dep.estatus_deposito= '1' "
        . " order by dep.nombre_deposito";
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

    //para incluir un nuevo registro
    public function incluir($datos)
    {
        $sql = "insert into deposito("
            . "nombre_deposito,"
            . "ubicacion_deposito,"
            . "unidad_almacenaje,"
            . "estatus_deposito,"
            . "tipo_deposito,"
            . "telefono_deposito,"
            . "fax_deposito,"
            . "fecha_creacion,"
            . "sector_id,"
            . "unidad_operativa_id,"
            . "capacidad_max,"
            . "capacidad_min,"
            . "estado_id,"
            . "municipio_id,"
            . "parroquia_id"
            . ")values("
            . "'".strtoupper($datos['nombre'])."',"
            . "'".strtoupper($datos['ubicacion'])."',"
            . "'".$datos['medida']."',"
            . "'1',"
            . "'".$datos['tipo']."',"
            . "'".$datos['telefono']."',"
            . "'".$datos['fax']."',"
            . "now(),"
            . "'".$datos['sector']."',"
            . "'".$datos['unidad']."',"
            . "'".$datos['max']."',"
            . "'".$datos['min']."',"
            . "'".$datos['estado']."',"
            . "'".$datos['municipio']."',"
            . "'".$datos['parroquia']."'"
            . ")";

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

    //para modificar un registro
    public function modificar($datos)
    {
        $sql = "update deposito set "
            . "nombre_deposito = '".strtoupper($datos['nombre'])."',"
            . "ubicacion_deposito='".strtoupper($datos['ubicacion'])."',"
            . "unidad_almacenaje='".$datos['medida']."',"
            . "tipo_deposito = '".$datos['tipo']."',"
            . "telefono_deposito = '".$datos['telefono']."',"
            . "fax_deposito = '".$datos['fax']."', "
            . "sector_id = '".$datos['sector']."', "
            . "estado_id = '".$datos['estado']."', "
            . "municipio_id = '".$datos['municipio']."', "
            . "parroquia_id = '".$datos['parroquia']."', "
            . "capacidad_max = '".$datos['max']."', "
            . "capacidad_min = '".$datos['min']."' "
            . " where id_deposito = '".$datos['id']."'";

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


    //para eliminar logicamente un registro
    public function estatusDeposito($id,$est)
    {
        $sql = "update deposito set estatus_deposito = '$est'"
                . " where id_deposito = '$id'";
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

    //VERIFICAR UTILIZACION
    public function verificar_uso($cod)
    {
        $sql ="select count(*) as total "
        . "from solicitud, relacion_deposito, despacho, stock, autorizacion"
        . " where solicitud.origen_solicitud='$cod' or solicitud.destino_solicitud='$cod'"
        . " or relacion_deposito.deposito_id='$cod'"
        . " or despacho.deposito_origen='$cod' or despacho.deposito_destino='$cod'"
        . " or stock.deposito_id='$cod' or autorizacion.deposito_despacho='$cod'";
        die($sql);
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
        $sql="select * from deposito as dep, tipo_deposito as tipo, sector as sec "
            . ", parroquia as par, municipio as mun, estado as est"
            . " where tipo.id_tipo_deposito = dep.tipo_deposito"
            . " and dep.id_deposito = '$id' and dep.sector_id=sec.id_sector and "
            . " sec.parroquia_id=par.id_parroquia and par.municipio_id=mun.id_municipio and"
            . " mun.estado_id=est.id_estado";
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
    //para consultar un registro por el id
    public function buscarDepId($id)
    {
        $sql="select * from deposito as dep where  dep.id_deposito = '$id'";
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
    //METODO QUE CARGA LA RELACION DE USUARIOS DE UN DEPOSITO PASADO POR PARAMETRO
    //==========================================================================
    public function relacionDeposito($deposito)
    {
        $sql = "select rd.*,per.pri_nombre_persona,per.seg_nombre_persona,per.pri_apellido_persona,per.seg_apellido_persona from relacion_deposito as rd,usuario as usu,persona as per "
				." where rd.deposito_id = '$deposito' and rd.usuario_id = usu.id_usuario and per.id_persona = usu.persona_id ";
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
    //==========================================================================
    //METODO QUE CARGA LA RELACION DE DEPOSITO ACTIVOS DE UN TRABAJADOR
    //==========================================================================
    public function relacionDepositoAct($unidad)
    {

	$trb = session::get('id_usuario');

        $sql = "select * from relacion_deposito as rd,deposito as d,relacion_unidad ru where rd.usuario_id = '$trb' "
                . " and rd.deposito_id = d.id_deposito and rd.estatus_relacion = '1'
				and rd.deposito_id = ru.deposito_id and ru.unidad_id='$unidad'";
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
    //==========================================================================
    //METODO QUE AGREGA UNA RELACION DE DEPOSITO TRABAJAOR
    //==========================================================================
    public function incluirRelacionDeposito($datos)
    {
        $sql = "insert into relacion_deposito("
                . "deposito_id,"
                . "trabajador_id,"
                . "estatus_relacion,"
				. "fecha_creacion"
                . ")values("
                . "'".$datos['deposito']."',"
                . "'".$datos['trabajador']."',"
                . "'1',now()"
                . ")";
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
    //==========================================================================
    //METODO QUE ACTIVA UNA RELACION DEPOSITO-USUARIO
    //==========================================================================
    public function activarRelacionDeposito($id)
    {
        $sql = "update relacion_deposito set estatus_relacion = '1' where id_relacion = '$id'";
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
    //==========================================================================
    //METODO QUE DESACTIVA UNA RELACION DEPOSITO-USUARIO
    //==========================================================================
    public function desactivarRelacionDeposito($id)
    {
        $sql = "update relacion_deposito set estatus_relacion = '9' where id_relacion = '$id'";
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
    //==========================================================================
    //CARGAR LOS DEPOSITO QUE NO HAN SIDO ASIGNADOS AL TRABAJADOR PASADO por PARAMETRO
    //==========================================================================
    public function noUsuarioDeposito($deposito)
    {
        $sql = "select * from usuario as usu,persona as per  where per.id_persona = usu.persona_id and usu.id_usuario  not in"
                . " (select usuario_id from relacion_deposito where deposito_id = '$deposito')";
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

	//==========================================================================
    //CARGAR LOS DEPOSITO QUE NO HAN SIDO ASIGNADOS AL TRABAJADOR PASADO
    //==========================================================================
    public function noTrabajadorDeposito($deposito)
    {
        $sql = "select * from trabajador as trb,persona as per  where per.id_persona = trb.persona_id and trb.id_trabajador  not in "
                . " (select trabajador_id from relacion_deposito where deposito_id = '$deposito')";
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

    //==========================================================================
}
