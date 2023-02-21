$(document).ready(function(){
 	
 	
//----------------------------------------------------------------------------------------------------
//FUNCION QUE BUSCA INFORMACION DE UN PROVEEDOR POR MEDIO DE SU RIF
//----------------------------------------------------------------------------------------------------
var getProveedor = function(){
    $.post('/compra/compras/cargarProveedor/','valor=' + $("#rif").val(),function(datos){
                    if( datos )
                    {
                        // $('#rif').html('');
                        //$('#razon_social').val('');
                        $('#proveedor').val('0');

                        //$('#rif').val(datos[0].rif_cliente);
                        $('#razon_social').val(datos.razon_social_proveedor);
                        $('#proveedor').val(datos.id_proveedor);
                        $('#direccion').val(datos.direccion_fiscal_proveedor);
                        $('#correo').val(datos.correo_proveedor);

                        $('#guardar').attr('disabled',false);
                        $('#agregar').attr('disabled',false);
                        //getDescuento(datos[0].id_cliente,0);

                    }else
                    {
                        alert("Proveedor no registrado .............");
                        $('#rif').val("");
                        $('#razon_social').val('');
                        $('#correo').val('');
                        $('#direccion').val('');
                        $('#id_proveedor').val('0');
                        $('#rif').focus();
                    }

        },'json');
    };
	
//------------------------------------------------------------------------------------------
//FUNCION QUE CARGA LOS TIPOS DE DOCUMENTOS 
//------------------------------------------------------------------------------------------
var getDocumento = function(){
    $.post('/compra/compras/buscarDocProveedor/','prv=' + $("#id_proveedor").val()+'&doc='+$("#tdoc").val()+'&unidad='+$("#unidad").val(),function(datos){
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
    
    //-----------------------------------------------------------------------------------------
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
	
	
//-------------------------------------------------------------------------------------------
//funcion para busqueda directa de los productos, desde grid de detalle
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
	
//-------------------------------------------------------------------------------------------------------
//// FUNCION QUE CARGA LOS DETALLES DEL DOCUMENTO RECIBIDO
//-------------------------------------------------------------------------------------------------------
var getRecepcion  = function(prv,tdoc,ndoc){
		
$.ajax( {  
            url: '/compra/compras/buscarInfRecepcion/',
            type: 'POST',
            dataType : 'json',
            async: false,
            data: 'prv=' + prv +'&doc='+ tdoc +'&nro='+ ndoc,
            success:function(datos){
                    //$('#recepcion').val(datos[0].id_recepcion);
                    $("#tabla_recepcion >tbody").html('');
                    var total = 0;
                    var subtotal=0;
                    var iva = 0;
                    var deposito = datos[0].nombre_deposito;
                    var id_rec = datos[0].id_recepcion;
                    var fecha  = datos[0].fecha_recepcion;

                    for(i=0;i < datos.length;i++)
                    {   	
                            var count = $('#tabla_recepcion >tbody >tr').length;
                            var idPrd= count +1;	


                            var nuevaFila="<tr>";								
                            nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value='"+datos[i].codigo_producto+"' class='form-control  codigo' /></td>";
                            nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' readonly='true' value='"+datos[i].nombre_producto+' '+datos[i].nombre_presentacion+"' /><input type='hidden' name='id[]' id='id"+idPrd+"'  /></td>";
                            nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control text-right' readonly='true' value='"+datos[i].precio_producto+"' /></td>";             			
                            nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"' data-id='"+idPrd+"'  class='form-control text-right calcular' value='"+datos[i].cantidad_producto+"'  /></td>";

                            nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control text-right ' value='"+datos[i].total_producto+"' /></td>";
                            nuevaFila=nuevaFila+"<td><input type='text' name='recibido[]' id='recibido"+idPrd+"' data-id='"+idPrd+"' value='"+datos[i].recibido_producto+"' class='form-control text-right' /></td>";

                            //nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar'><i class='fa fa-close'></i></button></td>";
                            nuevaFila=nuevaFila+"</tr>";



                            $("#tabla_recepcion >tbody").append(nuevaFila);   

                            //total = total + parseFloat(datos[i].total_producto);
                            //subtotal = subtotal + parseFloat(datos[i].monto_producto);
                            //iva = iva + parseFloat(datos[i].mto_iva_producto);	

                    };
                            $('#nro').val(id_rec);
                            $('#fecha').val(fecha);
                            $('#deposito').val(deposito);

                            //$('#subtotal').val(subtotal);
                            //$('#iva').val(iva);
                            //$('#total').val(total);
            },
            error: function(xhr, status) {
                            alert('Disculpe, existió un problema');
                            }
            });	
    };
   
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
	

    
//-------------------------------------------------------------------------------    
//METODO QUE CHEQUEA LA EXISTENCIA  DEL DOCUMENTO DE RECEPCION PARA EL REGISTRO DE LA COMPRA    
//-------------------------------------------------------------------------------    
var checkDocCompra = function(nro){
    
    var proveedor = $('#proveedor').val();
    var documento  = $('#tipo_doc_rec').val();
    
    $.ajax( {  
        url: '/compra/compras/buscarDocProveedor/',
        type: 'POST',
        dataType : 'json',
        async: false,
        data: 'prv='+proveedor+'&doc='+documento+'&nro='+nro,
        success:function(datos){
            if(datos.total > 0)
            {
                alert("Documento ya registrado ............");
            }

            },
        error: function(xhr, status) {
                alert('Disculpe, existie un problema .....');
            }
        });
       
}   
//---------------------------------------------------------------------------------------
//METODO QUE SE ACTIVA CUANDO SE CIERRA LA VENTANA EMERGENTE DE RECEPCIONES, se carga la los datos en el formulario
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
	
//----------------------------------------------------------------------
//METODO QUE CARGA INFORMACION DEL PROVEEDOR 
//----------------------------------------------------------------------
$('#rif').change(function(){
		
             getProveedor();
             //var cli = $('#cliente').val();
             //var tot = $('#subtotal').val();        

    });

    
$('#nro_doc_rec').change(function(){
   var valor = $(this).val();
    checkDocCompra(valor);
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
                $("#producto").focus();
                $("#myModal").modal();
                
                //alert("Pulsaste f5");
                return false;
            }
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
   
//---------------------------------------------------------------------------------
//METODO QUE RECARGA EL FORMULARIO
//---------------------------------------------------------------------------------
    $('#limpiar').click(function(){
            location.reload();
            
        });
//
//metodo que guarda una compra
//
    $('#guardar').click(function(){
            setDatos();		
    });

    $('#cancelar').click(function(){

            //location.reload();	

    });
//
//metodo que carga calendario
//
     $( "#emision" ).datepicker({
            changeMonth: true,
            changeYear: true
    });
	
 //-----------------------------------------------------------------------------
 //Metodo que elimina una compra
 //-----------------------------------------------------------------------------
    $(".eliminar").click(function(){

        var myDNI = $(this).val();

        $.ajax({  
                url: '/compra/compras/eliminarCompra/',
                type: 'POST',
                dataType : 'json',
                async: false,
                data: 'valor='+myDNI,
                success:function(datos){
                        if(datos)
                        {
                                alert("La Compra fue eliminada corectamente");							

                        }else
                                {
                                        alert("Error eliminando Compra");
                                }
                },
                error: function(xhr, status) {
                                alert('Disculpe, existiÃ³ un problema');
                                }
        });


    });
  //----------------------------------------------------------------------------
  //
  //----------------------------------------------------------------------------
  $(document).on('click',".eliminarFila",function(){
      var value = $(this).val();
      if(confirm("Seguro de eliminar fila ....."))
        $("#fila"+value).remove();
  });
    
    
//-----------------------------------------------------------------------------
//Metodo que busca una compra
//-----------------------------------------------------------------------------
	$(document).on('click','.buscar',function(){
		var valor = $(this).data('id');
		//alert(valor);
		//getDatos(valor);
		$.ajax( {  
                            url: '/compra/compras/buscarCompra/',
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
	
	$("#emision").datepicker("option", "dateFormat","yy-mm-dd");
	
	$("#vencimiento").datepicker({ minDate: 0, maxDate: "+1M" },"option", "dateFormat","yy-mm-dd");
	$("#vencimiento").datepicker("option", "dateFormat","yy-mm-dd");
	
	
		
    var activarCampos = function(){

            $('#proveedor').attr('disabled',false);
            $('#rif').attr('disabled',false);
            $('#tipo_doc_rec').attr('disabled',false);
            $('#nro_doc_rec').attr('disabled',false);
            $('#serie').attr('disabled',false);
            $('#emision').attr('disabled',false);
            $('#tipo').attr('disabled',false);
            //$('#prontopago').attr('disabled',false);
            //$('#tasa_pronto_pago').attr('disabled',false);
            //$('#plazo_pronto_pago').attr('disabled',false);


    };
    var desactivarCampos = function(){

            $('#proveedor').attr('disabled',true);
            $('#rif').attr('disabled',true);
            $('#tipo_doc_rec').attr('disabled',true);
            $('#nro_doc_rec').attr('disabled',true);
            $('#serie').attr('disabled',true);
            $('#emision').attr('disabled',true);
            $('#tipo').attr('disabled',true);
            $('#prontopago').attr('disabled',true);
            $('#tasa_pronto_pago').attr('disabled',true);
            $('#plazo_pronto_pago').attr('disabled',true);

    };	

    $('#unidad').change(function(){
            getDeposito();
            activarCampos();			
    });	

    $('#tipo').change(function(){
            if($(this).val()=='CREDITO')
            {
                $('#emision').attr('readOnly',false);
                $('#prontopago').attr('disabled',false);
                $('#tasa_pronto_pago').attr('disabled',false);
                $('#plazo_pronto_pago').attr('disabled',false);
            }else
                    {
                        $('#emision').attr('readOnly',true);
                        $('#prontopago').attr('disabled',true);
                        $('#tasa_pronto_pago').attr('disabled',true);
                        $('#plazo_pronto_pago').attr('disabled',true);

                    }
    });
    //------------------------------------------------------------------------
    //ACTIVA O DESACTIVA BOTON DE PEDIDO
    //------------------------------------------------------------------------
    $('#act_pedido').click(function(){
            if($('#act_pedido').prop('checked'))
            {
                    $('#pedido').attr('disabled',false);
                    $('#bto_recepcion').attr('disabled',false);
                    $('#agregar').attr('disabled',true);
            }else
            {
                    $('#pedido').attr('disabled',true);
                    $('#bto_recepcion').attr('disabled',true);
                    $('#agregar').attr('disabled',false);
            }	

    });

    //-------------------------------------------------------------------------
    //METODO QUE CARGA FORMULARIO DE BUSQUEDA DE INFORME DE RECEPCION 
    //------------------------------------------------------------------------
    $('#bto_recepcion').click(function(){
            var prv = $("#proveedor").val();
            var ndoc= $("#factura").val();
            var tdoc= $("#tdoc").val();

            $("#recModal").modal();
            getRecepcion(prv,tdoc,ndoc);

    return false;

    });			
    //-------------------------------------------------------------------------------------------------------
    //METODO QUE PERMITE AGREGAR DETALLE DE FATURA EN EL FORMULARIO
    //-------------------------------------------------------------------------------------------------------

 $(document).on('click',"#agregar",function(){

         var count = $('#tabla >tbody >tr').length;
         var idPrd= count +1;

         var nuevaFila="<tr id='fila"+idPrd+"'>";								
                     nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value=''   class='form-control  codigo' /></td>";
                     nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' value='' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='0'  /></td>";
                     nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"' data-id='"+idPrd+"'  class='form-control  text-right' value='0'  /></td>";            
                     nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"' data-id='"+idPrd+"'  class='form-control calcular_cant  text-right' value='0'  /></td>";						
                     nuevaFila=nuevaFila+"<td><input type='text' name='monto[]'  id='monto"+idPrd+"' data-id='"+idPrd+"' class='form-control  text-right'  value='0' readonly='true' /></td>";
                     nuevaFila=nuevaFila+"<td><input type='text' name='iva[]'  id='iva"+idPrd+"' data-id='"+idPrd+"' class='form-control  text-right'  value='0' readonly='true' /></td>";								
                     nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value='0' readonly='true'  /></td>";						
                     nuevaFila=nuevaFila+"<td><button class='btn btn-warning eliminarFila' type='button' value='"+idPrd+"'><i class='glyphicon glyphicon-remove'></i></button></td>";   
                     nuevaFila=nuevaFila+"</tr>";
         $("#tabla tbody").append(nuevaFila);   

 });
//--------------------------------------------------------------------------------------------
//METODO QUE ENVIA DATOS DEL FORMULARIO
//--------------------------------------------------------------------------------------------
	var  setDatos = function(){
		var msj = "0";
		
		$('#guardar').attr('disabled',true);
		
		if($('#proveedor').val()==0)
		{
			alert('Factura sin Poroveedor ***');
                        document.getElementById('cliente').focus();            
            return;
			
		}else{
                        if($('#tipo').val()=='')
                        {
                                alert('Seleccione el tipo de Compra ***');
                                $('#guardar').attr('disabled',false);
                                document.getElementById('tipo').focus();	            				
                        }else
                                {
                                    if($('#factura').val()== 0)
                                    {
                                            alert('Introduzca el Nº de Factura de Compra ***');
                                            document.getElementById('vendedor').focus();				         
                                    }else
                                        {
                                        if(confirm("¿Realmente desea guardar la nueva Factura de Compra ?"))
                                        {
                                            if(msj = prompt("Introduzca el numero de Control de Factura de Compra"))
                                            {
                                                $("#control").val(msj);
                                                $("#form_compra").submit();	
                                            }else
                                            {
                                                alert('Introduzca el Control Fiscal de Compra ***');
                                                $('#guardar').attr('disabled',false);
                                            }
                                        }
                                        else{
                                            //liberarProducto();
                                            //location.reload();	
                                        }            

                                        }
                                }				
                    }
		
	};	
		
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
    //-------------------------------------------------------------------------------------------------------
    //METODO QUE BUSCA UNA COMPRA 
    //-------------------------------------------------------------------------------------------------------	
    var getDatos = function(valor){

            $.ajax( {  
                    url: '/compra/compras/buscarCompra/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'valor='+valor,
                    success:function(datos){
                        if(datos)
                        {
                                $('#nro').val(datos[0].id_compra);
                                $('#fecha').val(datos[0].fecha_recepcion);
                                $('#proveedor').val(datos[0].nombre_proveedor);
                                for(i= 0;i < datos.length;i++ )
                                {
                                    var nuevaFila="<tr>";
                                    nuevaFila=nuevaFila+"<td>"+datos[i].codigo_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td>"+datos[i].nombre_producto+"("+datos[i].nombre_presentacion+")"+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].costo_producto+"</td>";
                                   // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].cantidad_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].monto_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].tsa_iva_producto+"</td>";
                                    nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].mto_total_producto+"</td>";
                                    nuevaFila=nuevaFila+"</tr>";
                                    $("#tabla tbody").append(nuevaFila);         

                                }							


                        }

                    },
                    error: function(xhr, status) {
                                    alert('Disculpe, existiÃ³ un problema');
                    }
            });
		
		
	};	
		
		
		
 });

