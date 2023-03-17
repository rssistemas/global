<?php
class empresaModel extends model
{

    public function __construct() {
        parent::__construct();
    }

    // para cargar listado de registros en la vista principal
    public function cargarEmpresa($ref = FALSE)
    {
        if($ref)
        {
        	if(is_int($ref))
			{
				$sql="select * from empresa where id = '$ref' by nombre_empresa";
			}else
				$sql="select * from empresa where nombre_empresa like '%$ref%' order by nombre_empresa";

        }
        else
        {
            $sql="select * from empresa  order by nombre_empresa";
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


    //para incluir un nuevo registro
    public function incluir($datos)
    {
		    $this->_db->start();
        $comentario = (isset($datos['comentario']))?$datos['comentario']:"Sin Comentarios";
            $sql = "insert into empresa("
			      . "fecha_creacion,"
            . "nombre_empresa,"
            . "comentario_empresa,"
            . "direccion_empresa,"
            . "telefono_empresa,"
            . "formato_factura,"
            . "moneda_principal,"
            . "moneda_secundaria,"
            . "tipo_empresa,"
            . "modo_prestaciones,"
            . "rif,"
            . "nit,"
            . "licencia_actividad,"
            . "agente_retencion,"
            . "correo_empresa,"
            . "estatus_empresa"
            . ")values("
			      . "now(),"
            . "'".$datos['nombre']."',"
            . "'".$comentario."',"
            . "'".$datos['direccion']."',"
            . "'".$datos['telefono']."',"
            . "'".$datos['formato_factura']."',"
            . "'".$datos['moneda_principal']."',"
            . "'".$datos['moneda_secundaria']."',"
            . "'".$datos['tipo_empresa']."',"
            . "'".$datos['prestaciones']."',"
            . "'".$datos['rif']."',"
            . "'".$datos['nit']."',"
            . "'".$datos['licencia_actividad']."',"
            . "'".$datos['agente_retencion']."',"
            . "'".$datos['correo']."',"
            . "'1')";
        //die($sql);
        $res = $this->_db->exec($sql);
        if($res)
        {
			       $ult_empresa = $this->_db->lastInsertId();


             $sql = "insert into det_empresa(fec_creado,empresa_id,usuario_id,estatus_det_empresa,condicion_empresa)
             values(now(),'".$ult_empresa."','".$datos['usuario']."','1','1')";
             $res = $this->_db->exec($sql);
             if($res)
             {
                $this->_db->confirm();
                return TRUE;
             }else {
               $error = $this->_db->getError();
               logger::errorLog('TABLE:det_empresa: '.$error['2'],'DB');
               $this->_db->cancel();
               return FALSE;
             }


        }
        else
        {
    			   $error = $this->_db->getError();
    			   logger::errorLog('TABLE:empresa: '.$error['2'],'DB');
             $this->_db->cancel();
             return FALSE;
        }
    }

    //para modificar un registro
    public function modificar($datos)
    {
        $sql = "update empresa set "
            . "nombre_empresa    = '".$datos['nombre']."',"
            . "direccion_empresa = '".$datos['direccion']."',"
            . "telefono_empresa  = '".$datos['telefono']."',"
            . "formato_factura   = '".$datos['formato_factura']."',"
            . "moneda_principal  = '".$datos['moneda_principal']."',"
            . "moneda_secundaria = '".$datos['moneda_secundaria']."',"
            . "tipo_empresa      = '".$datos['tipo_empresa']."',"
            . "modo_prestaciones   = '".$datos['prestaciones']."',"
            . "rif   = '".$datos['rif']."',"
            . "nit   = '".$datos['nit']."',"
            . "licencia_actividad   = '".$datos['licencia_actividad']."',"
            . "agente_retencion     = '".$datos['formato_factura']."',"
            . "correo_empresa       = '".$datos['correo']."' "
            . " where id_empresa    = '".$datos['id']."'";
        //die($sql);
        $res = $this->_db->exec($sql);
        if($res)
        {


            return TRUE;
        }
        else
        {
			       $error =$this->_db->getError();
			       logger::errorLog('TABLE:empresa: '.$error['2'],'DB');
             return FALSE;
        }
    }


    //para eliminar logicamente un registro
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
			$error =$this->_db->getError();
			logger::errorLog($error['2'],'DB');
            return false;
        }
    }




    public function comprobarEmpresa($nombre)
    {
        $sql = "select count(*) as total from empresa where nombre_empresa = '$nombre' ";
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
        $sql="select * from empresa where id_empresa = '$id' ";
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


    //-----------------------------------------------------------------------------------------------------------------------------------------
    //function that search empresas from user
    public function cargarUsuarioEmpresa($usuario,$search=false)
    {
        if($search)
		{
			$sql="select emp.*,demp.condicion_empresa from empresa as emp,det_empresa as demp where estatus_empresa='1' and nombre_empresa like '%$search%'
			and emp.id_empresa=demp.empresa_id	 order by nombre_empresa";
		}
        else{
			$sql="select * from usuario as usu ,det_empresa as demp where usu.estatus_usuario='1' and usu.id_usuario=demp.usuario_id  ";
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

    //-----------------------------------------------------------------------------------------------------------------------------------------
    //function that search empresas from user
    public function cargarUsuarioColaborador($usuario=false,$empresa)
    {
        if($usuario)
		{
			$sql="select * from usuario as usu ,det_empresa as demp where estatus_usuario='1' and alias_usuario like '%$usuario%'
			and usu.id_usuario=demp.usuario_id and usu.empresa_id ='$empresa' and usu.es_colaborador='S' order by alias_usuario";
		}
        else{

			$sql="select * from usuario as usu ,det_empresa as demp where usu.estatus_usuario='1' and usu.id_usuario=demp.usuario_id
            and usu.empresa_id ='$empresa' and usu.es_colaborador='S' ";
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

    //-----------------------------------------------------------------------------------------------------------------------------------------
    //function that search empresas from user
    public function cargarEmpresaUsuario($usuario,$search=false)
    {
        if($search)
		{
			$sql="select emp.*,demp.condicion_empresa from empresa as emp,det_empresa as demp where estatus_empresa='1' and nombre_empresa like '%$search%'
			and emp.id=demp.id order by nombre_empresa";
		}
        else{
			$sql="select emp.*,demp.condicion_empresa from empresa as emp,det_empresa as demp
            where estatus_empresa='1' and emp.id=demp.empresa_id and demp.usuario_id = '$usuario'  order by nombre_empresa";
		 }
        //die($sql);
        $res = $this->_db->sqlQuery($sql);
        if(count($res))
        {
            return $res;
        }
        else
        {
            return array();
        }
    }

     //-----------------------------------------------------------------------------------------------------------------------------------------
    //function that search empresas from user System
    public function cargarTotalEmpresa()
    {

		$sql="select emp.*,0 as condicion_empresa from empresa as emp  where emp.estatus_empresa='1'   order by emp.nombre_empresa";

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



    //----------------------------------------------------------------------------------------------------------------------------------------------
    //function that active empresas form user
    public function activarEmpresaUsuario($id)
    {

        $sql = "update det_empresa set condicion_empresa = '1' where id_det_empresa='$id'";

		//die($sql);
        $res = $this->_db->exec($sql);


	}




    //---------------------------------------------------------------------------------------------------------------------------------------------
    //function that inactive empresa form user
    public function inactivarEmpresaUsuario($id)
    {

		$sql = "update det_empresa set condicion_empresa = '0' where id_det_empresa='$id'";
		//die($sql);
        $res = $this->_db->exec($sql);


	}

}//FIN DE LA CLASE OBJETO DEL MODELO
