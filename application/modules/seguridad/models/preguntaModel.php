<?php
class preguntaModel  extends model
{
    public function __construct() {
        parent::__construct();
    }

    public function cargarPregunta($ref=FALSE,$emp)
    {
      if($emp)
      {
          if($ref)
          {
              $sql = "select * from pregunta where estatus_pregunta='1' and empresa_id = '$emp' "
              . "and pregunta like '%$ref%' order by pregunta ASC";
          }
          else
          {
              $sql = "select * from pregunta where estatus_pregunta='1' and empresa_id = '$emp' order by pregunta ASC";
          }
          //die($sql);
          $res = $this->_db->query($sql);
          $res->setFetchMode(PDO::FETCH_ASSOC);
          return $res->fetchAll();
      }else {
        return FALSE;
      }
    }
    public function insertar($datos)
    {
        if($datos)
        {
           $sql="insert into pregunta(fecha_creacion,pregunta,estatus_pregunta,empresa_id)"
            . "values(now(),'".$datos['descripcion']."','1','".$datos['empresa']."')";

           $res = $this->_db->exec($sql);
           if(!$res)
           {
               return false;
           }
           return true;
        }
    }
    public function modificar($datos)
    {
        if($datos)
        {
            $sql = "update pregunta set pregunta = '". $datos['descripcion']."'"
                    . " where id_pregunta = '".$datos['id']."'";

           $res = $this->_db->exec($sql);
           if(!$res)
           {
               return false;
           }
           return true;
        }
    }
    public function buscar($id)
    {
        $sql = "select * from pregunta where id_pregunta = '$id'";
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }

        return array();
    }
    public function desactivar($id)
    {
        if($id)
        {
            $sql = "update pregunta set estatus_pregunta = '9'"
                    . " where id_pregunta = '".$id."'";

           $res = $this->_db->exec($sql);
           if(!$res)
           {
               return false;
           }
           return true;
        }
    }
    //para verificar que no se repita un mismo registro
    public function verificar_existencia($ref)
    {
        $sql = "select count(*)as total from pregunta where pregunta = '$ref'";
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
        $sql =" select count(*) as total from usuariopregunta as usu"
            . " where usu.pregunta_id=".$cod;
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


}
