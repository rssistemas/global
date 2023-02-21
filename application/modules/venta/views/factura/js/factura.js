$(document).ready(function(){
 	
    // var getCliente = function(){
        // $.post('/globalAdm/venta/factura/buscarCliente/','rif=' + $("#rif").val()+'&tipo='+$("#nac").val(),function(datos){
        // if(datos.length >0 )
        // {
           // // $('#rif').html('');
            // $('#razon_social').val('');
            // $('#id_cliente').val('0');
			
            // //$('#rif').val(datos[0].rif_cliente);
            // $('#razon_social').val(datos[0].razon_social_cliente);
            // $('#id_cliente').val(datos[0].id_cliente);
        // }else
        // {
            // alert("Cliente no registrado .............");
            // $('#rif').val("");
            // $('#razon_social').val('');
            // $('#id_cliente').val('0');
            // $('#rif').focus();
        // }

            // },'json');
        // };
    
    var getContar = function(){
            $.post('/globalAdm/archivo/proveedor/contarCliente/','rif=' + $("#rif").val()+'&tipo=' + $("#tipo_rif").val(),function(datos){
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
	
	
	
	 //funcion para busqueda directa de los productos
    var getBusqueda = function(valor){
        $.post('/globalAdm/venta/factura/buscarProductoCatalogo/','item='+valor,function(datos){
           if(datos.length)     
           {
               var id = $('id_fila').val();
               $('#table_concepto').html("");
                var tabla = '';
                     tabla += '<table class="table  table-bordered">';
                     tabla += '<tr>';
                     tabla += '<td class="cabecera" width="10"></td>'
                     tabla += '<td class="cabecera" width="90">Codigo.</td>'
                     tabla += '<td class="cabecera" width="350">Nombre</td>'
                     tabla += '<td class="cabecera" width="40"></td>';
                     //tabla += '<td class="cabecera" width="110"></td>';  
                     tabla += '</tr>';
                
                
                var tr = '';    
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center"><td>'+datos[i].codigo_producto+'</td><td>'+datos[i].nombre_producto+'</td><td><button class="cerrar" id="'+id+'" data-dismiss="modal" value="'+datos[i].codigo_producto+'"><i class="fa fa-arrow-down"></i></button></td>';
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
				url: '/globalAdm/venta/factura/buscarProducto/',
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
	
	
	
	
	
	
    // $(document).on('click',"#agregar",function(){
        
            // var count = $('#tabla >tbody >tr').length;
            // var idPrd= count +1;
              
            // var nuevaFila="<tr>";
            // nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"'  class='form-control input-sm codigo' /></td>";
            // nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control input-sm'  /><input type='hidden' name='id[]' id='id"+idPrd+"'  /></td>";
            // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control input-sm' /></td>";
            
            // nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"'  class='form-control input-sm'  /></td>";
            // //nuevaFila=nuevaFila+"<td><select name='impuesto[]' id='impuesto"+idPrd+"' class='form-control input-sm'></select></td>"
            // //nuevaFila=nuevaFila+"<td><input type='text' name='descuento[]' id='descuento"+idPrd+"' class='form-control input-sm'  /></td>"
            // nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control input-sm' /></td>";
            // nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar'><i class='fa fa-close'></i></button></td>";
            // nuevaFila=nuevaFila+"</tr>";
            // $("#tabla tbody").append(nuevaFila);   
				
			
           
        
    // });
    
    
	$(document).on('click','.cerrar',function(e){
	var  valor = this.value
	var id = $('#id_fila').val(); 
   // alert(id);
        $.ajax( {  
				url: '/globalAdm/venta/factura/buscarProducto/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'codigo='+valor,
				success:function(datos){
							if(datos)
							{
								$('#codigo'+id).val(datos.codigo_producto);
								$('#descripcion'+id).val(datos.nombre_producto+'('+datos.nombre_prentacion+')');
								$('#precio'+id).val(datos.nombre_marca);
								//$('#producto'+id).val(datos.nombre_producto);
								
								$('#cantidad'+id).val(00);
								$('#id'+id).val(datos.id_det_producto);

							}

						},
				error: function(xhr, status) {
						alert('Disculpe, existiÃ³ un problema');
						}
                });

    });
	

    //los llamados
    $('#rif').change(function(){

        if(!$('#rif').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getCliente();

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
				$("#producto").focus();
                //alert("Pulsaste f5");
                return false;
            }
        });
        
    $(document).on('keyup','#producto',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
   });
	
	
    $('#limpiar').click(function(){
            location.reload();
            
        });
		
		
		
		
	//-----------------------------------------------------------------------------
   // METODO DE AUTOCOMPLETACION PARA LA BUSQUEDA DE GASTO
   //-----------------------------------------------------------------------------
    $("#producto").autocomplete({
        source: '/globalAdm/venta/factura/buscarProductoCatalogo/', /* este es el script que realiza la busqueda */
        minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
        select:productoSeleccionado,
        focus: productoFoco
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        var elemento = $("<a></a>");
        $("<span class='p-codigo'></span>").text(item.value.codigo).appendTo(elemento);
        $("<span class='p-nombre'></span>").text(item.value.producto).appendTo(elemento);
        $("<span class='p-tipo_producto'></span>").text(item.value.precio).appendTo(elemento);
        (i > 0)? '' : ul.prepend('<li class="ui-title" role="presentation"><span class="h-codigo">Codigo</span><span class="h-nombre">Nombre del producto</span><span class="h-tipo_producto">Categoria</span></li>'); 
        i++;
        return $("<li></li>").append(elemento).appendTo(ul);
      };    
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
	
	
	
	$(document).on('click','#add',function(){
		var gto = $('#id_producto').val(); 
		getProducto(gto);
		$('#producto').val("");
		$('#id_producto').val(0); 
		
	});
	
	
	//-----------------------------------------------------------------------------
   //metodos de autocompletado de proveedor
   //-----------------------------------------------------------------------------
    $("#cliente").autocomplete({
        source: '/globalAdm/venta/factura/autoBusCliente/', /* este es el script que realiza la busqueda */
        minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
        select:clienteSeleccionado,
        focus: clienteFoco
    });    
    
	function clienteFoco(event, ui)
    {   
        $("#cliente").val(ui.item.label);
        return false;
    }
	
    // ocurre cuando se selecciona un producto de la lista
    function clienteSeleccionado(event, ui)
    {
        //recupera la informacion del producto seleccionado
        var cliente = ui.item.value;        
        var id = cliente.id;

        //actualizamos los datos en el formulario
        
        $("#id_cliente").val(id);
        
        $("#cliente").val(cliente.nombre);
        return false;
    }
	
	var activarCampos = function(){
		
		$('#cliente').attr('disabled',false);
		$('#id_cliente').attr('disabled',false);
		$('#forma').attr('disabled',false);
		$('#emision').attr('disabled',false);
		$('#vencimiento').attr('disabled',false);
		$('#comentario').attr('disabled',false);
		
	};
	var desactivarCampos = function(){
		$('#cliente').attr('disabled',true);
		$('#id_cliente').attr('disabled',true);
		$('#forma').attr('disabled',false);
		$('#emision').attr('disabled',true);
		$('#vencimiento').attr('disabled',true);
		$('#comentario').attr('disabled',true);
		
	};		

	$('#unidad').change(function(){
		activarCampos();			
	});	
	
 });

