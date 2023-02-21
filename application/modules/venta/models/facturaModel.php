<?php
class facturaModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    public function cargarFactura($item = false)
    {
        if($item)
        {
            $sql = "select venta.*,cli.rif_cliente,cli.razon_social_cliente,cli.tipo_cliente,cli.direccion_fiscal_cliente,"
                    . "sum(total_producto)as tproducto,sum(mto_iva_producto)as tiva from venta,det_venta,cliente as cli where venta.cliente_id = cli.id_cliente "
                    . "and venta_id = id_venta group by id_venta order by venta.fecha_venta,venta.id_venta";
        }else
        {
            $sql = "select venta.*,cli.rif_cliente,cli.razon_social_cliente,cli.tipo_cliente,cli.direccion_fiscal_cliente,"
                    . "sum(total_producto)as tproducto,sum(mto_iva_producto)as tiva from venta,det_venta,cliente as cli where venta.cliente_id = cli.id_cliente "
                    . "and venta_id = id_venta group by id_venta order by venta.fecha_venta,venta.id_venta";
        }
        //die($sql);
        $res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            {
                return array();
            }   
    }
	
	//----------------------------------------------------------------------------------------------------
	//26/07/2017
	//METODO QUE INSERTA REGISTRO DE VENTA - LIBERA PROD. BLOQUEADOS Y ACTUALIZA INVENTARIO
	//----------------------------------------------------------------------------------------------------
	
	public function insertar($datos)
	{
		$sql = "select id_tipo_documento from tipo_documento where ncorto ='FAC' ";
		$res = $this->_db->query($sql);	
		$res->setFetchMode(PDO::FETCH_ASSOC);
                $tipo_doc = $res->fetch();
           
		   			
		$estacion = gethostname();
		$usuario = session::get('id_usuario');
		$hoy = date('Y-m-d');
		$vence = date('Y-m-d',strtotime('+'.$datos['plazo'].'day',strtotime($hoy)));
		$comentario = "FACTURA A ".$datos['tipo'].' VENCE EL '.$vence;
		
		$this->iniciar();
		$sql ="insert into venta("
                        ."fecha_venta,"
                        ."vendedor_id,"
                        ."cliente_id,"
                        ."tipo_venta,"
                        ."plazo_credito,"
                        ."estatus_venta,"
                        ."condicion_venta,"
                        ."control_venta,"
                        ."comentario_venta,"
                        ."usuario_id,"
                        ."estacion_venta"
                        .")values("
                        ."now(),"
                        ."'".$datos['vendedor']."',"
                        ."'".$datos['cliente']."',"
                        ."'".$datos['tipo']."',"
                        ."'".$datos['plazo']."',"
                        ."'1',"
                        ."'POR IMPRIMIR',"
                        ."'".$datos['control']."',"
                        ."'".$comentario."',"
                        ."'".$usuario."',"
                        ."'".$estacion."'"
                        .")";
                        //die($sql);
			$res = $this->_db->exec($sql);
                    if(!$res)
                    {
                        $this->cancelar();
                        return FALSE;	
                    }
                    else
                    {
                                $ultimo = $this->ultimo();
				
				$producto = $datos['producto'];
				$cantidad = $datos['cantidad'];
				$precio   = $datos['precio']; 
				$stock    = $datos['stock'];
                                $imp = $datos['impuesto'];
				
				$mto_total = 0;
				for($i=0;$i < count($producto);$i++)
				{
                                    $monto = $precio[$i] * $cantidad[$i];
                                    $total = ($monto * (($imp[$i]/100)+1));
                                    $mto_iva = $total - $monto;

                                    $sql="insert into det_venta("
                                            ."venta_id,"
                                            ."producto_id,"
                                            ."cantidad_producto,"
                                            ."precio_producto,"
                                            ."tsa_iva_producto,"
                                            ."mto_iva_producto,"
                                            ."total_producto)values("
                                            ."'".$ultimo."',"	
                                            ."'".$producto[$i]."',"
                                            ."'".$cantidad[$i]."',"
                                            ."'".$precio[$i]."',"
                                            ."'".$imp[$i]."',"
                                            ."'".$mto_iva."',"
                                            ."'".$total."')";
                                    //die($sql);
                                    $res = $this->_db->exec($sql);
                                    if(!$res)
                                    {
                                            $this->cancelar();
                                            return FALSE;	
                                    }else
                                        {
                                            $mto_total = + $total;

                                            $sql ="update stock set cantidad = cantidad - '".$cantidad[$i]."' where id_stock = '".$stock[$i]."' ";
                                            $res = $this->_db->exec($sql);
                                            if(!$this->desbloquearStock($stock[$i]))
                                            {
                                                $this->cancelar();
                                                return FALSE;
                                                //die("error1");
                                            }

                                        }
			        					
				}
				
				if($datos['tipo']=='CREDITO')
				{
					$comentario = "FACTURA POR COMPRA A CREDITO ....";
							
					$sql="insert into cxc("
						."fecha_creacion,"
						."tipo_doc_ori,"
						."nro_doc_ori,"
						."debitos_cxc,"
						."saldo_cxc,"
						."estatus_cxc,"
						."comentario_cxc,"
						."fecha_vence_cxc,"
						."condicion_cxc,"
                                                . "cliente_id"
                                                . ")values("
						."now(),"
						."'".$tipo_doc['id_tipo_documento']."',"
						."'".$ultimo."',"
						."'".$mto_total."',"
						."'".$mto_total."',"
						."'1',"
						."'".$comentario."',"
						."'".$vence."',"
						."'POR COBRAR',"
                                                ."'".$datos['cliente']."'"
                                                . ")";
						
						$res = $this->_db->exec($sql);
						if(!$res)
						{
                                                    $this->cancelar();
                                                    return false;
							
						}
					
				}
				
				$this->confirmar();
				return true;
				
	        }
				
	}
    
    //-----------------------------------------------------------------------------------------------
    //METODO QUE DESBLOQUE PRODUCTO A VENDER
    //-----------------------------------------------------------------------------------------------
	public function desbloquearStock($codigo)
	{
            $estacion = "prueba";
            $usuario  = session::get('id_usuario');

            $sql = "delete  from block_stock where  stock_id ='$codigo' and usuario_creador='$usuario' and estacion_block='$estacion'" ;	

            //die($sql);
            $res = $this->_db->exec($sql);
            if(!$res)
            {
                return FALSE;
            }else
                {
                    return TRUE;
                }		
		
	}
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA FACTURA POR ID 
	//------------------------------------------------------------------------------------------------
    public function buscarFactura($id)
	{
		$sql = "select *, cli.rif_cliente,cli.razon_social_cliente from venta, cliente as cli 
		where id_venta = '$id' and cli.id_cliente = cliente_id ";
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            {
                return array();
            }   
		
	}
    //------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA DETALLE DE FACTURA POR ID DE FACTURA
	//------------------------------------------------------------------------------------------------
    public function buscarDetFactura($id)
	{
		$sql = "select dv.*,p.nombre_producto from det_venta as dv,det_producto as dp,producto p  
		where dv.venta_id = '$id' and dv.producto_id = dp.id_det_producto and p.id_producto = dp.producto_id";
		$res = $this->_db->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            {
                return array();
            }   
		
	}
    
    
}
