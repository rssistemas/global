$(document).ready(function(){
 	
	//------------------------------------------------------------------------------------------
	//FUNCION QUE CARGA LOS TIPOS DE DOCUMENTOS 
	//------------------------------------------------------------------------------------------
    var getDocumento = function(){
        $.post('/globalAdm/compra/compras/buscarDocProveedor/','prv=' + $("#id_proveedor").val()+'&doc='+$("#tdoc").val()+'&unidad='+$("#unidad").val(),function(datos){
        if(datos.length >0 )
        {
            $('#ndoc').html("");
			$('#ndoc').append('<option value="" >-Seleccione-</option>');                            
			for(i=0; i < datos.length;i++)
			{
				 $('#ndoc').append('<option value="'+datos[i].nro_doc_ori+'" >'+datos[i].nro_doc_ori+'</option>');				
			} 
        }else
        {
            alert("Proveedor sin Recepciones .............");
            $('#proveedor').focus();
        }

            },'json');
        };
    
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
	
	
	
	
	
	
   //-----------------------------------------------------------------------------
   // METODO DE AUTOCOMPLETACION PARA LA BUSQUEDA E PRODCTO
   //-----------------------------------------------------------------------------
    $("#tGasto").autocomplete({
        source: '/globalAdm/compra/gastos/buscarTgasto/', /* este es el script que realiza la busqueda */
        minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
        select:tGastoSeleccionado,
        focus: tGastoFoco
    });    
    function tGastoFoco(event, ui)
    {   
        $("#tGasto").val(ui.item.nombre);
        
        return false;
    }
    // ocurre cuando se selecciona un producto de la lista
    function tGastoSeleccionado(event, ui)
    {
        //recupera la informacion del producto seleccionado
        var tGasto = ui.item.value;        
        var id = tGasto.id;

        //actualizamos los datos en el formulario
        
        $("#id_tGasto").val(id);
        
        $("#tGasto").val(tGasto.nombre);
        return false;
    }
	
	
	
	
	
	
	
	
   //-----------------------------------------------------------------------------
   //metodos de autocompletado de proveedor
   //-----------------------------------------------------------------------------
    $("#proveedor").autocomplete({
        source: '/globalAdm/almacen/recepcion/buscarProveedor/', /* este es el script que realiza la busqueda */
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
		
	var activarCampos = function(){
		
		$('#proveedor').attr('disabled',false);
		$('#id_proveedor').attr('disabled',false);
		$('#tdoc').attr('disabled',false);
		$('#ndoc').attr('disabled',false);
		$('#emision').attr('disabled',false);
		$('#vencimiento').attr('disabled',false);
		$('#recepcion').attr('disabled',false);
		$('#comentario').attr('disabled',false);
		
	};
	var desactivarCampos = function(){
		$('#proveedor').attr('disabled',true);
		$('#id_proveedor').attr('disabled',true);
		$('#tdoc').attr('disabled',true);
		$('#ndoc').attr('disabled',true);
		$('#emision').attr('disabled',true);
		$('#vencimiento').attr('disabled',true);
		$('#recepcion').attr('disabled',true);
		$('#comentario').attr('disabled',true);
		
	};	
		
	$('#unidad').change(function(){
		activarCampos();			
	});	
		
	$('#tdoc').change(function(){
		getDocumento();			
	});	
	
	$('#ndoc').change(function(){
		getRecepcion();	
	})	
	
	//-------------------------------------------------------------------------------------------------------
	// FUNCION QUE CARGA LOS DETALLES DEL DOCUMENTO RECIBIDO
	//-------------------------------------------------------------------------------------------------------
	 var getRecepcion  = function(row){
		
       $.ajax( {  
				url: '/globalAdm/compra/compras/buscarInfRecepcion/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'prv=' + $("#id_proveedor").val()+'&doc='+$("#tdoc").val()+'&nro='+$("#ndoc").val(),
				success:function(datos){
					$('#recepcion').val(datos[0].id_recepcion);
					$("#tabla >tbody").html('');
					var total = 0;
					var subtotal=0;
					var iva = 0;
					for(i=0;i < datos.length;i++)
					{   	
						var count = $('#tabla >tbody >tr').length;
						var idPrd= count +1;	
						
						
						var nuevaFila="<tr>";								
						nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value='"+datos[i].codigo_producto+"' readOnly='true'  class='form-control  codigo' /><input type='hidden' name='ir[]' id='ir"+idPrd+"' value='"+datos[i].id_recepcion+"'  /></td>";
						nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' value='"+datos[i].nombre_producto+"' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='"+datos[i].id_det_producto+"'  /></td>";
						nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control  text-right' value='"+datos[i].precio_producto+"' readOnly='true' /></td>";            
						nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"'  class='form-control  text-right' value='"+datos[i].cantidad_producto+"' readOnly='true' /></td>";
						//nuevaFila=nuevaFila+"<td><input type='text' name='monto[]' id='monto"+idPrd+"' class='form-control  text-right' value="+datos[i].monto+" readOnly='true' /></td>";
						nuevaFila=nuevaFila+"<td><input type='text' name='tsa_iva[]'  id='tsa_iva"+idPrd+"' data-id='"+idPrd+"' class='form-control  calculo text-right' readOnly='true' value='"+datos[i].tsa_iva_producto+"' /></td>";								
						nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value='"+datos[i].total_producto+"' readOnly='true' /></td>";
						nuevaFila=nuevaFila+"<td><input type='text' name='recibido[]' id='recibido"+idPrd+"' class='form-control  text-right'  readOnly='true' value='"+datos[i].recibido_producto+"' /></td>";
						
						nuevaFila=nuevaFila+"</tr>";
						
						$("#tabla tbody").append(nuevaFila);   
						
						total = total + parseFloat(datos[i].total_producto);
						subtotal = subtotal + parseFloat(datos[i].monto_producto);
						iva = iva + parseFloat(datos[i].mto_iva_producto);	
						
					}
					
					// nuevaFila = "<tr class='bg-blue-gradient'>";
					// nuevaFila=nuevaFila+"<td></td>";
					// nuevaFila=nuevaFila+"<td>Totales :</td>";
					// //nuevaFila=nuevaFila+"<td>SubTotal :</td>";
					// nuevaFila=nuevaFila+"<td><input name='subtotal' id='subtotal' type='text' class='form-control  text-right' value='"+subtotal+"' /></td>";
					// nuevaFila=nuevaFila+"<td></td>";
					// nuevaFila=nuevaFila+"<td><input name='iva' id='iva' type='text' class='form-control  text-right' value='"+iva+"' /></td>";
					// nuevaFila=nuevaFila+"<td></td>";
					// nuevaFila=nuevaFila+"<td><input name='subtotal' id='subtotal' type='text' class='form-control  text-right' value='"+total+"' /></td>";								 
					// $("#tabla tfoot").append(nuevaFila);
						$('#subtotal').val(subtotal);
						$('#iva').val(iva);
						$('#total').val(total);
				},
				error: function(xhr, status) {
						alert('Disculpe, existió un problema');
						}
			});	
    }	
		
		
		
		
		
		
		
 });

