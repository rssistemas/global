$(document).ready(function(){
 	
	
	//----------------------------------------------------------------------------------------------------
 	//FUNCION QUE BUSCA INFORMACION DE UN PROVEEDOR POR MEDIO DE SU RIF
 	//----------------------------------------------------------------------------------------------------
     var getProveedor = function(){
         $.post('/compra/ordencompra/cargarProveedor/','valor=' + $("#rif").val(),function(datos){
		         if( datos )
		         {
		             // $('#rif').html('');
		             //$('#razon_social').val('');
		             $('#proveedor').val('0');
					
		             //$('#rif').val(datos[0].rif_cliente);
		             $('#razon_social').val(datos.razon_social_proveedor);
		             $('#proveedor').val(datos.id_proveedor);
		             $('#guardar').attr('disabled',false);
		             $('#agregar').attr('disabled',false);
		             //getDescuento(datos[0].id_cliente,0);
		             
		         }else
		         {
		             alert("Proveedor no registrado .............");
		             $('#rif').val("");
		             $('#razon_social').val('');
		             $('#id_proveedor').val('0');
		             $('#rif').focus();
		         }

             },'json');
         };
	
	
	//------------------------------------------------------------------------------------------
	//FUNCION QUE COMPRUEBA EXISTENCIA DE NRO DE DOCUMENTO DE PROVEEDOR
	//------------------------------------------------------------------------------------------
    var getDocumento = function(){
        $.post('/compra/gastos/buscarDocProveedor/','prv=' + $("#id_proveedor").val()+'&tdoc='+$("#tdoc").val()+'&ndoc='+$("#ndoc").val(),function(datos){
        if(datos.total > 0 )
        {
			alert(".... Factura Existente ......");
            $('#ndoc').val("");	
			$('#ndoc').focus();	
        }else
			{
				
				$('#emision').focus();
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
	
	
	//---------------------------------------------------------------------------------------------
	//FUNCION QUE CARGA LOS DEPOSITOS DE UNA UNIDAD OPERATIVA DEPENDE DEL CONTROLADOR COMPRAS
	//---------------------------------------------------------------------------------------------
	var getDeposito = function(){
    	
    	$.post('/compra/compras/cargarDeposito/','valor=' + $("#unidad").val(),function(datos){
         if(datos.length > 0)
         {
	            $("#almacen").html('');
	        	$('#almacen').append('<option value="" >-Seleccione-</option>');
	        	var cadena="";   
	        	for(i=0;i < datos.length;i++)
	        	{
	        		cadena = datos[i].nombre_deposito.toUpperCase();
	        		$("#almacen").append("<option value='"+datos[i].id_deposito+"'>"+cadena+"</option>");	
	        	}
             
             
         }

     	},'json');
    	
    };
	
	
	 //-------------------------------------------------------------------------------------------
	 //FUNCION PARA BUSQUEDA DIRECTO DE PRODUCTOS
	 //-------------------------------------------------------------------------------------------
    var getBusqueda = function(valor){
        $.post('/compra/compras/cargarProducto/','valor='+valor,function(datos){
           if(datos.length)     
           {
               var id = $('id_fila').val();
               $('#table_concepto').html("");
                var tabla = '';
                     tabla += '<table class="table  table-bordered">';
                     tabla += '<tr>';
                     tabla += '<td class="cabecera" width="10"></td>';
                     tabla += '<td class="cabecera" width="90">Codigo.</td>';
                     tabla += '<td class="cabecera" width="350">Nombre</td>';
                     tabla += '<td class="cabecera" width="40"></td>';
                     //tabla += '<td class="cabecera" width="110"></td>';  
                     tabla += '</tr>';
                
                
                var tr = '';    
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center"><td>'+datos[i].codigo_producto+'</td><td>'+datos[i].nombre_producto+'</td><td><button class="carga" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_det_producto+'"><i class="fa fa-arrow-down"></i></button></td>';
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

    
	//---------------------------------------------------------------------------------------
    //METODO QUE SE ACTIVA CUANDO SE CIERRA LA VENTANA EMERGENTE DE RECEPCIONES
    //---------------------------------------------------------------------------------------
	$(document).on('click','.cerrar',function(e){
	var  valor = $('#nro').val();
	//var id = $('#id_fila').val(); 
   // alert(id);
        $.ajax( {  
				url: '/compra/compras/buscarRecepcion/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'codigo='+valor,
				success:function(datos){
							if(datos)
							{
								var total = 0;
								var subtotal = 0;
								var iva = 0;
								var recepcion = datos[0].id_recepcion;
								
								for(i=0;i < datos.length;i++)
								{   
										
									var count = $('#tabla >tbody >tr').length;
									var idPrd= count +1;	
									
									
									var nuevaFila="<tr>";								
									nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value='"+datos[i].codigo_producto+"' readOnly='true'  class='form-control  codigo' /></td>";
									nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' value='"+datos[i].nombre_producto+"' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='"+datos[i].id_det_producto+"'  /></td>";
									nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control  text-right' value='"+datos[i].precio_producto+"'  /></td>";            
									nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"'  class='form-control  text-right' value='"+datos[i].cantidad_producto+"'  /></td>";						
									nuevaFila=nuevaFila+"<td><input type='text' name='monto[]'  id='monto"+idPrd+"' data-id='"+idPrd+"' class='form-control calculo text-right'  value='"+datos[i].monto_producto+"' /></td>";
									nuevaFila=nuevaFila+"<td><input type='text' name='iva[]'  id='iva"+idPrd+"' data-id='"+idPrd+"' class='form-control calculo text-right'  value='"+datos[i].mto_iva_producto+"' /></td>";								
									nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value='"+datos[i].total_producto+"'  /></td>";						
						
									nuevaFila=nuevaFila+"</tr>";
									
									
									
									$("#tabla >tbody").append(nuevaFila);   
									
									total = total + parseFloat(datos[i].total_producto);
									subtotal = subtotal + parseFloat(datos[i].monto_producto);
									iva = iva + parseFloat(datos[i].mto_iva_producto);	
									
								};
								
								$('#total').val(total);
								$('#subtotal').val(subtotal);
								$('#iva').val(iva);
								
								$('#recepcion').val(recepcion);
								
								$('#agregar').attr('disabled',true);

							}

						},
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });

    });


	//-----------------------------------------------------------------------------------------------------
    //METODO QUE SE EJECUTA MEDIANTE CLASE .CARGA PRODUCTO SEGUN ID 
    //-----------------------------------------------------------------------------------------------------
	$(document).on('click','.carga',function(e){
	//var deposito = $(this).data('dep');
	var valor    = this.value;
	var id       = $('#id_fila').val(); 
	//var stock    = $(this).data('stock');
	var disponible  = 0;
   // alert(id);
        $.ajax( {  
				url: '/compra/compras/buscarProducto/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'valor='+valor,
				success:function(datos){
							if(datos)
							{
															
								$('#codigo'+id).val(datos.codigo_producto);
								$('#descripcion'+id).val(datos.nombre_producto+'('+datos.nombre_prentacion+')');
								$('#precio'+id).val(00);
								//$('#disponible'+id).val(disponible);
								$('#cantidad'+id).val(00);
								$('#id'+id).val(datos.id_det_producto);
								//$('#stock'+id).val(stock);

							}

						},
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });

    });
	
	
   //-----------------------------------------------------------------------------
   // METODO DE AUTOCOMPLETACION PARA LA BUSQUEDA DE GASTO
   //-----------------------------------------------------------------------------
    $("#producto").autocomplete({
        source: '/compra/ordencompra/buscarProductoCatalogo/', /* este es el script que realiza la busqueda */
        minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
        select:productoSeleccionado,
        focus: productoFoco
    });    
    function productoFoco(event, ui)
    {   
		var concepto = "";
		concepto = ui.item.label;
        $("#producto").val(concepto);
        
        return false;
    }
    // ocurre cuando se selecciona un producto de la lista
    function productoSeleccionado(event, ui)
    {
        //recupera la informacion del producto seleccionado
        var tGasto = ui.item.value;        
        var id = tGasto.id;

        //actualizamos los datos en el formulario
        
        $("#id_producto").val(id);
        
        $("#producto").val(tGasto.producto);
        return false;
    }
	
	
   //-----------------------------------------------------------------------------
   //metodos de autocompletado de proveedor
   //-----------------------------------------------------------------------------
    $("#proveedor").autocomplete({
        source: '/almacen/recepcion/buscarProveedor/', /* este es el script que realiza la busqueda */
        minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
        select:proveedorSeleccionado,
        focus: proveedorFoco
    });    
    
	function proveedorFoco(event, ui)
    {   
        $("#proveedor").val(ui.item.razon_social);
        
        return false;
    }
	
    // ocurre cuando se selecciona un producto de la lista
    function proveedorSeleccionado(event, ui)
    {
        //recupera la informacion del producto seleccionado
        var proveedor = ui.item.value;        
        var id = proveedor.id;

        //actualizamos los datos en el formulario
        
        $("#id_proveedor").val(id);
        
        $("#proveedor").val(proveedor.nombre);
        return false;
    }
	

    $('#correo').change(function(){

        if(!$('#correo').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getCorreo();

    });
    

	
    //----------------------------------------------------------------------
    //METODO QUE DESPLIEGA VENTANA CUANDO PRESIONAS F4
    //----------------------------------------------------------------------
           
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
				$("#producto").focus();
                //alert("Pulsaste f5");
                return false;
            }
        });

        
    //---------------------------------------------------------------------------------------------
	//
	//---------------------------------------------------------------------------------------------
	$(document).on("click",".editar",function(){
       var valor = $(this).data('id');
       //var valor = this.value;   
            $.ajax( {  
                    url: '/compra/ordencompra/buscarOrdenCompra/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'codigo='+valor,
                    success:function(datos){
                            $("#tabla tbody").html('');
                            $('#enlace').html('');
                                if(datos.length >0)
                                {
                                    var total = 0;
									var subtotal=0;
									var iva = 0;
									var id = datos[0].id_orden_compra;
									$('#nro').val(datos[0].id_orden_compra);
                                    $('#fecha').val(datos[0].fecha_creacion);
                                    $('#proveedor').val(datos[0].razon_social_proveedor);
                                    
									for(i= 0;i < datos.length;i++ )
                                    {
                                        var nuevaFila="<tr>";
                                        nuevaFila=nuevaFila+"<td>"+datos[i].codigo_producto+"</td>";
                                        nuevaFila=nuevaFila+"<td>"+datos[i].nombre_producto+"</td>";
                                        nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].precio+"</td>";
                                       // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
                                        nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].cantidad+"</td>";
										nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].monto+"</td>";
										nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].tasa_impuesto+"</td>";
										nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].total+"</td>";
                                        nuevaFila=nuevaFila+"</tr>";
                                        $("#tabla tbody").append(nuevaFila); 

										total = total + parseFloat(datos[i].total);
										subtotal = subtotal + parseFloat(datos[i].monto);
										iva = iva + parseFloat(datos[i].impuesto);		
                                        
                                    }   
									
									$('#subtotal').val(subtotal);
									$('#iva').val(iva);
									$('#total').val(total);
									var url = '<a class="navbar-brand" target="_blank" href="/globalAdm/reporte/index/impOC/'+id+'" title="Imprimir Orden de Compra"><button><i class="fa fa-print"></i></button></a>';
									$('#enlace').append(url);
                                }else
                                {
                                    return true;
                                }

                            },
                    error: function(xhr, status) {
                            alert('Disculpe, existiÃ³ un problema');
                            }
                });
        
   });
	
	
    $('#limpiar').click(function(){
        location.reload();        
     });
	//------------------------------------------------------------------------
	//ACTIVA O DESACTIVA BOTON DE PEDIDO
	//------------------------------------------------------------------------
	$('#act_pedido').click(function(){
		if($('#act_pedido').prop('checked'))
		{
			$('#pedido').attr('disabled',false);
			$('#bto_presupuesto').attr('disabled',false);
			$('#agregar').attr('disabled',true);
		}else
		{
			$('#pedido').attr('disabled',true);
			$('#presupuesto').attr('disabled',true);
			$('#agregar').attr('disabled',false);
		}	
		
	});	
	
	$('#guardar').click(function(){
		setDatos();		
	});
	
	var activarCampos = function(){
		
		$('#proveedor').attr('disabled',false);
		$('#rif').attr('disabled',false);
		$('#tipo').attr('disabled',false);
		$('#factura').attr('disabled',false);
		$('#emision').attr('disabled',false);
		$('#vencimiento').attr('disabled',false);
		//$('#recepcion').attr('disabled',false);
		$('#nac').attr('disabled',false);
		
	};
	var desactivarCampos = function(){
		$('#proveedor').attr('disabled',true);
		$('#rif').attr('disabled',true);
		$('#tipo').attr('disabled',true);
		$('#factura').attr('disabled',true);
		$('#emision').attr('disabled',true);
		$('#vencimiento').attr('disabled',true);
		//$('#recepcion').attr('disabled',true);
		$('#nac').attr('disabled',true);
		
	};	
		
	$('#unidad').change(function(){
		getDeposito();
		activarCampos();			
	});	
		
	
	//----------------------------------------------------------------------
	//METODO QUE CARGA INFORMACION DEL PROVEEDOR 
	//----------------------------------------------------------------------
	$('#rif').change(function(){		
        getProveedor();            
    });
	
	//------------------------------------------------------------------------------
    //METODO QUE ACTIVA LA BUSQUEDA DE PRODUCTO POR NOMBRE 
    //------------------------------------------------------------------------------ 
        
    $(document).on('keyup','#producto',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
   });
   
	
	$( "#emision" ).datepicker({
		changeMonth: true,
		changeYear: true
	});
	
	$("#emision").datepicker("option", "dateFormat","yy-mm-dd");
	
	$("#vencimiento").datepicker({ minDate: 0, maxDate: "+1M" },"option", "dateFormat","yy-mm-dd");
	
	$("#vencimiento").datepicker("option", "dateFormat","yy-mm-dd");
	
	//--------------------------------------------------------------------------------------------------
	//
	//---------------------------------------------------------------------------------------------------
	$(document).on('click','#add',function(){
		var gto = $('#id_producto').val(); 
		getProducto(gto);
		$('#producto').val("");
		$('#id_producto').val(0); 
		
	});	
	
	
	//-------------------------------------------------------------------------------------------------------
	// FUNCION QUE CARGA LOS DETALLES DEL GASTO DE LA TABLA TIPO DEW GASTO
	//-------------------------------------------------------------------------------------------------------
	 var getProducto  = function(valor){
		
       $.ajax( {  
				url: '/compra/ordencompra/buscarProducto/',
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
	
	
	//--------------------------------------------------------------------------------------------
	//METODO QUE ENVIA DATOS DEL FORMULARIO
	//--------------------------------------------------------------------------------------------
	var  setDatos = function(){
		var msj = "0";
		
		$('#guardar').attr('disabled',true);
		
		if($('#proveedor').val()==0)
		{
			alert('Factura sin Poroveedor ***');
			$('#guardar').attr('disabled',false);
            document.getElementById('cliente').focus();
            
            return;
			
		}else{
				if($('#tipo').val()=='')
				{
					alert('Seleccione el Tipo de Compra ***');
					$('#guardar').attr('disabled',false);
		            document.getElementById('tipo').focus();	            				
				}else
					{
						//if($('#presupuesto').val()== 0)
						//{
						//	alert('Introduzca el Nº de Presupuesto de Compra ***');
				        //   document.getElementById('vendedor').focus();				         
						//}else
						//	{
								if(confirm("¿Realmente desea guardar la nueva Orden de Compra ?"))
			                    {
			                    	//if(msj = prompt("Introduzca el numero de Control de Factura de Compra"))
			                    	//{
			                    		//$("#control").val(msj);
			                    		$("#form_compra").submit();	
			                    	//}			                        			                        
			                    }
			                    else{
			                    	//liberarProducto();
			                    	//location.reload();
									$('#guardar').attr('disabled',false);		
			                    }            
								
						//	}
					}				
			}
		
	};	
		
	
	//-------------------------------------------------------------------------------------------------------
	//METODO QUE PERMITE AGREGAR DETALLE DE FACTURA EN EL FORMULARIO
	//-------------------------------------------------------------------------------------------------------
	
     $(document).on('click',"#agregar",function(){
        
             var count = $('#tabla >tbody >tr').length;
             var idPrd= count +1;
              
             var nuevaFila="<tr>";								
			 nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value=''   class='form-control  codigo' /></td>";
			 nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' value='' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='0'  /></td>";
			 nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"' data-id='"+idPrd+"'  class='form-control  text-right' value='0'  /></td>";            
			 nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"' data-id='"+idPrd+"'  class='form-control calcular_cant  text-right' value='0'  /></td>";						
			 nuevaFila=nuevaFila+"<td><input type='text' name='monto[]'  id='monto"+idPrd+"' data-id='"+idPrd+"' class='form-control  text-right'  value='0' readonly='true' /></td>";
			 nuevaFila=nuevaFila+"<td><input type='text' name='iva[]'  id='iva"+idPrd+"' data-id='"+idPrd+"' class='form-control  text-right'  value='0' readonly='true' /></td>";								
			 nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value='0' readonly='true'  /></td>";						

			 nuevaFila=nuevaFila+"</tr>";
             $("#tabla tbody").append(nuevaFila);   
     
     });
	// //----------------------------------------------------------------------------------------------------------
	// //METODO QUE PERMITE RECALCULAR VALORES DE FACTURA
	// //----------------------------------------------------------------------------------------------------------
	// $(document).on('blur','.calculo',function(){
		// var id = $(this).data('id');		
		// var pre =0;
		// var cant=0;
		// var tiva = 0;
		// var mto =0;
		// var mto_total =0;
		// var acu_mto=0;
		// var acu_iva=0;
		// var acu_total=0;	
		
		// //alert(id);
		// cant = parseFloat($('#cantidad'+id).val());
		// pre = parseFloat($('#precio'+id).val());
		// tiva = parseFloat($('#tsa_iva'+id).val());
		
		// if(cant > 0 && pre > 0)
		// {
			// mto = pre * cant;
		// }
		// if(tiva > 0)
		// {
			// mto_total = mto * ((tiva/100)+1);			
		// }

		// $('#total'+id).val(parseFloat(mto_total).toFixed(2));
		
		
		// var count = $('#tabla >tbody >tr').length;
		// for(i=1;i <= count;i++)
		// {
			// acu_total = acu_total + parseFloat($('#total'+i).val());	
			// acu_mto   = acu_mto + parseFloat($('#precio'+i).val()) * parseFloat($('#cantidad'+i).val()) ;
			// acu_iva   = acu_iva + parseFloat($('#total'+i).val()) - (parseFloat($('#precio'+i).val()) * parseFloat($('#cantidad'+i).val()));
		// }		
			// $('#total').val(parseFloat(acu_total).toFixed(2));
			// $('#subtotal').val(parseFloat(acu_mto).toFixed(2));
			// $('#total_iva').val(parseFloat(acu_iva).toFixed(2));
		
	// });	
	
	
	//----------------------------------------------------------------------------------------------------
	//METODO QUE RECALCULA LOS TOTALES DE LA FACTURA  DEL PRODUCTO SELECCIONADO
	//---------------------------------------------------------------------------------------------------
	
	$(document).on('blur','.calcular_cant',function(){
		var fila      = $(this).data('id');
		var val_cant  = $(this).val();
		var val_monto = $('#monto'+fila).val();
		var val_iva   = $('#iva'+fila).val();
		var val_total = $('#total'+fila).val();
		var val_precio= $('#precio'+fila).val();
		
		
		var total = $('#total').val();
		var subtotal = $('#subtotal').val();
		var iva      = $('#iva').val();
		var descuento= $('#descuento').val();
		var tsa_iva  = $('#tsa_iva').val();
		var tsa_desc = $('#tsa_desc').val();		
		var sub_fila = 0;
		var iva_fila = 0; 
		 
 		if(val_cant > 0)
 		{
 						
			subtotal = parseFloat(subtotal) + (val_precio*val_cant);
			
			iva = parseFloat(iva) + ((subtotal * ((parseFloat(tsa_iva)/100) +1)) - subtotal);
			
			total = parseFloat(total) + (subtotal + iva);
			
			$('#subtotal').val(subtotal.toFixed(2));
			$('#iva').val(iva.toFixed(2));
			$('#total').val(total.toFixed(2));
			
			sub_fila = parseFloat(val_precio * val_cant);
			iva_fila =((sub_fila * ((parseFloat(tsa_iva)/100) +1)) - sub_fila);
			
			$('#monto'+fila).val(sub_fila.toFixed(2));
			$('#iva'+fila).val(iva_fila.toFixed(2));
			$('#total'+fila).val(parseFloat(sub_fila + iva_fila).toFixed(2));
			 		
 		}
		 		
		 
		
		
	});
			
 });

 