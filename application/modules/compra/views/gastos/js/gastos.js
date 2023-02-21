$(document).ready(function(){
 	
	//----------------------------------------------------------------------------------------------------
 	//FUNCION QUE BUSCA INFORMACION DE UN PROVEEDOR POR MEDIO DE SU RIF
 	//----------------------------------------------------------------------------------------------------
     var getProveedor = function(){
         $.post('/compra/gastos/cargarProveedor/','valor=' + $("#rif").val(),function(datos){
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
        $.post('/globalAdm/compra/gastos/buscarDocProveedor/','prv=' + $("#id_proveedor").val()+'&tdoc='+$("#tdoc").val()+'&ndoc='+$("#ndoc").val(),function(datos){
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
                $.post('/globalAdm/archivo/proveedor/comprobarCorreo/','correo=' + $("#correo").val() ,function(datos){
                if(datos.total >0)
                {
                    alert("Este correo electronico ya esta en uso...");
                    document.getElementById('correo').value="";
                    document.getElementById('correo').focus();
                }
            },'json');
        } 
    };
	
	
	//-------------------------------------------------------------------------------------------------
	//FUNCION QUE BUSCA LAS COMPRA DE EL PROVEEDOR PASADO EN UN PERIODO
    //-------------------------------------------------------------------------------------------------
	var getBusqueda = function(){
        $.post('/compra/gastos/buscarCpraProveedor/','prv='+$('#prov').val(),function(datos){
           if(datos.length > 0)     
           {
               $('#table_compra tbody').html("");
                 var tabla = '';
                     // tabla += '<table class="table  table-bordered">';
                     // tabla += '<tr>';
                     // tabla += '<td class="cabecera" width="90">Codigo</td>';
					 // tabla += '<td class="cabecera" width="90">Fecha</td>';
                     // tabla += '<td class="cabecera" width="250">Proveedor</td>';
                     // tabla += '<td class="cabecera" width="40">Monto</td>';
                     // //tabla += '<td class="cabecera" width="110"></td>';  
                     // tabla += '</tr>';
                
                
                var tr = '';    
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center">'+datos[i].id_compra+'</td><td>'+datos[i].fecha_creacion+'</td><td>'+datos[i].razon_social_proveedor+'</td><td></td><td><button class="cerrar" id="'+datos[i].id_compra+'" data-dismiss="modal" value="'+datos[i].id_compra+'"><i class="fa fa-arrow-down"></i></button></td>';
                    tr += '</tr>';
                }
                    
                tabla += tr;
                //tabla += '</table>';
                
                $('#table_compra tbody').append(tabla);
           }else
           {
               
           }

            },'json');

    };
	
    var eliminar = function(ref){
    	$.post('/compras/gastos/anularGasto/','valor='+ref, function (filas){
                    document.location.reload();
                },'json');
     };
    
    //-----------------------------------------------------------------------
    //METODO QUE PASA PARAMETRO DE LA COMPRA AL CERRAR VENTANA DE BUSQUEDA DE COMPRA
    //-----------------------------------------------------------------------
	$(document).on('click','.cerrar',function(e){
	var  valor = this.value;
	   
		$('#compra').val(valor);
		//$('#add').attr('disabled',false);	

    });
    
    //-----------------------------------------------------------------------
    //METODO QUE PASA PARAMETRO GASTO AL CERRAR VENTANA
    //-----------------------------------------------------------------------
	$(document).on('click','.cerrar_prod',function(e){
		var  valor = this.value;
		var id = $('#id_fila').val(); 
	    //alert(id);
	        $.ajax( {  
					url: '/compra/gastos/buscarTgastoCod/',
					type: 'POST',
					dataType : 'json',
					async: false,
					data: 'cod='+valor,
					success:function(datos){
						
								if(datos)
								{
									//alert(id);
									$('#codigo'+id).val(datos.id_tipo_gasto);
									$('#id'+id).val(datos.id_tipo_gasto);
									$('#descripcion'+id).val(datos.nombre_tipo_gasto+'('+datos.comentario_tipo_gasto+')');
									$('#precio'+id).val(0.0);
									//$('#producto'+id).val(datos.nombre_producto);
									
									$('#cantidad'+id).val(00);
									
	
								}
	
							},
					error: function(xhr, status) {
							alert('Disculpe, existiÃ³ un problema');
							}
	                });			

    });
    
    
    //-----------------------------------------------------------------------------------------
    //
    //-----------------------------------------------------------------------------------------
	
	$(".eliminar").click(function(e){
        var li = this.value;
        if(confirm("Realmente desea eliminar el gasto Nº "+li)){
        eliminar(li.value);
        
        }
    });
	
	//===========================================================================
    // FUNCION QUE ME PERMITE CREAR DE FORMA DINAMICA UNA FILA EN LA TABLA CARGA LOS IMPUESTOS EN COMBO
    //===========================================================================
    // $("#agregar").on('click', function(){
    	
    	

		 // var count = $('#tabla >tbody >tr').length;
		 // var idAct= count + 1;
		 
		 
		 // $.ajax( {  
				// url: '/archivo/impuesto/cargarImpuesto/',
				// type: 'POST',
				// dataType : 'json',
				// async: false,
				// data: 'cod=0',
				// success:function(datos){
					// if(datos.length > 0)     
           			// {
           				
           					 // var option = "";
       					 	 // for(i = 0; i < datos.length;i++)
       						 // {
       							// option = option + "<option value='"+datos[i].tasa_impuesto+"'>"+datos[i].nombre_impuesto+' ( '+datos[i].tasa_impuesto+" )</option>"; 	
       							
       						 // }
			 
							 // var nuevaFila="<tr>";
							 // //nuevaFila=nuevaFila+"<td><button type='button' id='"+idAct+"' class='openModal ' data-id='"+idAct+"' data-toggle='modal' data-target='#myModal'  ><i class='fa fa-search-plus'></i></button></td>";
							 // nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idAct+"' size='5' data-id='"+idAct+"'  class='form-control input-sm codigo'  /></td>";
							 // nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idAct+"' class='form-control input-sm' readOnly='true' /><input type='hidden' name='id[]' id='id"+idAct+"'  /></td>";
							 // nuevaFila=nuevaFila+"<td><input name='precio[]' type='text' id='precio"+idAct+"'  class='form-control input-sm text-right' /></td>";
							 // nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idAct+"' class='form-control input-sm num_deci text-right' /></td>";
							 // nuevaFila=nuevaFila+"<td><input type='text' name='monto[]' id='monto"+idAct+"' class='form-control input-sm num_deci text-right' /></td>";
							 // nuevaFila=nuevaFila+"<td><select  name='tsa_iva[]' id='tsa_iva"+idAct+"' class='form-control input-sm num_deci calculo text-right' data-id='"+idAct+"' >"+option+"</select></td>";
							 // nuevaFila=nuevaFila+"<td><input type='text' name='monto_iva[]' id='monto_iva"+idAct+"' class='form-control input-sm num_deci text-right' /></td>";
							 // nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idAct+"' class='form-control input-sm num_deci text-right' /></td>";
							 // nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar'><i class='fa fa-close'></i></button></td>";
							 // nuevaFila=nuevaFila+"</tr>";
							 // $("#tabla tbody").append(nuevaFila);    
							 
							 // $('#guardar').attr('disabled',false);     
	 				
	 				// }    
				// },
				// error: function(xhr, status) {
						// alert('Disculpe, existió un problema');
						// }
			// });	
        
     // });
    //--------------------------------------------------------------------------------------
    //METODO QUE ABRE VENTANA DE  TIPO DE GASTO
    //-------------------------------------------------------------------------------------- 
	
	$(document).on("click", ".openModal", function () {
       var valor = $(this).data('id');
        $('#id_fila').val(valor);
	
	});
	
   	//---------------------------------------------------------------------------------------
   	//METODO QUE ACTIVA BUSQUEDA DE TIPO DE GASTO
   	//---------------------------------------------------------------------------------------
   	$(document).on('keyup','#gasto',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getGasto(pro);
       }
   });
   
   
   //-------------------------------------------------------------------------
	//METODO QUE CARGA FORMULARIO DE BUSQUEDA DE COMPRAS POR Proveedor 
	//------------------------------------------------------------------------
	$('#bto_compra').click(function(){
		var prv = $("#proveedor").val();
		var ndoc= $("#factura").val();
		var tdoc= $("#tdoc").val();
		
		$("#cpraModal").modal();
		//getRecepcion(prv,tdoc,ndoc);
		
        return false;
		
	});
   
   //------------------------------------------------------------------------
	//ACTIVA O DESACTIVA BOTON DE compra
	//------------------------------------------------------------------------
	$('#act_pedido').click(function(){
		if($('#act_pedido').prop('checked'))
		{
			$('#compra').attr('disabled',false);
			$('#bto_compra').attr('disabled',false);
			//$('#agregar').attr('disabled',true);
		}else
		{
			$('#compra').attr('disabled',true);
			$('#bto_compra').attr('disabled',true);
			//$('#agregar').attr('disabled',false);
		}	
		
	});
   
   
	
   
	
	//---------------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------------
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
				 $("#tgasto").focus();
                 //alert("Pulsaste f5");
                 return false;
             }
         });
        
    //---------------------------------------------------------------------------------------------
	//METODO DE AGREGACION DE NUEVO GASTO
	//---------------------------------------------------------------------------------------------
	
	$(document).on('keyup','#prov',function(){
       var pro = this.value; 
       if(pro.length >4)
       {
           getBusqueda();
       }
   });
	
	
    $('#limpiar').click(function(){
            location.reload();
            
        });
		
	
	var activarCampos = function(){
		
		$('#rif').attr('disabled',false);	
		$('#tdoc').attr('disabled',false);
		$('#ndoc').attr('disabled',false);
		$('#emision').attr('disabled',false);
		$('#vencimiento').attr('disabled',false);
		$('#compra').attr('disabled',false);
		$('#comentario').attr('disabled',false);
		
	};
	var desactivarCampos = function(){
		$('#rif').attr('disabled',true);		
		$('#tdoc').attr('disabled',true);
		$('#ndoc').attr('disabled',true);
		$('#emision').attr('disabled',true);
		$('#vencimiento').attr('disabled',true);
		$('#compra').attr('disabled',true);
		$('#comentario').attr('disabled',true);
		
	};
		
	//-----------------------------------------------------------------------------
	//funcion que desencadena la activacion de los campos
	//-----------------------------------------------------------------------------	
	$('#unidad').change(function(){
		activarCampos();			
	});	
		
	//----------------------------------------------------------------------
	//METODO QUE CARGA INFORMACION DEL PROVEEDOR 
	//----------------------------------------------------------------------
	$('#rif').change(function(){
		
             getProveedor();
             //var cli = $('#cliente').val();
             //var tot = $('#subtotal').val();        

    });
	
	
	$('#act_cpra').click(function(){
		
		if($('#act_cpra').prop('checked'))
		{
			$('#compra').attr('disabled',false);
			$('#bto_cpra').attr('disabled',false);
		}else
		{
			$('#compra').attr('disabled',true);
			$('#bto_cpra').attr('disabled',true);
		}	
		
	});

	$('#guardar').click(function(){
		$(this).attr('disabled',true);
		setDatos();		
	});
	
	
	//---------------------------------------------------------------------------
	//CARGA LISTADO DE COMPRAS REGISTRADAS PARA UN PROVEEDOR
	//---------------------------------------------------------------------------
	$(document).on('click','#bto_cpra',function(){
		$("#myModal").modal();
		
	});
	
	
	$(document).on('click','.buscar',function(){
		
		
		var myDNI = $(this).data('id');
		
		buscarGasto(myDNI);
		$("#busqueda").modal();
		
	});
	
	//-------------------------------------------------------------------------------------------------------
	//METODO QUE PERMITE AGREGAR DETALLE DE GASTOS EN EL FORMULARIO
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
	
	
	var buscarGasto = function(valor){
		
		$.ajax( {  
				url: '/compra/gastos/buscarGasto/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'cod='+valor,
				success:function(datos){
					$("#tabla tbody").html('');
                            $('#enlace').html('');
                            if(datos)
                            {
                            	var id = datos[0].id_gasto;
                            	
								var total = 0;
								var subtotal=0;
								var iva = 0;
								
								
                                $('#nro').val(datos[0].id_gasto);
                                $('#emision').val(datos[0].fecha_emision_gasto);
                                $('#vencimiento').val(datos[0].fecha_vencimiento_gasto);
                                $('#proveedor').val(datos[0].razon_social_proveedor);
                                
                                for(i= 0;i < datos.length;i++ )
                                {
                                    var nuevaFila="<tr>";
                                    nuevaFila=nuevaFila+"<td>"+datos[i].id_gasto+"</td>";
                                    nuevaFila=nuevaFila+"<td>"+datos[i].nombre_tipo_gasto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].precio_tgasto+"</td>";
                                   // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].cantidad_tgasto+"</td>";
									nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].mto_tgasto+"</td>";
									nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].tsa_iva_tgasto+"</td>";
									nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].mto_total_tgasto+"</td>";
                                    nuevaFila=nuevaFila+"</tr>";
                                    $("#tabla tbody").append(nuevaFila); 

									
                                    
									total = total + parseFloat(datos[i].mto_tgasto);
									subtotal = subtotal + parseFloat(datos[i].precio_tgasto * datos[i].cantidad_tgasto);
									iva = iva + parseFloat(datos[i].mto_iva_tgasto);
                                } 
                                
															
								
								$('#subtotal').val(subtotal);
								$('#iva').val(iva);
								$('#total').val(total);
								var url = '<a  target="_blank" href="/globalAdm/reporte/index/impOC/'+id+'" title="Imprimir Orden de Compra"><button class="btn btn-default"><i class="fa fa-print"></i></button></a>';
								$('#enlace').append(url);
                                  
                            }else
                            {
                                return true;
                            }  
				},
				error: function(xhr, status) {
						alert('Disculpe, existió un problema');
						}
			});
	};
	
	
	//-------------------------------------------------------------------------------------------------------
	// FUNCION QUE CARGA LOS DETALLES DEL GASTO DE LA TABLA TIPO DE GASTO
	//-------------------------------------------------------------------------------------------------------
	 var getGasto  = function(valor){
		
       $.ajax( {  
				url: '/compra/gastos/buscarTgasto1/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'cod='+valor,
				success:function(datos){
					if(datos.length > 0)     
           			{
						$('#table_concepto_pro').html("");
		                var tabla = '';
		                     tabla += '<table class="table  table-bordered">';
		                     tabla += '<thead><tr>';
		                     tabla += '<td class="cabecera" width="10"></td>';
		                     tabla += '<td class="cabecera" width="90">Codigo.</td>';
		                     tabla += '<td class="cabecera" width="350">Descripcion del Gasto</td>';
		                     tabla += '<td class="cabecera" width="40">Condicion</td>';
		                     //tabla += '<td class="cabecera" width="110"></td>';  
		                     tabla += '</tr></thead>';
		                
		                
		                var tr = '';    
		                for (i = 0; i < datos.length; i++){
		                    tr += '<tr>';
		                    tr += '<td align="center"><td>'+datos[i].id_tipo_gasto+'</td><td>'+datos[i].nombre_tipo_gasto+'</td><td>'+datos[i].condicion_tipo_gasto+'</td><td><button class="cerrar_prod btn btn-default" data-dismiss="modal" value="'+datos[i].id_tipo_gasto+'"><i class="fa fa-arrow-down"></i></button></td>';
		                    tr += '</tr>';
		                }
		                    
		                tabla += tr;
		                tabla += '</table>';
		                
		                $('#table_concepto_pro').html( tabla );
		            }    
				},
				error: function(xhr, status) {
						alert('Disculpe, existió un problema');
						}
			});	
    };
	
	
	//----------------------------------------------------------------------------------
	//METODO QUE ENVIA LOS DATOS DEL FORMULARIO
	//----------------------------------------------------------------------------------
	var setDatos = function(){
		var msj = "0";
		
		
		
		if($('#id_proveedor').val()==0)
		{
			alert('Gasto sin Poroveedor ***');
            document.getElementById('proveedor').focus();
             $('#guardar').attr('disabled',false);
            return;
			
		}else{
			
			if($('#tdoc').val()=="")
			{
				alert('Seleccione el Tipo de Documento de Origen ***');
            	document.getElementById('tdoc').focus();
				 $('#guardar').attr('disabled',false);
           		return;
				
			}else
				{
					if($('#ndoc').val()=="")
					{
						alert('Introduzca Nro de Documento   ***');
            			document.getElementById('ndoc').focus();
						 $('#guardar').attr('disabled',false);
            			return;
						
					}else
						{
							if($('#emision').val()=="")
							{
								alert('Introduzca la fecha de Emision del Documento ***');
            					document.getElementById('emision').focus();
								 $('#guardar').attr('disabled',false);
            					return;
								
							}else
								{
									if($('#vencimiento').val()=="")
									{
										alert('Introduzca la fecha de vencimiento del Documento ***');
		            					document.getElementById('vencimiento').focus();
										 $('#guardar').attr('disabled',false);
		            					return;
										
									}else
										{
											if($('#compra').val()=="")
											{
												alert('Introduzca El numero de la Compra a Relacionar ***');
				            					document.getElementById('compra').focus();
												 $('#guardar').attr('disabled',false);
				            					return;
												
											}else
												{
													var count = $('#tabla >tbody >tr').length;
													
													
													if(count > 0 && $('#total').val()>0)													
														$('#form_gasto').submit();
													else
														{
															alert('Introduzca el detalle de los Gastos a Relacionar ***');
				            								 $('#guardar').attr('disabled',false);
				            								document.getElementById('add').focus();
				            
				            								return;	
															
														}
												}
										}
									
								}
							
						}
				}
			
		}
		
		
	};
	
	
	
	
	//----------------------------------------------------------------
	//METODO QUE RECALCULA LOS MONTOS DEL DETALLE
	//----------------------------------------------------------------
	
	$(document).on('blur','.calculo',function(){
		var id = $(this).data('id');		
		var pre =0;
		var cant=0;
		var tiva = 0;
		var mto =0;
		var mto_total =0;
		var acu_mto=0;
		var acu_iva=0;
		var acu_total=0;	
		
		//alert(id);
		cant = parseFloat($('#cantidad'+id).val());
		pre = parseFloat($('#precio'+id).val());
		tiva = parseFloat($('#tsa_iva'+id).val());
		
		if(cant > 0 && pre > 0)
		{
			mto = pre * cant;
		}
		if(tiva > 0)
		{
			mto_total = mto * ((tiva/100)+1);			
		}

		//$('#total'+id).val(parseFloat(mto_total).toFixed(2));
		
		
		var count = $('#tabla >tbody >tr').length;
		for(i=1;i <= count;i++)
		{
			
			acu_mto   = acu_mto + parseFloat($('#precio'+i).val()) * parseFloat($('#cantidad'+i).val()) ;
			acu_iva   = acu_iva + ((acu_mto * ((tiva/100)+1)) - acu_mto);  
			acu_total = acu_total + (acu_mto + acu_iva);	
		}		
			$('#total').val(acu_total.toFixed(2));
			$('#subtotal').val(parseFloat(acu_mto).toFixed(2));
			$('#total_iva').val(acu_iva.toFixed(2));
		
	});	
	
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

