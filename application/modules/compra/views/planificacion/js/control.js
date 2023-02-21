$(document).ready(function(){

	//------------------------------------------------------------------------------
	//METODO ODE BUSQUEDA DE REQUISITO(SERVICIO O PRODUCTOS)
	//------------------------------------------------------------------------------
	
	
	$(document).on('click','.detalle',function(){
		var fila = $(this).data('id');
		var valor    = this.value;
		
		$.ajax( {  
				url: '/compra/planificacion/buscarDetPlanificacion/',
				type: 'POST',
				dataType : 'json',
				async: true,
				data: 'valor='+valor,
				success:function(datos){
					if(datos)
					{
						   $('.tabla_detalle').html("");	 
						   var id = fila;
						  // alert(id);
						   
							var tabla = '<div class="panel-body">';
								 tabla += '<table class=""table table-bordered " id="tabla_detalle'+fila+'">';
								 tabla += '<tbody>';
								 tabla += '<tr class="">';
								 tabla += '<th class="cabecera" width="110"><span class=" h5 text-muted">Nro Requisito</span></th>';
								 tabla += '<th class="cabecera" width="90"><span class=" h5 text-muted">Tipo</span></th>';
								 tabla += '<th class="cabecera" width="300"><span class=" h5 text-muted">Descripcion</span></th>';
								 tabla += '<th class="cabecera" width="90"><span class=" h5 text-muted">Cantidad</span></th>';
								 tabla += '<th class="cabecera" width="90"><span class=" h5 text-muted">Prioridad</span></th>';
								 tabla += '<th class="cabecera" width="90"><span class=" h5 text-muted">Plazo.</span></th>';
								 tabla += '<th class="cabecera" width="90"></th>';
								 tabla += '<th class="cabecera" width="90"></th>'; 			
								 tabla += '</tr>';
							
							
							var tr = '';    
							for (i = 0; i < datos.length; i++){
								tr += '<tr>';
								if($('#tipo').val()==='SERVICIO'){									
									tr += '<td align="center">'+datos[i].id_det_pln_compra+'</td><td>'+datos[i].tipo_requisito+'</td><td>'+datos[i].requisito+'</td><td>'+datos[i].cantidad_requisito+'</td><td>'+datos[i].prioridad_requisito+'</td><td>'+datos[i].plazo_ejecucion+'</td><td>'+datos[i].condicion_requisito+'</td><td><button class="carga " id="'+id+'" data-dismiss="modal" value="'+datos[i].id_servicio+'"><i class="fa fa-arrow-down"></i></button></td>';
                                                                    }else{
									tr += '<td align="center">'+datos[i].id_det_pln_compra+'</td><td>'+datos[i].tipo_requisito+'</td><td>'+datos[i].requisito+'</td><td>'+datos[i].cantidad_requisito+'</td><td>'+datos[i].prioridad_requisito+'</td><td>'+datos[i].plazo_ejecucion+'</td><td>'+datos[i].condicion_requisito+'</td><td><button class="carga " id="'+id+'" data-dismiss="modal" value="'+datos[i].id_det_producto+'"><i class="fa fa-arrow-down"></i></button></td>';
                                                                    }
                                                                    tr += '</tr>';
							}
								
							tabla += tr;
							tabla += '</tbody>';
							tabla += '</table></div>';
							
							$('#det'+fila).html(tabla);		

					}
				},						
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });
		
		
	});
	
});