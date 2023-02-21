$(document).ready(function(){

	//-------------------------------------------------------------------------------------------
	//ACTIVA CAMPOS DEL FORMULARIO "DETALLE PLANIFICACION"
	//-------------------------------------------------------------------------------------------
	var activarCamposDetalle = function(){
		
		$('#tipo').attr('disabled',false);
		$('#req').attr('disabled',false);
		$('#cantidad').attr('disabled',false);		
		$('#prioridad').attr('disabled',false);		
		$('#comentario').attr('disabled',false);
		$('#plazo').attr('disabled',false);
		$('#motivo').attr('disabled',false);
		
	};
	//------------------------------------------------------------------------------------------
	//DESACTIVA CAMPOS DEL FORMULARIO "DETALLE PLANIFICACION"
	//------------------------------------------------------------------------------------------
	var desactivarCamposDetalle = function(){
		$('#tipo').attr('disabled',true);
		$('#plazo').attr('disabled',true);
		$('#motivo').attr('disabled',true);
		$('#req').attr('disabled',true);
		$('#cantidad').attr('disabled',true);		
		$('#prioridad').attr('disabled',true);		
		$('#comentario').attr('disabled',true);
		
	};
	
	
	
	//-------------------------------------------------------------------------------------------
	//METODO QUE ACTIVA EL ENVIA DEL FORMULARIO DE PLANIFICACION
	//-------------------------------------------------------------------------------------------
	$('#agregar').click(function(){
		setDatos();		
	});
	
	//--------------------------------------------------------------------------------------------
	//
	//--------------------------------------------------------------------------------------------
	$('#nuevo').click(function(){
		activarCamposDetalle();
		$('#pln_modal').modal();
		$('#tipo').focus();	
	});
	
	
	//--------------------------------------------------------------------------------------------
	//METODO QUE ENVIA DATOS DEL FORMULARIO
	//--------------------------------------------------------------------------------------------
		var  setDatos = function(){
		var msj = "0";
		var count = $('#tabla_req >tbody >tr').length;
		//$('#guardar').attr('disabled',true);
		
		if(count <= 0 )
		{
			alert('Detalle de planificacion sin Requisitos ***');
            document.getElementById('requisito').focus();            
            return;
			
		}else{
				if($('#tipo').val()== "" )
				{
					alert('Introduzca el tipo del Requisito ***');
		            document.getElementById('cantidad').focus();	            				
				}else
					{
						if($('#prioridad').val()== "")
						{
							alert('Seleccione la prioridad del requisito');
				            document.getElementById('prioridad').focus();				         
						}else
							{
								if(confirm("¿Realmente desea guardar la nuevo Detalle de Planificacion ?"))
			                    {
			                    	//if(msj = prompt("Introduzca el numero de Control de Factura de Compra"))
			                    	//{
			                    		//$("#control").val(msj);
			                    		$("#detalle").submit();	
			                    	//}			                        			                        
			                    }
			                    else{
			                    	//liberarProducto();
			                    	location.reload();	
			                    }            
								
							}
					}				
			}
		
	};
	
//-----------------------------------------------------------------------------------------
// metodo que carga valores resultante de la busqueda autocomplete de requisitos
//-----------------------------------------------------------------------------------------
	$(document).on('keyup','#req',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
   });
	
	
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
						   var id = $('id_fila').val();
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

//---------------------------------------------------------------------	
//	metodo que busca el requisito seleccionado por en la lista 
//---------------------------------------------------------------------
	$(document).on('click','.enlace',function(){
		var valor = $(this).data('id');
		
		$.post('/compra/planificacion/buscarRequisitoId/','valor=' +valor+'&tipo='+ $("#tipo").val(),function(dat){
				if(dat)
				{
					
					$('#req').val(dat.nombre);
					$('#id_requisito').val(dat.id);
					$('#visor').html('');
					
				}else
					alert('error cargando data');
		
		},'json');
		
	});
//---------------------------------------------------------------------
//metodo que agrega lineas en tabla de requisitos
//---------------------------------------------------------------------
	$(document).on('click','#incluir',function(){
		var nombre = $('#req').val();
		var codigo = $('#id_requisito').val();
		var cantidad = $('#cantidad').val();
		
		var count = $('#tabla_req >tbody >tr').length;
		var idPrd= count +1;	
						
		var nuevaFila="<tr>";								
		nuevaFila=nuevaFila+"<td><input type='text' name='nro[]' id='nro"+idPrd+"' size='10' value='"+idPrd+"' class='form-control  codigo' /></td>";
		nuevaFila=nuevaFila+"<td><input type='text' name='nombre[]' id='nombre"+idPrd+"' class='form-control ' readonly='true' value='"+nombre+"' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='"+codigo+"'  /></td>";		
		nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control text-right ' value='"+cantidad+"'  /></td>";
	
		
		nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar' class='btn btn-default borrar'><i class='fa fa-close'></i></button></td>";
		nuevaFila=nuevaFila+"</tr>";
		
		
		
		$("#tabla_req >tbody").append(nuevaFila); 
		
		
		$('#req').val('');
		$('#id_requisito').val('0');
		$('#cantidad').val('0.0');
		
	});
	
//----------------------------------------------------------------------
//metodo que elimina fila de tabla de requisitos
//----------------------------------------------------------------------	
	$(document).on('click', '.borrar', function (event) {
		event.preventDefault();
		$(this).closest('tr').remove();
	});

});