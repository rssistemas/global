<?php
class sectorModel  extends model
{
    public function __construct() {
        parent::__construct();
    }

    public function cargarSector($ref=FALSE)
    {
        if($ref)
        {
            $sql = "select sec.*, par.descripcion_parroquia as medida from sector as sec, parroquia as par"
            . " where sec.parroquia_id = par.id_parroquia and sec.descripcion_sector like '%$ref%' order by sec.descripcion_sector";
        }
        else
        {
            $sql = "select sec.*, mun.descripcion_municipio as medida from sector as sec, municipio as mun"
            . " where sec.municipio_id = mun.id_municipio order by sec.descripcion_sector";
        }
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    }
    public function insertar($datos)
    {
        if($datos)
        {
            $sql = "insert into sector(descripcion_sector, fecha_creacion, estatus_sector, municipio_id,empresa_id)
             values('".ucfirst($datos['descripcion'])."', now(), '1',".$datos['municipio'].",".$datos['empresa'].")";
            $res = $this->_db->exec($sql);
            if($res)
            {
                    $this->_ultimo_registro = $this->_db->lastInsertId();

                    return TRUE;
            }
            else
                $error = $this->_db->getError();
                logger::errorLog('TABLE:sector: '.$error['2'],'DB');
                return false;
        }
    }
    public function modificar($datos)
    {
        $sql = "update sector set "
                . "descripcion_sector = '".$datos['descripcion']."', "
                . "municipio_id = '".$datos['municipio']."' "
                . " where id_sector = ".$datos['id'];
        //die($sql);        
        $res = $this->_db->exec($sql);
        if($res)
        {
            return TRUE;
        }
        else
        {
            $error = $this->_db->getError();
            logger::errorLog('TABLE:sector: '.$error['2'],'DB');
            return false;
        }
    }
    public function buscarSector($descripcion)
    {
        $sql = "select * from sector as sec where sec.descripcion_sector = '$descripcion'";
       // die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }
        else
            return FALSE;
    }

    public function buscar($id)
    {
        $sql = "select * from sector where id_sector='$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
    }
    public function desactivar($id_ref)
    {
        $sql = "update sector set estatus_sector = '9' where id_sector='$id_ref'";
        $res = $this->_db->exec($sql);
        if(!$res)
            return FALSE;
        else
            return TRUE;
    }

	 //--METODO PARA CARGAR SECTORES CORRESPONDIENTES A UNA PARROQUIA
    public function buscarSectores($parroquia)
    {
        $sql = "select * from sector as sect where sect.municipio_id = '$parroquia' order by descripcion_sector";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }
        else
            return array();
    }

	public function buscarLocalidad($sector)
	{
		$sql = "select sec.id_sector,descripcion_sector,mun.id_municipio,mun.estado_id
		from sector as sec,municipio as mun  where sec.id_sector = '$sector' and
		 mun.id_municipio = sec.municipio_id";

		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();

	}

	public function comprobarSector($mun,$sec)
    {
        $sql ="select count(*)as total from sector as sec where sec.municipio_id='$mun' and sec.descripcion_sector = ".$sec;
        $res = $this->_db->query($sql);
        if($res){
			$res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch() ;
		}
        else
            return array("total"=>0);
    }

}
