<?php
class productoModel extends model
{
	private $_ult_producto;
    public function __construct() {
        parent::__construct();
		$this->_ult_producto=0;
    }
    
    public function cargarProducto($item = FALSE)
    {
        
		if($item)
        {
            $sql = "select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
					. ",0 as existencia from producto as pro,det_producto as dpro, "
					. "marca as mar, presentacion as pre,det_presentacion as dpre "
                    . "where dpro.producto_id = pro.id_producto and nombre_producto like '%$item%'"
                    . " and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id and "
					. " dpre.det_producto_id = dpro.id_det_producto "
					. " and dpre.presentacion_id = pre.id_presentacion order by pro.nombre_producto,id_marca,id_presentacion";
        }else
            {
				$sql = "select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
					. ",0 as existencia from producto as pro,det_producto as dpro, "
					. "marca as mar, presentacion as pre,det_presentacion as dpre "
                    . "where dpro.producto_id = pro.id_producto "
                    . " and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id and "
					. " dpre.det_producto_id = dpro.id_det_producto "
					. " and dpre.presentacion_id = pre.id_presentacion order by pro.nombre_producto,id_marca,id_presentacion";
            }
       // die($sql);    
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();        
		
    }
    
    public function catalogoProducto()
    {
        $sql = "select pro.*,rubro.* from producto as pro,rubro,det_producto as dpro  "
                    . "where rubro.id_rubro = pro.rubro_id and dpro.producto_id = pro.id_producto"
                    . "  order by pro.nombre_producto ";
        
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();        
    }
    //--------------------------------------------------------------------------------------------------
    //METODO QUE CARGA DATOS PARA ARMAR LISTA DE PRECIO
    //--------------------------------------------------------------------------------------------------          
    public function listaPrecioProducto($item = FALSE,$unidad=FALSE,$deposito=FALSE)
    {
    	$usuario = session::get('id_usuario');
    	
        if($unidad)
        {
        	if($deposito)
        	{
        		if($item)
        		{
        			$sql =" select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
					. ",stock.cantidad as existencia,stock.costo_stock as costo,stock.precio_stock as precio,stock.utilidad_stock as utilidad,id_stock from producto as pro,det_producto as dpro, "
					. " marca as mar, presentacion as pre,stock,relacion_unidad as ru,relacion_deposito as rd "
                    . " where dpro.producto_id = pro.id_producto and nombre_producto like '%$item%'"
                    . " and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id and "
					. " dpro.presentacion_id = pre.id_presentacion and dpro.id_det_producto = stock.producto_id "
					. " and stock.deposito_id = '$deposito' and ru.unidad_id = '$unidad' and ru.deposito_id = stock.deposito_id "
					. " and rd.deposito_id = stock.deposito_id and rd.usuario_id = '$usuario' order by pro.nombre_producto,id_marca,id_presentacion";
				}else
					{
						$sql =" select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
						. ",stock.cantidad as existencia,stock.costo_stock as costo,stock.precio_stock as precio,stock.utilidad_stock as utilidad,id_stock from producto as pro,det_producto as dpro, "
						. " marca as mar, presentacion as pre,stock,relacion_unidad as ru,relacion_deposito as rd "
	                    . " where dpro.producto_id = pro.id_producto and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id and "
						. " dpro.presentacion_id = pre.id_presentacion and dpro.id_det_producto = stock.producto_id "
						. " and stock.deposito_id = '$deposito' and ru.unidad_id = '$unidad' and ru.deposito_id = stock.deposito_id "
						. " and rd.deposito_id = stock.deposito_id and rd.usuario_id = '$usuario' order by pro.nombre_producto,id_marca,id_presentacion";
						
					}
			
			}else
				{
					$sql =" select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
						. ",stock.cantidad as existencia,stock.costo_stock as costo,stock.precio_stock as precio,stock.utilidad_stock as utilidad,id_stock " 
						. " from producto as pro,det_producto as dpro,"
						. " marca as mar, presentacion as pre,stock,relacion_unidad as ru, relacion_deposito as rd "
	                    . " where dpro.producto_id = pro.id_producto and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id and "
						. " dpro.presentacion_id = pre.id_presentacion and dpro.id_det_producto = stock.producto_id "
						. " and  ru.deposito_id = stock.deposito_id  and stock.deposito_id = rd.deposito_id and   "
						. " rd.usuario_id = '$usuario' order by pro.nombre_producto,id_marca,id_presentacion";
				}
				
        }else
			{
				if($item)
		        {
		            $sql = "select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
							. ",stock.cantidad as existencia,stock.costo_stock as costo,stock.precio_stock as precio,stock.utilidad_stock as utilidad,id_stockfrom producto as pro,det_producto as dpro, "
							. "marca as mar, presentacion as pre,stock,relacion_deposito as rd "
		                    . "where dpro.producto_id = pro.id_producto and nombre_producto like '%$item%'"
		                    . " and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id and  "
							. " and dpro.presentacion_id = pre.id_presentacion and stock.producto_id = dpro.id_det_producto "
							. " rd.deposito_id = stock.deposito_id and rd.usuario_id ='$usuario' order by pro.nombre_producto,id_marca,id_presentacion";
		        }else
		            {
						$sql = "select pro.*,dpro.*,mar.nombre_marca as marca,pre.nombre_presentacion as presentacion"
							. ",stock.cantidad as existencia,stock.costo_stock as costo,stock.precio_stock as precio, "
							. " stock.utilidad_stock as utilidad,id_stock from producto as pro,det_producto as dpro, "
							. " marca as mar, presentacion as pre,stock,relacion_deposito as rd "
		                    . " where dpro.producto_id = pro.id_producto "
		                    . " and pro.estatus_producto = '1' and mar.id_marca = dpro.marca_id  "
							. " and dpro.presentacion_id = pre.id_presentacion and stock.producto_id = dpro.id_det_producto and "
							. " rd.deposito_id = stock.deposito_id and rd.usuario_id ='$usuario' order by pro.nombre_producto,id_marca,id_presentacion";
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
    //------------------------------------------------------------------------------------------------------    
    // carga los detalles de un producto pasandole el id del producto de paramentro
    public function cargarDetProducto($id)
    {
        $sql = "select dpro.*,marca.nombre_marca,pre.nombre_presentacion,stock.cantidad as existencia "
                . "from det_producto as dpro,marca,presentacion as pre,det_presentacion as dpre,stock "
                . "where dpro.producto_id = '$id'  and dpro.marca_id = marca.id_marca "
                . "and pre.id_presentacion = dpre.presentacion_id and dpre.det_producto_id = dpro.id_det_producto "
				. "and stock.producto_id = dpro.id_det_producto "
                . " order by marca.nombre_marca";
       //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();        
           
    }
	//-----------------------------------------------------------------------------------        
    //METODO QUE INCLUYE UN NUEVO PRODUCTO 
    //-----------------------------------------------------------------------------------
    public function incluirProducto($datos)
    {
        $this->iniciar();

        $sql = "insert into producto("
        . "fecha_creacion,"
        . "nombre_producto,"
        . "estatus_producto,"
        . "grupo_id"
        . ")values("
        . "now(),"
        . "'".$datos['nombre']."',"
        . "'1',"
        . "'".$datos['grupo']."'"
        . ")";
        $res = $this->_db->exec($sql);
        if(!$res)
        {
            return FALSE;
        }else
            {
		$id_producto = $this->ultimo();
				
		$sql = "insert into det_producto("
                . "producto_id,"
                . "codigo_producto,"
                . "marca_id,"
                . "modelo,"
		. "unidad_almacenamiento,"
                . "estatus_det_producto,"
                . "comentario,"
		. "presentacion_id"
                . ")values("
                . "'".$id_producto."',"
                . "'".$datos['codigo']."',"
                . "'".$datos['marca']."',"
                . "'".$datos['modelo']."',"
                . "'".$datos['medida']."',"
		. "'1',"
                . "'".$datos['comentario']."',"
		. "'".$datos['presentacion']."'"
                . ")";
    
				// die($sql);
				$res = $this->_db->exec($sql);
				if(!$res)
				{
					$this->cancelar();
					return FALSE;
				}else
					{
						$this->_ult_producto = $this->ultimo();
						///---------------------------------------------------------------------
						//creamos el detalle de la presentacion
						//----------------------------------------------------------------------
						$sql="insert into det_presentacion(presentacion_id,det_producto_id,estatus_det_producto)
						values('".$datos['presentacion']."','".$this->_ult_producto."','1')";
						$res = $this->_db->exec($sql);

						//----------------------------------------------------------------------
						//seleccionamos los depositos validos
						//----------------------------------------------------------------------
						$sql = "select id_deposito from deposito where estatus_deposito = '1'";
						$res = $this->_db->query($sql);
						if($res)
						{
							$res->setFetchMode(PDO::FETCH_ASSOC);            
							$deposito = $res->fetchAll();
							if(count($deposito))
							{
								//foreach ($deposito as $dep)
								//{
									//-------------------------------------------------------------
									//inserto registro de producto en cada deposito en cantidad 0
									//-------------------------------------------------------------
								//	$sql = "insert into stock(producto_id,cantidad,fecha_ult_act,deposito_id)"
								//			. "values('".$this->_ult_producto."','0',now(),'".$dep['id_deposito']."')";
								//	$res = $this->_db->exec($sql);
								//	if(!$res)
								//	{
								//		$this->cancelar();
								//		return FALSE;
								//	}    
								//}
								
								$this->confirmar();
								return true;   
							}else
							{
								$this->cancelar();
								return FALSE;
							}
								
						}else
						{
							$this->cancelar();
							return FALSE;
						}
													
					}			  
            
			}
        
    }
    
    //--------------------------------------------------------------------------
    //
    //--------------------------------------------------------------------------
    public function editar($datos)
    {
        //$this->iniciar();
        $sql="update producto set nombre_producto = '".$datos['nombre']."',grupo_id ='".$datos['grupo']."' where id_producto = '".$datos['id']."' ";
        $res = $this->_db->exec($sql);
        
        
        $sql = "update det_producto set codigo_producto = '".$datos['codigo']."',marca_id = '".$datos['marca']."',modelo = '".$datos['modelo']."',"
                . "unidad_almacenamiento = '".$datos['medida']."',presentacion_id = '".$datos['presentacion']."' where id_det_producto = '".$datos['id_det']."' ";
              //die($sql); 
        $res = $this->_db->exec($sql);
         if(!$res)
         {
            //        $this->cancelar();
           return FALSE;
         }else
             {
              //      $this->confirmar();
                 return TRUE;
                   
            }
            
            
        
        
        
    }   
    //-----------------------------------------------------------------------------
    //METODO QUE PERMITE REALIZAR LA BUSQUEDA DE UN PRODUCTO POR ID O POR NOMBRE
    //TABLAS(PRODUCTO-DET_PRODUCTO-STOCK)
    //-----------------------------------------------------------------------------        
    public function buscar($valor,$almacen = false)
    {
		if($valor)
		{
                        //BUSCA SI EL VALOR PASADO ES UN ENTERO
			if(is_int($valor))
			{
				if($almacen)
				{
					$sql = "select prd.*,dprd.*,stock.cantidad,stock.precio_stock,nombre_marca as marca 
					from producto as prd,det_producto as dprd,stock where 
					dprd.id_det_producto = '$valor' and  dprd.producto_id = prd.id_producto and marca.id_marca = dprd.marca_id
					and stock.producto_id = dprd.id_det_producto and stock.deposito_id = '$almacen' ";
				}else
					{
						$sql = "select prd.*,dprd.*,stock.cantidad,stock.precio_stock,nombre_marca as marca,nombre_presentacion, 
						um.nombre_uni_med,grp.nombre_grupo,cla.nombre_clasificacion,cla.id_clasificacion from producto as prd,det_producto as dprd,
                                                stock,marca,uni_med as um, grupo as grp,clasificacion as cla,presentacion as pre  where 
						dprd.id_det_producto = '$valor' and  dprd.producto_id = prd.id_producto and  um.id_uni_med = dprd.unidad_almacenamiento
						and stock.producto_id = dprd.id_det_producto and marca.id_marca = dprd.marca_id and dprd.presentacion_id = id_presentacion and 
                                                grp.id_grupo = prd.grupo_id and cla.id_clasificacion = grp.clasificacion_id ";
					}					
			}
                        //BUSCA SI EL VALOR PASADO ES UNA TIRA DE CARACTERES
			if(is_string($valor))
			{
				if($almacen)
				{
					$sql = "select prd.*,dprd.*,stock.cantidad,stock.precio_stock from producto as prd,det_producto as dprd,stock where 
					prd.nombre_producto = '$valor' and  dprd.producto_id = prd.id_producto 
					and stock.producto_id = dprd.id_det_producto and stock.deposito_id = '$almacen' ";
				}else
					{
						$sql = "select prd.*,dprd.*,stock.cantidad,stock.precio_stock from producto as prd,det_producto as dprd,stock where 
						prd.nombre_producto = '$valor' and  dprd.producto_id = prd.id_producto 
						and stock.producto_id = dprd.id_det_producto";
					}	
			}		
			
		}	
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
    }
	
	
	//---------------------------------------------------------------------------
    //busca un producto por su codigo o serial de barra 
	//mod 28/11/2016
    //--------------------------------------------------------------------------
    public function buscarCodProducto($codigo)
    {
        $sql = "select pro.*,mar.nombre_marca,um.nombre_uni_med,dpro.codigo_producto, dpro.id_det_producto,nombre_presentacion "
                . "from producto  as pro,det_producto as dpro,uni_med as um, marca as mar, presentacion as pre  "
                . "where dpro.codigo_producto = '$codigo' and pro.id_producto = dpro.producto_id and pre.id_presentacion = dpro.presentacion_id "
				. " and dpro.marca_id = mar.id_marca and  um.id_uni_med = dpro.unidad_almacenamiento";
        $res = $this->_db->query($sql);
        //die($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
    }
    
    //---------------------------------------------------------------------------------------------
    //busca los productos por descripcion
    //---------------------------------------------------------------------------------------------
    public function buscarDescProducto($producto)
    {
        $sql ="select pro.nombre_producto,dpro.*,marca.nombre_marca,pre.nombre_presentacion,stock.cantidad as existencia "
                . "from det_producto as dpro,marca,presentacion as pre,stock,producto as pro,det_presentacion as dpre "
                . "where pro.nombre_producto like '%$producto%' and dpro.producto_id = pro.id_producto  and dpro.marca_id = marca.id_marca "
                . "and pre.id_presentacion = dpre.presentacion_id and dpre.det_producto_id = dpro.id_det_producto "
				. "and stock.producto_id = dpro.id_det_producto group by dpro.id_det_producto";
        //die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            
            $datos = $res->fetchAll();
            foreach ($datos as $valor)
            {
                $val[] = array(
                    "label"=>$valor['nombre_producto'],
                    "value"=>array("producto"=>$valor['nombre_producto'],
                                    "serial"=>$valor['serial_producto'],
                                    "marca"=>$valor['nombre_marca'],
                                    "codigo"=>$valor['codigo_producto'],
                                    "modelo"=>$valor['modelo'],
                                    "id"=>$valor['id_det_producto']));
            }
            
            
            return $val;
        }else
            return array();
        
    }
    
	//-------------------------------------------------------------------------------------------------
	//busca los productos por descripcion
	//-------------------------------------------------------------------------------------------------
    public function buscarAutoProducto($producto)
    {
        $sql ="select pro.nombre_producto,dpro.*,marca.nombre_marca,pre.nombre_presentacion "
                . " from det_producto as dpro,marca,presentacion as pre,producto as pro "
                . " where pro.nombre_producto like '%$producto%' and dpro.producto_id = pro.id_producto "
                . " and dpro.marca_id = marca.id_marca and dpro.presentacion_id = pre.id_presentacion 
                group by dpro.id_det_producto";
        //die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            
            $datos = $res->fetchAll();
            foreach ($datos as $valor)
            {
                $val[] = array(
                    "label"=>$valor['nombre_producto'],
                    "value"=>array("producto"=>$valor['nombre_producto'],
                                    "codigo"=>$valor['codigo_producto'],
									"serial"=>$valor['serial_producto'],
                                    "marca"=>$valor['nombre_marca'],                              
                                    "modelo"=>$valor['modelo'],
									"presentacion"=>$valor['nombre_presentacion'],
									"existencia"=>0,
									"precio"=> 0,
                                    "id"=>$valor['id_det_producto']));
            }
            
            
            return $val;
        }else
            return array();
        
    }
	
	
	//----------------------------------------------------------------------------------------
    //METODO QUE BUSCA UN PRODUCTO EN FORMA DETALLADA
    //----------------------------------------------------------------------------------------
    public function buscarDetProducto($id)
    {
        $sql ="select pro.nombre_producto,dpro.*,marca.nombre_marca,pre.nombre_presentacion "
                . "from det_producto as dpro,marca,presentacion as pre,producto as pro "
                . "where dpro.id_det_producto = '$id' and dpro.producto_id = pro.id_producto  and dpro.marca_id = marca.id_marca "
                . "and pre.id_presentacion = dpro.presentacion_id ";
        //die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();
        
    }
	//-------------------------------------------------------------------------------------------------------
	//METODO QUE BUSCA UN PRODUCTO EN FORMA DETALLADA PARA LA VENTA(Factura-) 
	//-------------------------------------------------------------------------------------------------------
    public function buscarProductoVenta($producto)
    {
    	$usuario = session::get('id_usuario');
        $sql ="select pro.nombre_producto,dpro.*,marca.nombre_marca,pre.nombre_presentacion,stock.*,dep.nombre_deposito "
                . "from det_producto as dpro,marca,presentacion as pre,producto as pro,stock,deposito as dep "
                . " where pro.nombre_producto like '%$producto%' and dpro.producto_id = pro.id_producto"
                . " and dpro.marca_id = marca.id_marca and stock.producto_id = dpro.id_det_producto "
                . " and pre.id_presentacion = dpro.presentacion_id and dep.id_deposito = stock.deposito_id "
                . " and stock.deposito_id in(select deposito_id from relacion_deposito where estatus_relacion = '1' and usuario_id ='$usuario' )  ";
       // die($sql);
        $res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
        
    }
	//------------------------------------------------------------------------------------------
	//METODO QUE BUSCA  UN PRODUCTO SEGUN EL NUMERO DE STOCK EN LA TABLA STOCK
	//------------------------------------------------------------------------------------------
	public function buscarStockProducto($stock)
	{
		$sql = "select pro.nombre_producto,dpro.*,marca.nombre_marca,pre.nombre_presentacion,"
			  ."stock.*,dep.nombre_deposito from producto as pro,det_producto as dpro,marca, "
			  ."presentacion as pre,deposito as dep, stock where dpro.producto_id = pro.id_producto and "
			  ."dpro.marca_id = marca.id_marca and stock.producto_id = dpro.id_det_producto and "
			  ."pre.id_presentacion = dpro.presentacion_id and dep.id_deposito = stock.deposito_id "
			  ."and stock.id_stock = '$stock'";
		//die($sql);
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();		
			
		
	}
	
	//------------------------------------------------------------------------------------------
	//METODO QUE ACTUALIZA LA UTILIDAD Y PRECIO DE UN PRODUCTO EN STOCK
	//------------------------------------------------------------------------------------------
	public function actualizarUtilidad($datos)
	{
			$sql = "update stock set 
			utilidad_stock = '".$datos['utilidad']."',
			precio_stock = '".$datos['precio']."' where id_stock = '".$datos['stock']."'";
			
			$res = $this->_db->exec($sql);
			if(!$res)
			{
				$this->cancelar();
				return FALSE;
			}else
				{
					return TRUE;
				}		
					
				
			
		
	}
		
	//-------------------------------------------------------------------------------------------
	//METODO QUE BUSCA LA DISPONIBILIDAD DE EXISTENCIA DE UN PRODUCTO
	//-------------------------------------------------------------------------------------------
	public function buscarDisponibilidadProducto($stock)
	{
		$disponible = array();	
		$bloquedo = 0;
		$existencia = 0;
		$sql ="select sum(cantidad_block) as bloqueado from block_stock where stock_id = '$stock'";
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $datos =  $res->fetch();
            if($datos['bloqueado']>0)
				$bloqueado= $datos['bloqueado'];
			else
				$bloqueado = 0;	
        
        }		
		
		$sql ="select sum(cantidad) as existencia from stock where id_stock = '$stock'";
		$res = $this->_db->query($sql);
        if($res){
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $datos =  $res->fetch();
            if($datos['existencia']>0)
				$existencia = $datos['existencia'];
			else
				$existencia = 0;	
        
        }
		
		$disponible["cantidad"] = $existencia-$bloqueado; 
		
		return $disponible;
				
		
	}
	
