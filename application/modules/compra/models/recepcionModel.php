<?php
class recepcionModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    //==========================================================================
    //CARGA LAS RECEPCIONES DEL PROCESO INTERNO ENTRE DEPOSITOS
    //==========================================================================
    public function cargarRecepcionInterna($almacen = FALSE,$id = FALSE)
    {
        $trabajador = session::get('trabajador');
        if($almacen)
        {    
            if($id)
                //$sql = "select * from recepcion where id_recepcion = '$id' ";
                $sql = "select rec.*,prv.razon_social,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito and rec.id_recepcion = '$id'"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rec.deposito_id = '$almacen' and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='1'  order by fecha_recepcion";
            else
                $sql = "select rec.*,prv.razon_social,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rec.deposito_id = '$almacen' and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='1'  order by fecha_recepcion";
        }else
        {
            $sql = "select rec.*,dep1.nombre_deposito as deposito_recepcion,dep2.nombre_deposito as deposito_despacho,tdoc.ncorto,tdoc.nombre_tipo_documento"
                    . " from recepcion as rec join deposito as dep1 on rec.deposito_id = dep1.id_deposito join despacho as dsp"
                    . " on rec.nro_doc_ori = dsp.id_despacho join deposito as dep2 on dsp.deposito_origen = dep2.id_deposito"
                    . " join tipo_documento as tdoc on rec.tipo_doc_ori = tdoc.id_tipo_documento join  relacion_deposito as rd "
                    . " on rd.deposito_id = rec.deposito_id where rd.estatus_relacion = '1' and rd.trabajador_id = '$trabajador' "
                    . " and rec.tipo_recepcion='1' order by fecha_recepcion";
        }
        
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
	
	//==========================================================================
    //CARGA LAS RECEPCIONES DEL PROCESO INTERNO ENTRE DEPOSITOS POR RANGO DE FECHA
    //==========================================================================
    public function cargarRecepcionInternaRango($rango = false,$almacen = FALSE)
    {
        $trabajador = session::get('trabajador');
        if($almacen)
        {    
            if($rango){
				$inicio = $rango['inicio'];
				$fin = $rango['fin'];
                $sql = "select rec.*,dep1.nombre_deposito as deposito_recepcion,dep2.nombre_deposito as deposito_despacho,tdoc.ncorto,tdoc.nombre_tipo_documento"
                    . " from recepcion as rec join deposito as dep1 on rec.deposito_id = dep1.id_deposito join despacho as dsp"
                    . " on rec.nro_doc_ori = dsp.id_despacho join deposito as dep2 on dsp.deposito_origen = dep2.id_deposito"
                    . " join tipo_documento as tdoc on rec.tipo_doc_ori = tdoc.id_tipo_documento join  relacion_deposito as rd "
                    . " on rd.deposito_id = rec.deposito_id where rd.estatus_relacion = '1' and rd.trabajador_id = '$trabajador' "
                    . " and rec.tipo_recepcion='1' and rec.deposito_id = '".$almacen."' and rec.fecha_recepcion between '".$inicio."' and '".$fin."'   order by fecha_recepcion";
            }else{
                $sql = "select rec.*,dep1.nombre_deposito as deposito_recepcion,dep2.nombre_deposito as deposito_despacho,tdoc.ncorto,tdoc.nombre_tipo_documento"
                    . " from recepcion as rec join deposito as dep1 on rec.deposito_id = dep1.id_deposito join despacho as dsp"
                    . " on rec.nro_doc_ori = dsp.id_despacho join deposito as dep2 on dsp.deposito_origen = dep2.id_deposito"
                    . " join tipo_documento as tdoc on rec.tipo_doc_ori = tdoc.id_tipo_documento join  relacion_deposito as rd "
                    . " on rd.deposito_id = rec.deposito_id where rd.estatus_relacion = '1' and rd.trabajador_id = '$trabajador' "
                    . " and rec.tipo_recepcion='1' and rec.deposito_id = '".$almacen."'   order by fecha_recepcion";
			}
        }else
        {
            if($rango){
				$inicio = $rango['inicio'];
				$fin = $rango['fin'];
                $sql = "select rec.*,dep1.nombre_deposito as deposito_recepcion,dep2.nombre_deposito as deposito_despacho,tdoc.ncorto,tdoc.nombre_tipo_documento"
                    . " from recepcion as rec join deposito as dep1 on rec.deposito_id = dep1.id_deposito join despacho as dsp"
                    . " on rec.nro_doc_ori = dsp.id_despacho join deposito as dep2 on dsp.deposito_origen = dep2.id_deposito"
                    . " join tipo_documento as tdoc on rec.tipo_doc_ori = tdoc.id_tipo_documento join  relacion_deposito as rd "
                    . " on rd.deposito_id = rec.deposito_id where rd.estatus_relacion = '1' and rd.trabajador_id = '$trabajador' "
                    . " and rec.tipo_recepcion='1'  and rec.fecha_recepcion between '".$inicio."' and '".$fin."'   order by fecha_recepcion";
            }else{
                $sql = "select rec.*,dep1.nombre_deposito as deposito_recepcion,dep2.nombre_deposito as deposito_despacho,tdoc.ncorto,tdoc.nombre_tipo_documento"
                    . " from recepcion as rec join deposito as dep1 on rec.deposito_id = dep1.id_deposito join despacho as dsp"
                    . " on rec.nro_doc_ori = dsp.id_despacho join deposito as dep2 on dsp.deposito_origen = dep2.id_deposito"
                    . " join tipo_documento as tdoc on rec.tipo_doc_ori = tdoc.id_tipo_documento join  relacion_deposito as rd "
                    . " on rd.deposito_id = rec.deposito_id where rd.estatus_relacion = '1' and rd.trabajador_id = '$trabajador' "
                    . " and rec.tipo_recepcion='1'   order by fecha_recepcion";
			}
        }
        
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
	
	
	
    //==========================================================================
    //CARGA LAS RECEPCIONES DE PRODUCTOS DE PROVEEDORES(PROCESO EXTERNO  )
    //==========================================================================
    public function cargarRecepcionProveedor($almacen = FALSE,$id = FALSE)
    {
         $trabajador = session::get('id_usuario');
        if($almacen)
        {    
            if($id)
                //$sql = "select * from recepcion where id_recepcion = '$id' ";
                $sql = "select rec.*,prv.razon_social_proveedor,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.origen_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito and rec.id_recepcion = '$id'"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rec.deposito_id = '$almacen' and rd.usuario_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='PROVEEDORES'  order by fecha_recepcion";
            else
                $sql = "select rec.*,prv.razon_social_proveedor,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rec.deposito_id = '$almacen' and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='2' order by fecha_recepcion";
        }else
        {
            $sql = "select rec.*,prv.razon_social_proveedor,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.origen_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rd.usuario_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='PROVEEDORES' order by fecha_recepcion";
        }
        
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
	
	//==========================================================================
    //CARGA LAS RECEPCIONES DE PRODUCTOS DE PROVEEDORES(PROCESO EXTERNO  )por rango
    //==========================================================================
    public function cargarRecepcionProveedorRango($rango = FALSE,$almacen = FALSE)
    {
         $trabajador = session::get('trabajador');
        if($almacen)
        {    
            if($rango){
				$inicio = $rango['inicio'];
				$fin = $rango['fin'];            
                $sql = "select rec.*,prv.razon_social,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito "
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rec.deposito_id = '$almacen' and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='2' "
					. " and rec.fecha_recepcion between '".$inicio."' and '".$fin."' order by fecha_recepcion";
            }else{
                $sql = "select rec.*,prv.razon_social,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rec.deposito_id = '$almacen' and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='2' order by fecha_recepcion";
			}	
        }else
        {
           if($rango){
				$inicio = $rango['inicio'];
				$fin = $rango['fin'];            
                $sql = "select rec.*,prv.razon_social,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito "
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . " and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='2' "
					. " and rec.fecha_recepcion between '".$inicio."' and '".$fin."' order by fecha_recepcion";
		   }else{
                $sql = "select rec.*,prv.razon_social,dep.nombre_deposito,tdoc.ncorto "
                    . "from recepcion as rec,proveedor as prv,deposito as dep,relacion_deposito as rd,tipo_documento as tdoc "
                    . "where rec.proveedor_id = prv.id_proveedor and rec.deposito_id = dep.id_deposito"
                    . " and  rd.deposito_id = rec.deposito_id and rd.estatus_relacion = '1'"
                    . "  and rd.trabajador_id = '$trabajador' "
                    . " and tdoc.id_tipo_documento = rec.tipo_doc_ori and rec.tipo_recepcion='2' order by fecha_recepcion";
		   }	
        }
        
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
	
	
    public function insertar($datos)
    {
        $this->iniciar();
        $sql="insert into recepcion("
                . "fecha_recepcion,"
                . "tipo_recepcion,"
                . "usuario_id,"
                . "estatus_recepcion,"
                . "tipo_doc_ori,"
                . "nro_doc_ori,"
                . "comentario,"
                . "origen_id,"
                . "deposito_id,"
                . "unidad_operativa,"
                . "orden_compra"
                . ")values("
                . "now(),"
                . "'".$datos['operacion']."',"
                . "'".$datos['usuario']."',"
                . "'1',"
                . "'".strtoupper($datos['tdoc'])."',"
                . "'".$datos['ndoc']."',"
                . "'".strtoupper($datos['comentario'])."',"
                . "'".$datos['id_origen']."',"
                . "'".$datos['deposito']."',"
		. "'".$datos['unidad']."',"
                . "'".$datos['orden_compra']."'"
                . ")";
        //die($sql);
        $res = $this->_db->exec($sql);
        if(!$res)
        {
            $this->cancelar();
            return FALSE;
        }else
            {
                $ult_recepcion = $this->_db->lastInsertId();
                $producto = $datos['producto'];
                $cantidad = $datos['cantidad'];
                $precio = $datos['precio'];
		$tsa_iva = $datos['tsa_iva'];
		$recibido = $datos['recibido'];
				
		$orden_compra = $datos['orden_compra'];
				
                for($i = 0;$i < count($producto);$i++ )
                {
                    $monto = $precio[$i] * $cantidad[$i];
                    $total = $monto * (($tsa_iva[$i]/100)+1);	
                    $mto_iva = $total-$monto;

                    if(is_array($orden_compra))
                    {
                        if(count($orden_compra)>1)
                            $oc = $orden_compra[$i];
                        else
                            $oc = $orden_compra[0];
                    }else
                        $oc = $orden_compra;
					
					
                    $sql = "insert into det_recepcion("
                            . "recepcion_id,"
                            . "producto_id,"
                            . "precio_producto,"
                            . "cantidad_producto,"
                            . "monto_producto,"
                            . "tsa_iva_producto,"
                            . "mto_iva_producto,"
                            . "total_producto,"
                            . "estatus,"
                            . "recibido_producto,"
                            . "orden_compra_id"
                            . ")values("
                            . "'".$ult_recepcion."',"
                            . "'".$producto[$i]."',"
                            . "'".$precio[$i]."',"
                            . "'".$cantidad[$i]."',"
                            . "'".$monto."',"
                            . "'".$tsa_iva[$i]."',"
                            . "'".$mto_iva."',"
                            ."'".$total."',"
                            . "'1',"
                            . "'".$recibido[$i]."',"
                            . "'".$oc."')";
							
							
                    $res = $this->_db->exec($sql);
                    if(!$res)
                    {
                        $this->cancelar();
                        return FALSE;                        
                    }else
                        {
                            // se actualiza el inventario cuando se confirma la compra
                            //  $existe = $this->verificarStockProd($datos['deposito'],$producto[$i],$presentacion[$i]);
                            // if($existe['total']>0)
                            // {
                            //     $sql = "update stock set cantidad = cantidad + '".$cantidad[$i]."',fecha_ult_act = now()"
                            //     . "where producto_id = '".$producto[$i]."' and deposito_id = '".$datos['deposito']."'";
                                
                            // }else{
                                $sql = "insert into stock(producto_id,deposito_id,cantidad,fecha_ult_act,recepcion_id)"
                                        . "values('".$producto[$i]."','".$datos['deposito']."','".$cantidad[$i]."',now(),'".$ult_recepcion."')";
                            //}
                            $res = $this->_db->exec($sql);
                            if(!$res)
                            {
                                $this->cancelar();
                                return FALSE;                        
                            }else
                                {
                                    if(is_array($orden_compra))
                                    {
                                            for($j=0;$j<count($orden_compra);$j++)
                                            {
                                                    if($orden_compra[$j]>0)
                                                       $sql = "update orden_compra set condicion_orden_compra = 'CERRADO' where id_orden_compra = '".$orden_compra[$j]."'";	
                                            }				
                                    }else
                                    {
                                            if($orden_compra>0)
                                                $sql = "update orden_compra set condicion_orden_compra = 'CERRADO' where id_orden_compra = '".$orden_compra."'";
                                    }		

                                    $res = $this->_db->exec($sql);
                                }	
							
                        }
                }    
                $this->confirmar();
                return true;                   
            }
        
    }
    
    public function insertarInterna($datos)
    {
        $this->iniciar();
        $sql="insert into recepcion("
                . "fecha_recepcion,"
                . "tipo_recepcion,"
                . "usuario_id,"
                . "estatus_recepcion,"
                . "tipo_doc_ori,"
                . "nro_doc_ori,"
                . "comentario,"
                . "deposito_id"
                . ")values("
                . "now(),"
                . "'".$datos['tipo']."',"
                . "'".$datos['usuario']."',"
                . "'1',"
                . "'".strtoupper($datos['tipo_doc'])."',"
                . "'".$datos['nro_doc']."',"
                . "'".strtoupper($datos['comentario'])."',"
                . "'".$datos['deposito']."'"
                . ")";
        //die($sql);
        $res = $this->_db->exec($sql);
        if(!$res)
        {
            $this->cancelar();
            return FALSE;
        }else
            {
                $ult_recepcion = $this->_db->lastInsertId();
                $producto = $datos['producto'];
                                
                for($i = 0;$i < count($producto);$i++ )
                {  
                    $codigo  = $producto[$i]['codigo'];

                    $presentacion = $datos['presentacion'];
                    $cantidad = $datos['cantidad'];

                    for($j=0; $j < count($presentacion);$j++)
                    {
                        if(isset($cantidad[$j]))
                        {    
                            $sql = "insert into det_recepcion("
                            . "recepcion_id,"
                            . "producto_id,"
                            . "presentacion_id,"
                            . "cantidad,"
                            . "estatus"
                            . ")values("
                            . "'".$ult_recepcion."',"
                            . "'".$codigo."',"
                            . "'".$presentacion[$j]."',"
                            . "'".$cantidad[$j]."',"
                            . "'1')";
                            $res = $this->_db->exec($sql);
                            if(!$res)
                            {
                                $this->cancelar();
                                return FALSE;                        
                            }else
                                {
                                    //$existe = $this->verificarStockProd($datos['deposito'],$codigo,$presentacion[$j]);
                                    //if($existe['total']>0)
                                    //{
                                    //    $sql = "update stock set cantidad = cantidad + '".$cantidad[$j]."',fecha_ult_act = now()"
                                    //    . "where producto_id = '".$codigo."' and deposito_id = '".$datos['deposito']."' and presentacion_id = '".$presentacion[$j]."'";
                                        
                                    //}else{
                                        $sql = "insert into stock(producto_id,deposito_id,cantidad,fecha_ult_act,recepcion_id)"
                                                . "values('".$codigo."','".$datos['deposito']."','".$cantidad[$j]."',now(),'".$ult_recepcion."')";
                                    //}
                                    $res = $this->_db->exec($sql);
                                    if(!$res)
                                    {
                                        $this->cancelar();
                                        return FALSE;                        
                                    }
                                }
                        }    
                    }                      
                }
				$sql="update asignacion_transporte set condicion_asignacion_transporte = 'LIBERADO'  
				where transporte_id='".$datos['transporte']."' and despacho_id='".$datos['nro_doc']."'";
				$res = $this->_db->exec($sql);	
				
				$sql="update transporte set condicion_transporte = 'DISPONIBLE' where id_transporte'".$datos['transporte']."'";				
				$res = $this->_db->exec($sql);	
				
                $this->confirmar();
                return true;                   
            }
        
    }
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE CARGA RECEPCION POR PROVEEDOR, TIPO DE OCUMENTO Y NUMERO DE DOCUMETO 
	//------------------------------------------------------------------------------------------------
    public function buscarDocRecep($proveedor,$tipo_doc,$nro_doc)
    {
        
        $sql = "select * from recepcion where proveedor_id = '$proveedor' "
                . "and tipo_doc_ori = '$tipo_doc' and nro_doc_ori = '$nro_doc'";
        
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
        
    }
    
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE CARGA RECEPCION POR PROVEEDOR, TIPO DE OCUMENTO Y NUMERO DE DOCUMETO 
	//------------------------------------------------------------------------------------------------
    public function buscarinfRecepcion($proveedor,$tipo_doc,$nro_doc)
    {
        
        $sql = "select rec.*,drec.*,prd.nombre_producto,dprd.serial_producto,dprd.codigo_producto,dprd.id_det_producto,dep.nombre_deposito,pre.nombre_presentacion from recepcion as rec,"
				. "det_recepcion as drec,producto as prd,det_producto as dprd,presentacion as pre,deposito as dep where proveedor_id = '$proveedor' "
                . "and tipo_doc_ori = '$tipo_doc' and nro_doc_ori = '$nro_doc' and drec.producto_id = dprd.id_det_producto and pre.id_presentacion = dprd.presentacion_id and " 
				. " dprd.producto_id = prd.id_producto and pre.id_presentacion = dprd.presentacion_id and drec.recepcion_id = rec.id_recepcion and
				 dep.id_deposito = rec.deposito_id  order by id_det_recepcion ";
        
        //die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
	
	
	
    public function buscarRecepcion($id)
    {
        
        $sql = "select rec.*,drec.*,prs.nombre_presentacion,prd.id_producto,prd.nombre_producto,dprd.*,tdoc.ncorto,
				marca.nombre_marca,dep.nombre_deposito,prv.razon_social_proveedor  from recepcion as rec,det_recepcion as drec,
				producto as prd,det_producto as dprd,marca,deposito as dep,presentacion as prs,proveedor as prv,tipo_documento as tdoc where 
				rec.id_recepcion = '$id' and drec.recepcion_id = rec.id_recepcion and rec.proveedor_id = prv.id_proveedor and rec.tipo_doc_ori = tdoc.id_tipo_documento and
				drec.producto_id = dprd.id_det_producto and dprd.producto_id = prd.id_producto and marca.id_marca = dprd.marca_id and 
				rec.deposito_id = dep.id_deposito and prs.id_presentacion = dprd.presentacion_id 
				order by id_det_recepcion";
        
       // die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
    
    public function verificarStockProd($deposito,$producto,$presentacion)
    {
        $sql = "select count(*) as total from stock "
                . "where producto_id = '$producto' and deposito_id = '$deposito' and presentacion_id='$presentacion'";
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $res->fetch();
            if($datos['total']>0)
                return $res->fetch();
            else
                return array("total"=>0);
        }else
            return array("total"=>0);
        
    }
    public function cargarDetRecepcion($id)
    {
        $sql = "select drec.*,prd.codigo_producto,prd.nombre_producto,mar.nombre_marca,pre.nombre_presentacion from det_recepcion as drec,producto as prd,marca as mar,presentacion as pre where drec.recepcion_id = '$id' and prd.id_producto = drec.producto_id and prd.marca_id = mar.id_marca and prd.presentacion_id = pre.id_presentacion order by id_det_recepcion  ";
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    }        
            
    
    
}

?>

