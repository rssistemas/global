$(document).ready(function(){
   
    
    
    $(document).on('click','.ver',function(){
	var valor = $(this).data('id');
	//alert(valor);
	//getDatos(valor);
	$.ajax( {  
		url: '/compra/cotizacion/buscarSolicitud/',
		type: 'POST',
		dataType : 'json',
		async: false,
		data: 'valor='+valor,
		success:function(datos){
                	if(datos.length > 0)
			{
                            $('#nro').val(datos[0].id_compra);
                            $('#fecha').val(datos[0].fecha_creacion);
                            $('#proveedor').val(datos[0].razon_social_proveedor);
				
                            var total = 0;
                            var subtotal=0;
                            var iva = 0;
                            for(i= 0;i < datos.length;i++ )
                            {
                                    var nuevaFila="<tr>";
                                    nuevaFila=nuevaFila+"<td>"+datos[i].det_producto_id+"</td>";
                                    nuevaFila=nuevaFila+"<td>"+datos[i].nombre_producto+"("+datos[i].nombre_presentacion+")"+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].costo_producto+"</td>";
                                   // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].cantidad_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].monto_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].tsa_iva_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].mto_total_producto+"</td>";
                                    nuevaFila=nuevaFila+"</tr>";
                                    $("#tabla tbody").append(nuevaFila);         
                                    
				total = total + parseFloat(datos[i].mto_total_producto);
				subtotal = subtotal + parseFloat(datos[i].costo_producto * datos[i].cantidad_producto);
				iva = iva + parseFloat(datos[i].mto_iva_producto);
								
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
	});
    
    
//---------------------------------------------------------------------------------------
//FUNCION QUE CARGA LOS DEPOSITOS DE UNA UNIDAD OPERATIVA
//---------------------------------------------------------------------------------------
   
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

//------------------------------------------------------------------------------------------
//METODO QUE CARGA REQUISITOS DE TIPO PRODUCTO QUE ESTAN EN CONDICION (POR COTIZAR)
//-----------------------------------------------------------------------------------------
    
    var getRequisitoProducto = function(empresa,unidad,valor){
      
        $.post('/compra/cotizacion/buscarProducto/','empresa='+empresa+'&unidad='+unidad+'&valor='+valor,function(datos){
           if(datos.length)
           {
               $('#table_req tbody').html("");

                var tr = '';
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td><input name="req" id="req'+i+'" type="checkbox" value="'+datos[i].id_det_requisicion+'"  /></td>';
                    tr += '<td align="center">'+datos[i].requisicion_id+'</td>';
                    tr += '<td align="center">'+datos[i].id_requisito+'</td>';
                    tr += '<td >'+datos[i].nombre_producto+'</td>';
                    tr += '<td>'+datos[i].cantidad_requisito+'</td>';                   
                    tr += '<td>'+datos[i].comentario_evaluacion+'</td>';
                    tr += '</tr>';
                }

                $('#table_req tbody').html(tr);
                
                $('#enlace').html("<button class='btn btn-default input-sm' id='add' type='button' > Agregar a Lista</button>");
           }else
           {
               alert("Error cargando datos de Requisitos");
           }

            },'json');
        
        
        
    }; 

//------------------------------------------------------------------------------------------
//METODO QUE CARGA LOS REQUISITOS DE TIPO SERVICIO QUE ESTAN EN CONDICION DE (POR COTIZAR)
//-----------------------------------------------------------------------------------------
    
    var getRequisitoServicio = function(empresa,unidad,valor){
      
        $.post('/compra/cotizacion/buscarServicio/','empresa='+empresa+'&unidad='+unidad+'&valor='+valor,function(datos){
           if(datos.length > 0)
           {
               $('#table_req tbody').html("");

                var tr = '';
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td><input name="req" id="req'+i+'" type="checkbox" value="'+datos[i].id_det_requisicion+'"  /></td>';
                    tr += '<td align="center">'+datos[i].requisicion_id+'</td>';
                    tr += '<td align="center">'+datos[i].id_requisito+'</td>';
                    tr += '<td >'+datos[i].nombre_producto+'</td>';
                    tr += '<td>'+datos[i].cantidad_requisito+'</td>';                   
                    tr += '<td>0.0</td>';
                    tr += '</tr>';
                }

                $('#table_req tbody').html(tr);
                
                $('#enlace').html("<button class='btn btn-default input-sm' id='add' type='button' > Agregar a Lista</button>");
           }else
           {
               alert("Error cargando datos de Requisitos");
           }

            },'json');
        
        
        
    };    
    
    
$('#unidad').change(function(){
    getDeposito();
    
 //activarCampos();			
    });

$('#almacen').change(function(){
    $('#tipo').attr('disabled',false);    
    });    
    
$('#tipo').change(function(){
    $('#requisito').attr('disabled',false);
    
    });

$(document).on("click",".eliminarFila",function(){
    $(this).parent().parent().remove();
});
   
//-------------------------------------------------------------------------
//METODO QUE ACTIVA LA BUSQUEDA DE PRODUCTO
//-------------------------------------------------------------------------
$(document).on('keyup','#requisito',function(e){
   var pro = $(this).val();
   var unidad = $('#unidad').val();
   var empresa = $('#empresa').val();
   
   if(e.which != 115)
   {
        if(pro.length >2)
        {
            $('#table_req tbody').html("");
             if($('#tipo').val()=='SERVICIO')
                    getRequisitoServicio(empresa,unidad,pro);
             else
                    getRequisitoProducto(empresa,unidad,pro);

        }
    }else
    {
        $('#table_req tbody').html("");
        if($('#tipo').val()=='SERVICIO')
               getRequisitoServicio(empresa,unidad);
        else
               getRequisitoProducto(empresa,unidad);
        
    }
});    
    
$(document).on('click','#add',function(){
    var unidad = $('#unidad').val();
    var empresa = $('#empresa').val();
    var count = $('#table_req >tbody >tr').length;
    var tipo = $('#tipo').val();
    
    for(i = 0; i < count;i++)
    {
        if( $('#req'+i).is(':checked') ) {
            
            var cad = $('#req'+i).val();
            //alert(cad);
           if(tipo == 'PRODUCTO')
           {
                $.post('/compra/cotizacion/cargarProducto/','empresa='+empresa+'&unidad='+unidad+'&valor='+cad,function(datos){
                    var tr = '';
                    
                    tr += '<tr>';
                    tr += '<td align="center">'+datos.tipo_requisicion+'</td>';
                    tr += '<td align="center">'+datos.tipo_requisito+'</td>';
                    tr += '<td align="center">'+datos.id_requisito+'</td>';
                    tr += '<td align="center">'+datos.nombre_producto+'</td>';
                    tr += '<td>'+datos.cantidad_requisito+'</td>';                   
                    tr += '<td >'+datos.comentario_evaluacion+'</td>';
                    tr += '<td ><button class="btn btn-default input-sm eliminarFila"  type="button" ><i class="fa fa-trash-o"></i></button></td>';
                    tr += '</tr>';
                    
                    $('#tabla tbody').append(tr);
                    
                    $('#table_req tbody').html("");
                    
                },'json');
               
           }
           if(tipo == 'SERVICIO')
           {
                $.post('/compra/cotizacion/cargarServicio/','empresa='+empresa+'&unidad='+unidad+'&valor='+cad,function(datos){
                     var tr = '';
                    
                    tr += '<tr>';
                    tr += '<td align="center">'+datos.tipo_requisicion+'</td>';
                    tr += '<td align="center">'+datos.tipo_requisito+'</td>';
                    tr += '<td align="center">'+datos.id_requisito+'</td>';
                    tr += '<td align="center">'+datos.nombre_servicio+'</td>';
                    tr += '<td>'+datos.cantidad_requisito+'</td>';                   
                    tr += '<td >'+datos.comentario_evaluacion+'</td>';
                    tr += '<td ><button class="btn btn-default input-sm eliminarFila"  type="button" ><i class="fa fa-trash-o"></i></button></td>';
                    tr += '</tr>';
                    
                    $('#tabla tbody').append(tr);
                    
                    $('#table_req tbody').html("");
                   
                },'json');
               
           }
           
            
            
        }
        
    }
    
});    
    
});