	public function bloquearProducto($stock,$producto,$cantidad)
	{
		$estacion = "prueba";
		$usuario  = session::get('id_usuario');
		
		$sql = "insert into block_stock("
				."fecha_creacion,"
				."usuario_creador,"
				."estacion_block,"
				."producto_block,"
				."stock_id,"
				."cantidad_block)values("
				."now(),"
				."'".$usuario."',"
				."'".$estacion."',"
				."'".$producto."',"
				."'".$stock."',"
				."'".$cantidad."')";
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
	
	public function desbloquearProducto($stock)
	{
		$estacion = "prueba";
		$usuario  = session::get('id_usuario');
		
		$sql = "delete  from block_stock where  stock_id ='$stock' and usuario_creador='$usuario' and estacion_block='$estacion'" ;	
			
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
        
        
        public function insertarImagen($valor)
        {
            $sql = "select count(*)as total from imagenes where codigo='".$valor['nombre']."'";
            $res = $this->_db->query($sql);
            if($res)
            {
                $res->setFetchMode(PDO::FETCH_ASSOC);
                $datos =  $res->fetch();
                if($datos['total']>0)
                {
                    $sql="update imagenes set imagen='".$valor['data']."',tipo='".$valor['tipo']."' where codigo = '".$valor['nombre']."'";
                    $res = $this->_db->exec($sql);        
                    if(!$res)
                    {
                        return FALSE;
                    }else
                    {
                        return TRUE;
                    }
                    
                }else
                {
                    $sql="insert into imagenes(imagen,tipo_imagen,codigo)"
                    . "values('".$valor['data']."','".$valor['tipo']."','".$valor['nombre']."')";
                    $res = $this->_db->exec($sql);        
                    if(!$res)
                    {
                        return FALSE;
                    }else
                    {
                        return TRUE;
                    }
                }
            }
                        
        }        
        
}