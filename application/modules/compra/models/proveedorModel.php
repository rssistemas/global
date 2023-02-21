<?php
class proveedorModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    //--------------------------------------------------------------------------
    // METODO QUE CARGA DATOS DE LA TABLA PROVEEDOR POR ID O TODOS LOS DATOS
    //--------------------------------------------------------------------------
    public function cargarProveedor($item = FALSE)
    {
        if($item){
       		if(is_int($item))
        	 	$sql = "select * from proveedor where id_proveedor = '$item'";
			else
				$sql = "select * from proveedor where razon_social_proveedor  like '%$item%'order by razon_social_proveedor ";				 
		}else
            $sql = "select * from proveedor order by razon_social_proveedor ";
        
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
    
    public function buscarProvDesc($valor)
    {        
        $val = array();
		$sql = "select * from proveedor where razon_social_proveedor like '%$valor%'";
        //die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);            
            $datos = $res->fetchAll();
            foreach ($datos as $valor)
            {
                $val[] = array(
                    "label"=>  ucfirst($valor['razon_social_proveedor']),
                    "value"=>array("nombre"=>  ucfirst($valor['razon_social_proveedor']),
                                    "id"=>$valor['id_proveedor'],"correo"=>$valor['correo_proveedor'],
                                    "dir"=>$valor['direccion_fiscal_proveedor']));
            }
            
            return $val;
            
        }else
            return array();
    }
            
    public function buscarProveedor($item = FALSE)
    {
        if(is_int($item))
         $sql = "select * from proveedor where id_proveedor = '$item'";
        else
            $sql = "select * from proveedor where rif_proveedor = '$item'";
        
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
	
	//----------------------------------------------------------------------------------------
	//METODO QUE BUSCA PROVEEDOR POR SU RIF
	//----------------------------------------------------------------------------------------
	public function buscarRifProveedor($valor)
    {
        
        $sql = "select * from proveedor where rif_proveedor = '$valor'";             
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
    // METODO QUE COMPRUEBA PROVEEDOR POR NOMBRE O RAZON SOCIAL
    //==========================================================================
    public function comprobarProveedor($proveedor)
    {
        $sql = "select count(*)as total from proveedor "
        . "where trim(razon_social) = '".trim($proveedor)."'";
        //die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $res->fetch();
            if(count($datos)>0)
                return $datos;
            else
                return array("total"=>0);
        }
        else
        {
            return array('total'=>0);
        }
    }
    //==========================================================================
	// METODO QUE CUENTA EXISTENCIA DE PROVEEDOR POR SU RIF
	//==========================================================================
	public function verificar_existencia($tipo,$rif)
    {
		$rif = strtoupper($tipo).$rif;
        $sql = "select count(*) as total from proveedor where rif_proveedor = '$rif' ";
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
	
	//---------------------------------------------------------------------------
	//para incluir un nuevo registro DE COMPRA
	//---------------------------------------------------------------------------
    public function incluir($datos)
    {
		$rif = strtoupper($datos['nacionalidad']).$datos['cedula'];
        $sql = "insert into proveedor("
            . "rif_proveedor,"
            . "razon_social_proveedor,"
            . "direccion_fiscal_proveedor,"
            . "correo_proveedor,"
            . "telefono_proveedor,"			
            . "nombre_contacto_proveedor,"
            . "telefono_contacto_proveedor,"
            . "fecha_creacion,"
            . "estatus_proveedor,"
            . "comentario_proveedor,"
            . "condicion_proveedor,"
			. "tipo_proveedor"
            . ")values("
            . "'".$rif."',"
            . "'".$datos['nom_pro']."',"
            . "'".$datos['direccion']."',"
            . "'".$datos['correo_pro']."',"
            . "'".$datos['telf_pro']."',"
            . "'".$datos['nom_con']."',"
            . "'".$datos['telf_con']."',"
            . "now(), "
            . "'1',"
            . "'".$datos['comentario']."',"
            . "'ACTIVO',"
			. "'".$datos['tipo']."')";
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
	
    //--------------------------------------------------------------------------------------------
    //METODO DE CARGA DE DOCUMENTOS DE RECEPCION DEL PROVEEDOR
    //--------------------------------------------------------------------------------------------
    public function cargarDocumentoRecProveedor($prv,$doc,$unidad)
    {
        $sql ="select rec.nro_doc_ori,id_recepcion  from recepcion as rec where rec.proveedor_id = '$prv' 
        and tipo_doc_ori = '$doc' and unidad_operativa = '$unidad' ";

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
    
    //-------------------------------------------------------------------------------------------
    //METODO DE CONTEO DE DOCUMENTO  DE COMPRA DE PROVEEDOR
    //-------------------------------------------------------------------------------------------
    public function cargarDocumentoCpraProveedor($prv,$tdoc,$ndoc)
    {
            $sql = "select count(*) as total from gasto where tipo_doc_ori ='$tdoc' 
                    and nro_doc_ori = '$ndoc' and proveedor_id='$prv'";
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
    //---------------------------------------------------------------------------------------------
    //METODO QUE CARGA COMPRAS DE PROVEEDORES QUE COINCIDAN CON EL PARAMETRO PASADO
    //---------------------------------------------------------------------------------------------
    public function buscarCpraProveedor($prv)
    {

            $sql ="select cpra.*,prv.razon_social_proveedor  from compra as cpra,proveedor as prv where 
                       cpra.estatus_compra='1' and cpra.condicion_compra !='CERRADA' and prv.id_proveedor = cpra.proveedor_id and
                       cpra.proveedor_id in(select id_proveedor from proveedor where razon_social_proveedor like '%$prv%') ";

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
	
}
