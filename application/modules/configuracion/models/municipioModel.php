<?php
class municipioModel  extends model
{
    public function __construct() {
        parent::__construct();
    }

    public function cargarMunicipio($ref=false)
    {
        if($ref)
        {
            $sql = "select mun.*,est.descripcion_estado as medida from municipio as mun,estado as est"
                . " where mun.estado_id = est.id_estado and mun.descripcion_municipio like '%$ref%' order by mun.descripcion_municipio";

        }
        else
        {
        $sql = "select mun.*,est.descripcion_estado as medida from municipio as mun,estado as est"
                . " where mun.estado_id = est.id_estado order by mun.descripcion_municipio";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    }
    public function incluir($datos)
    {
        if($datos)
        {
            $sql = "insert into municipio (descripcion_municipio, fecha_creacion, estatus_municipio, estado_id)
             values('".ucfirst($datos['descripcion'])."', now(), 1,".$datos['estado'].")";
            $res = $this->_db->exec($sql);
            if($res)
            {
                    $this->_ultimo_registro = $this->_db->lastInsertId();
                    return TRUE;
            }
            else
                    return false;
        }
    }
    public function modificar($datos)
    {
        $sql = "update municipio set "
                . "descripcion_municipio = '".$datos['descripcion']."' "
                . " where id_municipio = ".$datos['id'];
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
            return false;
    }
    public function buscarMunicipio($descripcion)
    {
        $sql = "select * from municipio as mun where mun.descripcion_municipio = '$descripcion'";
       // die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
        {
            $this->RegLog();
            return FALSE;

        }
    }

    public function buscar($id)
    {
        $sql = "select * from municipio where id_municipio='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();

    }
    public function comprobarMunicipio($est,$mun)
    {
        $sql ="select count(*)as total from municipio as mun where mun.estado_id='$est' and mun.descripcion_municipio = ".$mun;
        $res = $this->_db->query($sql);
        if($res){
			$res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch() ;
		}
        else
            return array("total"=>0);
    }
    public function desactivar($id_ref)
    {
        $sql = "update municipio set estatus_municipio = '9' where id_municipio='$id_ref'";
        $res = $this->_db->exec($sql);
        if(!$res)
            return FALSE;
        else
            return TRUE;
    }
	//CARGA MUNICIPIOS ASOCIADOS A UN ESTADO
	public function buscarMunicipios($estado=FALSE)
    {
      if($estado)
      {
        $sql = "select * from municipio as mun"
        . " where mun.estado_id = '$estado' and mun.estatus_municipio='1' "
        . "order by descripcion_municipio";
      }else {
        $sql = "select * from municipio as mun"
        . " where  mun.estatus_municipio='1' order by descripcion_municipio";
      }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
            return array();
    }
}
