<?php
class personaModel extends model
{
    private $_ultimo_registro;
    
    public function __construct() {
        parent::__construct();
        $this->_ultimo_registro = 0;
    }
    
    public function ult_persona_reg()
    {
        return $this->_ultimo_registro;
    }        

     //para incluir un nuevo registro
    public function incluir($datos)
    {           
        $sql = "insert into persona( nacionalidad_persona, cedula_persona,
                pri_nombre_persona, seg_nombre_persona, pri_apellido_persona,
                seg_apellido_persona, fecha_nac_persona, celular_persona,
                telefono_persona, licencia_persona, direccion_persona,
                estado_civil_persona, lugar_nac_persona, sexo_persona,
                sector_id,estado_id,municipio_id,parroquia_id,correo_electronico_persona) values(
                '".$datos['nacionalidad']."', '".$datos['cedula']."',
                '".$datos['pri_nom']."', '".$datos['seg_nom']."',
                '".$datos['pri_ape']."', '".$datos['seg_ape']."',
                '".$datos['fecha_nac']."', '".$datos['celular']."', '".$datos['local']."',
                '".$datos['licencia']."', '".$datos['direccion']."',
                '".$datos['estado_civil']."', '".$datos['lugar_nac']."',
                '".$datos['sexo']."', '".$datos['sector']."','".$datos['estado']."',
                '".$datos['municipio']."','".$datos['parroquia']."','".$datos['correo']."')";
	
	//die($sql);			
	if($this->_db->exec($sql))
	{
            $this->_ultimo_registro = $this->_db->lastInsertId();	
            return TRUE;	
	}
        else
        {
			$error =$this->_db->getError();
            logger::errorLog($error['2'],'DB');
            return false;
        }
    }
    
    //para modificar un registro
    public function modificar($datos)
    {
        $sql = "update persona set "
            . "pri_nombre_persona= '".$datos['pri_nom']."', "
            . "seg_nombre_persona= '".$datos['seg_nom']."', "
            . "pri_apellido_persona= '".$datos['pri_ape']."', "
            . "seg_apellido_persona= '".$datos['seg_ape']."', "
            . "direccion_persona= '".$datos['direccion']."', "
            . "telefono_persona= '".$datos['local']."', "
            . "celular_persona= '".$datos['celular']."', "
            . "fecha_nac_persona= '".$datos['fecha_nac']."', "
            . "lugar_nac_persona= '".$datos['lugar_nac']."', "
            . "sexo_persona= '".$datos['sexo']."', "
            . "licencia_persona= '".$datos['licencia']."', "
            . "estado_civil_persona= '".$datos['estado_civil']."', "
            . "sector_id= ".$datos['sector']." "
            . " where id_persona = ".$datos['id'];
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

    //para verificar que no se repita un mismo registro
    public function verificar_existencia($tipo,$id)
    {
        $sql = "select count(*)as total from persona "
            . " where nacionalidad_persona = '$tipo' "
            . " and cedula_persona = '$id'";
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
    public function buscarPersona($cedula, $tipo)
    {
        $sql = "select * from persona as pers "
            . "where pers.cedula_persona = '$cedula'"
            . " and pers.nacionalidad_persona = '$tipo'";
        $res = $this->_db->query($sql);
        if($res)
        {
             $res->setFetchMode(PDO::FETCH_ASSOC);
             return $res->fetch();
        }
        else
        {
            return FALSE;
        }
    }

}//FIN DE LA CLASE OBJETO DEL MODELO
