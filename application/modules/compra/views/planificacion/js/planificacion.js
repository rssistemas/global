$(document).ready(function(){

	//$( document ).tooltip();

	
	//-------------------------------------------------------------------------------------------
	//METODO DE MANIPULACION DE FORMULARIOS
	//-------------------------------------------------------------------------------------------
	//ACTIVA CAMPOS DEL FORMULARIO "PLANIFICACION"
	//-------------------------------------------------------------------------------------------
	var activarCampos = function(){
		
		$('#responsable').attr('disabled',false);
		$('#inicio').attr('disabled',false);
		$('#fin').attr('disabled',false);
		$('#ref_contable').attr('disabled',false);
		$('#objetivo').attr('disabled',false);
		$('#dependencia').attr('disabled',false);
		$('#comentario').attr('disabled',false);
		
		//-------------------------------------------------------------------------------------------
		//CONFIGURACION DE CAMPO TIPO FECHA
		//-------------------------------------------------------------------------------------------
		$("#emision").datepicker({ minDate: 0, maxDate: "+1M" },"option", "dateFormat","yy-mm-dd");
		$("#emision").datepicker("option", "dateFormat","yy-mm-dd");
		
		$("#vencimiento").datepicker({ minDate: 0, maxDate: "+1M" },"option", "dateFormat","yy-mm-dd");
		$("#vencimiento").datepicker("option", "dateFormat","yy-mm-dd");
		
		
	};
	//------------------------------------------------------------------------------------------
	//DESACTIVA CAMPOS DEL FORMULARIO "PLANIFICACION"
	//------------------------------------------------------------------------------------------
	var desactivarCampos = function(){
		$('#responsable').attr('disabled',true);
		$('#inicio').attr('disabled',true);
		$('#fin').attr('disabled',true);
		$('#ref_contable').attr('disabled',true);
		$('#objetivo').attr('disabled',true);
		$('#dependencia').attr('disabled',true);
		$('#comentario').attr('disabled',true);
		
	};
	

	
	//------------------------------------------------------------------------------------------
	//METODO QUE ACTIVA CAMPOS
	//------------------------------------------------------------------------------------------
	$('#unidad').change(function(){
		
		activarCampos();
		$('#guardar').attr('disabled',false);	
	});

	
	
	
	
	//-------------------------------------------------------------------------------------------
	//METODO QUE ACTIVA EL ENVIA DEL FORMULARIO DE PLANIFICACION
	//-------------------------------------------------------------------------------------------
	$('#guardar').click(function(){
		setDatos();		
	});
	
	//--------------------------------------------------------------------------------------------
	//
	//--------------------------------------------------------------------------------------------
	$('#nuevo').click(function(){
		activarCamposDetalle();
		$('#tipo').focus();	
	});
	
	
	//--------------------------------------------------------------------------------------------
	//METODO QUE ENVIA DATOS DEL FORMULARIO
	//--------------------------------------------------------------------------------------------
	var  setDatos = function(){
		var msj = "0";
		
		$('#guardar').attr('disabled',true);
		
		if($('#responsable').val()==0)
		{
			alert('Planificacion sin Responsable ***');
            document.getElementById('cancelar').focus();
            
            return;
			
		}else{
				if($('#inicio').val()=='')
				{
					alert('Seleccione fecha de inicio de planificacion ***');
		            document.getElementById('inicio').focus();	            				
				}else
					{
						if($('#fin').val()== "")
						{
							alert('Seleccione fecha de finalizacion de planificacion');
				            document.getElementById('plazo').focus();				         
						}else
							{
								if(confirm("¿Realmente desea guardar la nueva Planificacion de Compra ?"))
			                    {
			                    	//if(msj = prompt("Introduzca el numero de Control de Factura de Compra"))
			                    	//{
			                    		//$("#control").val(msj);
			                    		$("#form_pln").submit();	
			                    	//}			                        			                        
			                    }
			                    else{
			                    	//liberarProducto();
			                    	//location.reload();	
			                    }            
								
							}
					}				
			}
		
	};
	
	//------------------------------------------------------------------------------------------------
	//METODO 
	//------------------------------------------------------------------------------------------------
	var getDatos = function(valor){
		
			$.ajax( {  
				url: '/compra/planificacion/buscarPlanificacion/',
				type: 'POST',
				dataType : 'json',
				async: true,
				data: 'valor='+valor,
				success:function(datos){
					if(datos)
					{
						
						    var detalle = datos[0].detalle;
							//alert(detalle);
							
							$('#nro').val(datos[0].id);
							$('#inicio').val(datos[0].inicio);
							$('#final').val(datos[0].fin);
							$('#departamento').val(datos[0].dpto.toUpperCase());
							$('#objetivo').val(datos[0].concepto.toUpperCase());
							$('#unidad').val(datos[0].unidad.toUpperCase());		
							
						    var tr = '';    
							for (i = 0; i < datos[0].detalle.length; i++){
								tr += '<tr>';																
								tr += '<td align="center">'+detalle[i].id_det_pln_compra+'<td>'+detalle[i].tipo_requisito.toUpperCase()+'</td><td>'+detalle[i].requisito.toUpperCase()+'</td><td>'+detalle[i].cantidad_requisito+'</td><td>'+detalle[i].prioridad_requisito.toUpperCase()+'</td><td>'+detalle[i].plazo_ejecucion+' dia</td>';
								tr += '</tr>';
							}
								
							
							
							
							$('#tabla_pln >tbody').append(tr);		

					}
				},						
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });
		
		
		
	}
	
	//------------------------------------------------------------------------------------------------
	//METODO QUE CARGA LOS TRABAJADORES DE UN DEPARTAMENTO 
	//------------------------------------------------------------------------------------------------
	var getTrabajadores = function(dpto){
		
		$.post('/compra/planificacion/cargarTrabajadores/','valor=' + dpto,function(datos){
         if(datos.length > 0)
         {
	            $("#responsable").html('');
	        	$('#responsable').append('<option value="" >-Seleccione-</option>');
	        	var cadena="";   
	        	for(i=0;i < datos.length;i++)
	        	{
	        		cadena = datos[i].pri_nombre_persona.toUpperCase()+' '+datos[i].pri_apellido_persona.toUpperCase();
	        		$("#responsable").append("<option value='"+datos[i].id_trabajador+"'>"+cadena+"</option>");	
	        	}
             
             
         }

     	},'json');
		
		
	};
	
	//-------------------------------------------------------------------------------------
	//
	//-------------------------------------------------------------------------------------
	$('#dependencia').change(function(){
		var valor = $(this).val();
		getTrabajadores(valor);
		
	});
	//--------------------------------------------------------------------------------------
	//
	//--------------------------------------------------------------------------------------
	$(document).on('keyup','#busqueda',function(){
		
	   var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
		
		
	});
	
	//---------------------------------------------------------------------------------------
	//
	//---------------------------------------------------------------------------------------
	$(document).on('click','.buscar',function(){
		var valor =  $(this).data('id');
		getDatos(valor);
		
	});
	
	//--------------------------------------------------------------------------------------------
	//METODO QUE CREA NUEVA FILA EN TABLA
	//--------------------------------------------------------------------------------------------
	
	$(document).on('click','#agregar',function(){
		
		var count = $('#tabla >tbody >tr').length;
		var idPrd= count +1;	
		
		var option = "";
			
			option = option + "<option value='BAJA'>Baja</option>"
			option = option + "<option value='MEDIA'>Media</option>"
			option = option + "<option value='ALTA'>Alta</option>"
						
		var nuevaFila="<tr>";								
		nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' data-id='"+idPrd+"' size='10' class='form-control' /></td>";
		nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' readonly='true' /></td>";
		nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"' class='form-control text-right'  /></td>";	
		
		nuevaFila=nuevaFila+"<td><input type='text' name='tipo[]' id='tipo"+idPrd+"' class='form-control' readonly='true'  /></td>";	
		nuevaFila=nuevaFila+"<td><select name='prioridad[]' id='prioridad"+idPrd+"' class='form-control' >"+option+"</select></td>";
		nuevaFila=nuevaFila+"<td><input type='text' name='plazo[]' id='plazo"+idPrd+"' class='form-control text-right '  /></td>";
		nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar' class='btn btn-default borrar'><i class='fa fa-close'></i></button></td>";
		nuevaFila=nuevaFila+"</tr>";
		
		
		
		$("#tabla >tbody").append(nuevaFila);
		
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
				$("#tipo").focus();
                alert(myDNI);
                return false;
            }
        });
     
    //------------------------------------------------------------------------------
	//METODO ODE BUSQUEDA DE REQUISITO(SERVICIO O PRODUCTOS)
	//------------------------------------------------------------------------------
	var getBusqueda = function(valor){
		
		
		 $.ajax( {  
				url: '/compra/planificacion/buscarRequisito/',
				type: 'POST',
				dataType : 'json',
				async: true,
				data: 'valor='+valor+'&tipo='+$('#tipo').val(),
				success:function(datos){
					if(datos)
					{
						   var id = $('#id_fila').val();
						  // alert(id);
						   $('#table_concepto').html("");
							var tabla = '';
								 tabla += '<table class="table  table-bordered">';
								 tabla += '<tr>';
								 tabla += '<td class="cabecera" width="10"></td>';
								 tabla += '<td class="cabecera" width="90">Codigo.</td>';
								 tabla += '<td class="cabecera" width="350">Descripcion</td>';
								 tabla += '<td class="cabecera" width="40"></td>';
								 //tabla += '<td class="cabecera" width="110"></td>';  
								 tabla += '</tr>';
							
							
							var tr = '';    
							for (i = 0; i < datos.length; i++){
								tr += '<tr>';
								if($('#tipo').val()=='SERVICIO')									
									tr += '<td align="center"><td>'+datos[i].id_servicio+'</td><td>'+datos[i].nombre_servicio+'</td><td><button class="carga" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_servicio+'"><i class="fa fa-arrow-down"></i></button></td>';
								else
									tr += '<td align="center"><td>'+datos[i].codigo_producto+'</td><td>'+datos[i].nombre_producto+'</td><td><button class="carga" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_det_producto+'"><i class="fa fa-arrow-down"></i></button></td>';
								tr += '</tr>';
							}
								
							tabla += tr;
							tabla += '</table>';
							
							$('#table_concepto').html( tabla );		

					}
				},						
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });
		
		
	};
	//-----------------------------------------------------------------------------------------------------
    //METODO QUE SE EJECUTA MEDIANTE CLASE .CARGA PRODUCTO SEGUN ID 
    //-----------------------------------------------------------------------------------------------------
	$(document).on('click','.carga',function(e){
	//var deposito = $(this).data('dep');
	var valor    = this.value;
	var id       = $('#id_fila').val(); 
	//var stock    = $(this).data('stock');
	var tipo  = $('#tipo').val();
    alert(id);
        $.ajax( {  
				url: '/compra/planificacion/buscarRequisitoId/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'valor='+valor,
				success:function(datos){
							if(datos)
							{
															
								$('#codigo'+id).val(datos.id);
								$('#descripcion'+id).val(datos.requisito);
								$('#tipo'+id).val(tipo);
								$('#cantidad'+id).val(00);
								$('#id'+id).val(datos.id);
								

							}

						},
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });

    });
	
	
});