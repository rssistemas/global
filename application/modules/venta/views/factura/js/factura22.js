$(document).ready(function(){
//----------------------------------------------------------------------------------------------------
//METODO QUE BUSCA INFORMACION DE UN CLIENTE POR MEDIO DE SU RIF
//----------------------------------------------------------------------------------------------------
     var getCliente = function(){
         $.post('/venta/factura/buscarCliente/','rif=' + $("#rif").val(),function(datos){
		         if(datos.length >0 )
		         {
		             // $('#rif').html('');
		             //$('#razon_social').val('');
		             $('#cliente').val('0');
					
		             //$('#rif').val(datos[0].rif_cliente);
		             $('#razon_social').val(datos[0].razon_social_cliente);
		             $('#cliente').val(datos[0].id_cliente);
		             
		             getDescuento(datos[0].id_cliente,0);
		             
		         }else
		         {
		             alert("Cliente no registrado .............");
		             $('#rif').val("");
		             $('#razon_social').val('');
		             $('#id_cliente').val('0');
		             $('#rif').focus();
		         }

             },'json');
         };
    
    
    
    var getContar = function(){
            $.post('/archivo/proveedor/contarCliente/','rif=' + $("#rif").val()+'&tipo=' + $("#tipo_rif").val(),function(datos){
            if(datos)
            {
                if(datos.total > 0)
                {    
                    alert("El proveedor ya esta registrado ........");
                    $('#rif').val("");
                    $('#rif').focus();
                }    
            }

            },'json');
    };   
    
    var getCorreo = function(){ 
        if(!(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/.test($('#correo').val()))){
            alert("Formato no permitido.........");
            $('#correo').val('');
            $('#correo').focus();
        }else{
                $.post('/archivo/proveedor/comprobarCorreo/','correo=' + $("#correo").val() ,function(datos){
                if(datos.total >0)
                {
                    alert("Este correo electronico ya esta en uso...");
                    document.getElementById('correo').value="";
                    document.getElementById('correo').focus();
                }
            },'json');
        } 
    };
	
	
	//---------------------------------------------------------------------
	 //funcion para busqueda directa de los productos
	//-------------------------------------------------------------------- 
    var getBusqueda = function(valor){
        $.post('/venta/factura/buscarProductoCatalogo/','valor='+valor,function(datos){
           if(datos.length)     
           {
               var id = $('id_fila').val();
               $('#table_concepto').html("");
                var tabla = '';
                     tabla += '<table class="table  table-bordered">';
                     tabla += '<tr>';           
                     tabla += '<td class="cabecera" width="70">Codigo.</td>';
                     tabla += '<td class="cabecera" width="400">Nombre</td>';
                     tabla += '<td class="cabecera" width="80">Precio</td>';
                     tabla += '<td class="cabecera" width="50">Exist.</td>';
                     tabla += '<td class="cabecera" width="180">Ubicacion</td>';  
                     tabla += '<td class="cabecera" width="30"></td>';
                     tabla += '</tr>';
                
                
                var tr = '';    
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center">'+datos[i].codigo_producto+'</td><td>'+datos[i].nombre_producto+' / '+datos[i].nombre_presentacion+'</td><td>'+datos[i].precio_stock+'</td><td>'+datos[i].cantidad+'</td><td>'+datos[i].nombre_deposito+'</td><td><button class="cerrar btn btn-default" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_det_producto+'" data-dep="'+datos[i].deposito_id+'" data-stock="'+datos[i].id_stock+'" ><i class="fa fa-arrow-down"></i></button></td>';
                    tr += '</tr>';
                }
                    
                tabla += tr;
                tabla += '</table>';
                
                $('#table_concepto').html( tabla );
           }else
           {
               
           }

            },'json');

    };
	
    
//-------------------------------------------------------------------------------------------------------
// FUNCION QUE CARGA LOS DETALLES DE la factura DE LA TABLA producto
//-------------------------------------------------------------------------------------------------------
	var getProducto  = function(valor){
		
        $.ajax( {  
                 url: '/venta/factura/buscarProducto/',
                 type: 'POST',
                 dataType : 'json',
                 async: false,
                 data: 'cod='+valor,
                 success:function(datos){
                         //$('#recepcion').val(datos[0].id_recepcion);
                         //$("#tabla >tbody").html('');
                         //var total = 0;
                         //var subtotal=0;
                         //var iva = 0;
                         //for(i=0;i < datos.length;i++)
                         //{   	
                                 var count = $('#tabla >tbody >tr').length;
                                 var idPrd= count +1;	


                                 var nuevaFila="<tr>";								
                                 nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value='"+datos.codigo_producto+"' readOnly='true'  class='form-control  codigo' /></td>";
                                 nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' value='"+datos.nombre_producto+"' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='"+datos.id_det_producto+"'  /></td>";
                                 nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control  text-right' value='0'  /></td>";            
                                 nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"'  class='form-control  text-right' value='0'  /></td>";						
                                 nuevaFila=nuevaFila+"<td><input type='text' name='tsa_iva[]'  id='tsa_iva"+idPrd+"' data-id='"+idPrd+"' class='form-control calculo text-right'  value='0' /></td>";								
                                 nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value='0'  /></td>";						

                                 nuevaFila=nuevaFila+"</tr>";

                                 $("#tabla tbody").append(nuevaFila);   

                                 //total = total + parseFloat(datos[i].total_producto);
                                 //subtotal = subtotal + parseFloat(datos[i].monto_producto);
                                 //iva = iva + parseFloat(datos[i].mto_iva_producto);	

                         //}

                                 //$('#subtotal').val(subtotal);
                                 //$('#iva').val(iva);
                                 //$('#total').val(total);
                 },
                 error: function(xhr, status) {
                     alert('Disculpe, existió un problema');
                 }
             });	
    };
	
	
//-----------------------------------------------------------------------------------------------------
//
//-----------------------------------------------------------------------------------------------------
var getDescuento = function(cliente,subtotal){

        $.post('/venta/cliente/buscarDescuentoCliente/','cliente=' + cliente +'&monto=' + subtotal,function(datos){
    if(datos.descuento > 0 )
    {
        alert("El cliente tiene descuentos ........");
        $('#tsa_desc').val(datos.descuento);                    
    }else
        $('#tsa_desc').val(0);

    },'json');

};
	
//------------------------------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------------------------------
var getIVA= function(cliente,subtotal){

        $.post('/venta/cliente/buscarDescuentoCliente/','cliente=' + cliente +'&monto=' + subtotal,function(datos){
    if(datos.descuento > 0 )
    {
        alert("El cliente tiene descuentos ........");
        $('#tsa_desc').val(datos.descuento);                    
    }else
        $('#tsa_desc').val(0);

    },'json');

};
	
	//-------------------------------------------------------------------------------------------------------
	//METODO QUE PERMITE AGREGAR DETALLE DE FATURA EN EL FORMULARIO
	//-------------------------------------------------------------------------------------------------------
	
     $(document).on('click',"#agregar",function(){
        
             var count = $('#tabla >tbody >tr').length;
             var idPrd= count +1;
             var opt="";
             
             $.ajax( {  
                        url: '/venta/factura/buscarImpuesto/',
                        type: 'POST',
                        dataType : 'json',
                        async: false,
                        data: 'codigo=0',
                        success:function(datos){
             
                            if(datos.length > 0)
                             {
                                 opt = opt + "<option value='0'>-Seleccione-</option>"; 
                                for (i = 0; i < datos.length; i++)
                                {
                                     opt = opt + "<option value='"+datos[i].tasa_impuesto+"'>"+datos[i].nombre_impuesto+"</option>"; 

                                }
                                 
                            
                            }
                            var nuevaFila="<tr>";
                            nuevaFila=nuevaFila+"<td class='celda-tabla'><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"'  class='form-control input-sm codigo ' /><input type='hidden' name='stock[]' id='stock"+idPrd+"'  /></td>";
                            nuevaFila=nuevaFila+"<td class='celda-tabla'><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control input-sm ' readonly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"'  /></td>";
                            nuevaFila=nuevaFila+"<td class='celda-tabla'><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control input-sm text-right' readonly='true' /></td>";

                            nuevaFila=nuevaFila+"<td class='celda-tabla'><input type='text' name='cantidad[]' id='cantidad"+idPrd+"' data-id='"+idPrd+"'  class='form-control input-sm text-right calcular'  /><input type='hidden' name='disponible[]' id='disponible"+idPrd+"'  /></td>";
                            nuevaFila=nuevaFila+"<td class='celda-tabla'><select  name='imp[]' id='imp"+idPrd+"' data-id='"+idPrd+"' class='form-control input-sm'  >"+opt+"</select></td>";
                          
                            nuevaFila=nuevaFila+"<td class='celda-tabla'><input type='text' name='total[]' id='total"+idPrd+"' class='form-control text-right input-sm ' readonly='true' /></td>";
                            nuevaFila = nuevaFila+"<td class='celda-tabla'><i class='fa fa-trash-o btn' id='eliminar'></i></td>";
                            nuevaFila=nuevaFila+"</tr>";
                            $("#tabla tbody").append(nuevaFila);
                        	
	
                        }   
             });	
                
           
        
     });
    
    //-----------------------------------------------------------------------------------------------------
    //METODO QUE SE EJECUTA MEDIANTE CLASE .CERRAR CARGA PRODUCTO SEGUN ID DE STOCK Y SU DISPONIBILIDAD 
    //PARA ELLO RESTA LOS PROODUCTOS BLOQUEADOS CONTRA LA CANTIDAD EN STOCK
    //-----------------------------------------------------------------------------------------------------
	$(document).on('click','.cerrar',function(e){
	var deposito = $(this).data('dep');
	var valor    = this.value;
	var id       = $('#id_fila').val(); 
	var stock    = $(this).data('stock');
	var disponible  = 0;
   // alert(id);
        $.ajax( {  
            url: '/venta/factura/buscarProducto/',
            type: 'POST',
            dataType : 'json',
            async: false,
            data: 'codigo='+stock,
            success:function(datos){
                                if(datos)
                                {

                                    $.ajax( {  
                                           url: '/venta/factura/buscarBloqueos/',
                                           type: 'POST',
                                           dataType : 'json',
                                           async: false,
                                           data: 'codigo='+stock,
                                           success:function(val){
                                            if(val)
                                            {
                                                    disponible = val.cantidad;
                                                    //alert(val.cantidad);	
                                            }

                                             },
                                           error: function(xhr, status) {
                                                           alert('Disculpe, existiÃ³ un problema');
                                                           }
                                });


                                        $('#codigo'+id).val(datos.codigo_producto);
                                        $('#descripcion'+id).val(datos.nombre_producto+'('+datos.nombre_prentacion+')');
                                        $('#precio'+id).val(datos.precio_stock);
                                        $('#disponible'+id).val(disponible);

                                        $('#cantidad'+id).val(00);
                                        $('#id'+id).val(datos.id_det_producto);
                                        $('#stock'+id).val(stock);

                                }

                            },
            error: function(xhr, status) {
                            alert('Disculpe, existiÃ³ un problema');
                            }
                });

    });
	
	//-----------------------------------------------------------------------------------------------------
    //METODO QUE SE EJECUTA MEDIANTE CLASE .EDITAR CARGA LOS DATOS DE UNA FACTURA Y LOS DETALLES
    //
    //-----------------------------------------------------------------------------------------------------
	$(document).on('click','.buscar',function(){
	
	var id = $(this).data('id');
		
   // alert(id);
        $.ajax( {  
                    url: '/venta/factura/buscarFactura/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'valor='+id,
                    success:function(datos){
                                if(datos)
                                {			
                                        $('#nro').val(datos.id_venta);
                                        $('#fecha').val(datos.fecha_venta);
                                        $('#cliente').val(datos.razon_social_cliente);
                                        $('#tipo').val(datos.tipo_venta);
                                        $('#vence').val(datos.fecha_venta);
                                        $('#direccion').val(datos.direccion_fiscal_cliente);
                                        
                                         $.ajax( {  
                                                url: '/venta/factura/buscarDetFactura/',
                                                type: 'POST',
                                                dataType : 'json',
                                                async: false,
                                                data: 'valor='+id,
                                                success:function(val){
                                                                        if(val.length > 0)
                                                                        {
                                                                                 $("#tabla tbody").html('');
                                                                                 $('#enlace').html('');

                                                                                var total = 0;
                                                                                var subtotal=0;
                                                                                var iva = 0; 
                                                                                for(i = 0;i < val.length;i++)
                                                                                {

                                                                                        var nuevaFila="<tr>";
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].producto_id+"</td>";
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].nombre_producto+"</td>";
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].precio_producto+"</td>";
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].cantidad_producto+"</td>";
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].precio_producto+"</td>";
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].mto_iva_producto+"</td>"     
                                                                                        nuevaFila=nuevaFila+"<td>"+val[i].total_producto+"</td>";
                                                                                        nuevaFila = nuevaFila+"<td></td>";
                                                                                        nuevaFila=nuevaFila+"</tr>";
                                                                                        $("#tabla tbody").append(nuevaFila);

                                                                                        total = total + parseFloat(val[i].total_producto);
                                                                                        subtotal = subtotal + parseFloat(val[i].precio_producto * val[i].cantidad_producto);
                                                                                        iva = iva + parseFloat(val[i].mto_iva_producto);
                                                                                }

                                                                                $('#subtotal').val(subtotal);
                                                                                $('#iva').val(iva);
                                                                                $('#total').val(total);
                                                                                var url = '<a  target="_blank" href="/globalAdm/reporte/index/impOC/'+id+'" title="Imprimir Orden de Compra"><button class="btn btn-default"><i class="fa fa-print"></i></button></a>';
                                                                                $('#enlace').append(url);	

                                                                        }

                                                                },
                                                error: function(xhr, status) {
                                                                alert('Disculpe, existiÃ³ un problema');
                                                                }
                                });

                                            }

                                    },
                    error: function(xhr, status) {
                                    alert('Disculpe, existiÃ³ un problema');
                                    }
                });

    });
	
	//----------------------------------------------------------------------------------------------------
	//METODO QUE PERMITE REALIZAR BLOQUEOS DE PRODUCTOS A FACTURAR
	//----------------------------------------------------------------------------------------------------
	var bloquear = function(stock,producto,cantidad){
		$.post('/venta/factura/bloquearproducto/','stock=' +stock+'&producto=' +producto+'&cantidad='+cantidad,function(datos)
		{
            if(datos)
            {
                    
                    //alert("El Producto ya esta Bloqueado.....");
                    //$('#').val("");
                    //$('#rif').focus();
                    return true;
                    
            }else
            	{
            		return false;
            	}

        },'json');

		
		
	};
	
	var desbloquear = function(stock,producto,cantidad){
		$.post('/venta/factura/bloquearproducto/','stock=' +stock+'&producto=' +producto+'&cantidad='+cantidad,function(datos)
		{
            if(datos)
            {
                    
                    //alert("El Producto ya esta Bloqueado.....");
                    //$('#').val("");
                    //$('#rif').focus();
                    return true;
                    
            }else
            	{
            		return false;
            	}

        },'json');

				
	};
	
	//-------------------------------------------------------------------------------------------
	//METODO QUE LIBERA PRODUCTOS DE BLOQUEOS QUE SE REALIZAN PARA FACTURAR
	//-------------------------------------------------------------------------------------------
	
	var liberarProducto = function(stock){
		
		var count = $('#tabla >tbody >tr').length;
		var resp = true;
		for(i = 1;i <= count;i++)
		{
                    var val_stock = $('#stock'+i).val();
                    var val_producto =  $('#id'+i).val();
                    var val_cantidad =  $('#cantidad'+i).val();

                    $.ajax( {  
                                url: '/venta/factura/desbloquearproducto/',
                                type: 'POST',
                                dataType : 'json',
                                async: false,
                                data: 'stock=' +val_stock,
                                success:function(val){
                                                        if(!val)
                                                        {
                                                                return false;	
                                                        }
                                                },
                                error: function(xhr, status) {
                                                alert('Disculpe, existiÃ³ un problema');
                                                }
                            });
						
			
		}
				
		return true;

		
		
	};
	
	//-------------------------------------------------------------------------------------------------

    //los llamados
    $('#rif').change(function(){
		

        if(!$('#rif').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getCliente();
             //var cli = $('#cliente').val();
             //var tot = $('#subtotal').val();
             

    });
    

    $('#correo').change(function(){

        if(!$('#correo').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getCorreo();

    });
       
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
            if(e.which == 13) {
                e.preventDefault();
                return false;
            }
            if(e.which == 115) {
                //e.preventDefault();
		var myDNI = $(this).data('id');
		$(".modal #id_fila").val( myDNI );
		$("#myModal").modal();
		
                //alert("Pulsaste f5");
                return false;
            }
            $("#producto").focus();
        });

	//--------------------------------------------------------------------------------------------
	//METODO QUE EJECUTA BUSQUEDA DE PRODUCTOS EN FORMULARIO EMERGENTE
	//--------------------------------------------------------------------------------------------
        
	    $(document).on('keyup','#producto',function(){
	       var pro = this.value; 
	       if(pro.length >2)
	       {
	           getBusqueda(pro);
	       }
	   });
	
	//-----------------------------------------------------------------------------------------------
	
    $('#limpiar').click(function(){
            location.reload();
            
        });
		
		
		
		
	//----------------------------------------------------------------------------------------------------
	//METODO QUE RECALCULA LOS TOTALES DE LA FACTURA Y BLOQUEA LAS CANTIDADES DEL PRODUCTO SELECCIONADO
	//---------------------------------------------------------------------------------------------------
	
	$(document).on('blur','.calcular',function(){
		var id_cant  = $(this).data('id');
		var val_cant = $(this).val();
		var val_disp = $('#disponible'+id_cant).val();
		var val_stock= $('#stock'+id_cant).val();
		var val_prod = $('#id'+id_cant).val();
		var val_precio=$('#precio'+id_cant).val();
		var total =    $('#total').val();
		var subtotal = $('#subtotal').val();
		var iva      = $('#iva').val();
		var descuentp= $('#descuento').val();
		var tsa_iva  = $('#tsa_iva'+id_cant).val();
		//var tsa_desc = $('#tsa_desc').val();		
		
		 
		 if(val_cant > val_disp)
		 {
		 	alert("La cantidad es mayor a lo disponible ....");
		 	$('#cantidad'+id_cant).val(0);
		 	$('#cantidad'+id_cant).focus();
		 }else
		 	{
                            if(val_cant > 0)
                            {
                                bloquear(val_stock,val_prod,val_cant);
                               if(!tsa_iva)
                               {
                                   tsa_iva = $('#default_iva').val();
                               }
                                //alert(tsa_iva);                
                                $('#total'+id_cant).val(val_precio*val_cant);

                                subtotal = parseFloat(subtotal) + (val_precio*val_cant);

                                iva = parseFloat(iva) + ((subtotal * ((parseFloat(tsa_iva)/100) +1)) - subtotal);

                                total = parseFloat(total) + (subtotal + iva);

                                $('#subtotal').val(subtotal.toFixed(2));
                                $('#iva').val(iva.toFixed(2));
                                $('#total').val(total.toFixed(2));


                            }
		 		
		 	}
		
		
		
	});
	//-------------------------------------------------------------------------------------------------------
	//activa campos del formulario y el boton agregar detalle
        //------------------------------------------------------------------------------------------------------
	$('#unidad').change(function(){
		activarCampos();
                $('#agregar').attr('disabled',false);
                $('#guardar').attr('disabled',false);
	});	
	
	$('#act_pedido').click(function(){
		if($('#act_pedido').prop('checked'))
		{
			$('#pedido').attr('disabled',false);
			$('#bto_pedido').attr('disabled',false);
		}else
		{
			$('#pedido').attr('disabled',true);
			$('#bto_pedido').attr('disabled',true);
		}	
		
	});
	
	$('#tipo').change(function(){
		if(this.value=='CREDITO')
		{
			$('#plazo').attr('disabled',false);
		}else
			$('#plazo').attr('disabled',true);
		
	});
	
	//---------------------------------------------------------------------------------------------
	//METODO AQUE ACTIVA CAMPOS DE FORMULARIO
	//---------------------------------------------------------------------------------------------
	var activarCampos = function(){
		
		$('#cliente').attr('disabled',false);
		$('#rif').attr('disabled',false);
                $('#serie').attr('disabled',false);
		$('#tipo_doc').attr('disabled',false);
		$('#vendedor').attr('disabled',false);
		//$('#vendedor').attr('disabled',false);
		//$('#comentario').attr('disabled',false);
		
	};
	
	//---------------------------------------------------------------------------------------------
	//METODO AQUE DESACTIVA CAMPOS DE FORMULARIO
	//---------------------------------------------------------------------------------------------
	var desactivarCampos = function(){
		
		$('#cliente').attr('disabled',true);
		$('#rif').attr('disabled',true);
		$('#nac').attr('disabled',true);
		$('#tipo').attr('disabled',true);
		$('#vendedor').attr('disabled',true);
		
	};		

	//--------------------------------------------------------------------------------------------
	//METODO QUE PERMITE CANCELAR OPERACION Y DESBLOQUEA LOS PRODUCTOS BLOIQUEADOS A FACTURAR 
	//--------------------------------------------------------------------------------------------
	$('#cancelar').click(function(){
		
		if(liberarProducto())
		{
			//alert("cancelado");
			location.reload();	
		}else
			{
				alert("Error cancelando operacion .....");
			}
	});
	
	$('#guardar').click(function(){
		
		setDatos();
		
	});	
	//--------------------------------------------------------------------------------------------
	//METODO QUE ENVIA DATOS DEL FORMULARIO
	//--------------------------------------------------------------------------------------------
	var  setDatos = function(){
		var msj = "0";
		if($('#cliente').val()==0)
		{
                    alert('Factura sin Cliente ***');
                    document.getElementById('cliente').focus();
            
                    return;
			
		}else{
			if($('#tipo').val()=='')
			{
                            alert('Seleccione el tipo de Factura ***');
		            document.getElementById('tipo_doc').focus();	            				
			}else
                            {
				if($('#vendedor').val()=='')
				{
                                    alert('Seleccione Vendedor ***');
				    document.getElementById('vendedor').focus();				         
				}else
                                    {
					if(confirm("¿Realmente desea guardar la nueva Factura ?"))
			                {
                                            if(msj = prompt("Introduzca el numero de Control de Factura"))
			                    {
			                    	$("#control").val(msj);
			                    	$("#form_factura").submit();	
			                    }			                        			                        
			                }
			                else{
			                    	liberarProducto();
			                    	location.reload();	
			                    }            
								
                                    }
                            }				
                    }
		
		
		
		
		
	};
 });

