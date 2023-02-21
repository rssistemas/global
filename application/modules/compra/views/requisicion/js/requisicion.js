$(document).ready(function () {

    var getDocumento = function () {
        $.post('/compra/recepcion/buscarDocumento/','tdoc='+$('#tdoc').val()+'&nro_doc='+$('#nro_doc').val()+'&prov='+$('#id_proveedor').val(),function(datos){

            if(datos.length > 0 )
            {
                alert("Este documento ya esta registrado .....");
                $('#nro_doc').val('');
                $('#nro_doc').focus();
            }else
            {
               $('#agregar').attr('disabled',false);
               $('#agregar').focus();
            }

        },'json');

    };


//---------------------------------------------------------------------------------------
//FUNCION QUE CARGA LOS DEPOSITOS DE UNA UNIDAD OPERATIVA
//---------------------------------------------------------------------------------------

    var getDeposito = function (){

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
//funcion para busqueda modal incremental de los productos
//------------------------------------------------------------------------------------------
    var getBusqueda1 = function(valor){
        $.post('/compra/requisicion/cargarProducto/','valor='+valor,function(datos){
           if(datos.length)
           {
               var id = $('id_fila').val();
               $('#tabla_prod tbody').html("");


                var tr = '';
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center">'+datos[i].codigo_producto+'</td><td>'+datos[i].nombre_producto+'</td><td>'+datos[i].marca+'</td><td>'+datos[i].presentacion+'</td><td><button class="cerrar" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_det_producto+'"><i class="fa fa-arrow-down"></i></button></td>';
                    tr += '</tr>';
                }

                $('#tabla_prod tbody').html(tr);
           }else
           {
               alert("Error cargando datos de Producto");
           }

            },'json');

    };


//------------------------------------------------------------------------------------------
//funcion para busqueda modal incremental de los Servicio
//------------------------------------------------------------------------------------------
    var getBusqueda2 = function(valor){
        $.post('/compra/requisicion/cargarServicio/','valor='+valor,function(datos){
           if(datos.length)
           {
               var id = $('id_fila').val();
			         $('#tabla_prod thead').html("");
               $('#tabla_prod tbody').html("");

                var th = '';
				        th = th + '<tr>';
                th = th + '<td class="cabecera" width="70">Codigo.</td>';
                th = th + '<td class="cabecera" width="300">Nombre</td>';
                th = th + '<td class="cabecera" width="250">Grupo</td>';
                th = th + '<td class="cabecera" width="200">Clasificacion</td>';
                th = th + '<td class="cabecera" width="30"></td>';
                th = th + '</tr>';

				$('#tabla_prod thead').html(th);

                var tr = '';
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center">'+datos[i].id_servicio+'</td><td>'+datos[i].nombre_servicio+'</td><td>'+datos[i].nombre_grupo+'</td><td>'+datos[i].nombre_clasificacion+'</td><td><button class="cerrar" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_servicio+'"><i class="fa fa-arrow-down"></i></button></td>';
                    tr += '</tr>';
                }

                $('#tabla_prod tbody').html(tr);
           }else
           {
               alert("Error cargando datos de Servicio");
           }

            },'json');

    };



//--------------------------------------------------------------------------------------------
//funcion para busqueda directa de los productos
//--------------------------------------------------------------------------------------------
    var getProducto  = function(row,valor){
        $.post('/compra/requisicion/buscarProducto/','valor='+valor,function(datos){

            if(datos)
            {
                $('#tipo'+row).val("PRODUCTO");
                $('#codigo'+row).val(datos.codigo_producto.toUpperCase());
                $('#descripcion'+row).val(datos.nombre_producto.toUpperCase());
				$('#id'+row).val(datos.id_det_producto);
                $('#marca'+row).val(datos.marca.toUpperCase());
                $('#medida'+row).val(datos.nombre_uni_med.toUpperCase());
                $('#cantidad'+row).val('00.0');
				$('#total'+row).val('00.0');
                $('#cantidad'+row).focus();
            }else
            {
                alert("Producto no esta registrado .....");

               $('#codigo'+row).focus();
            }

        },'json');

    }


//--------------------------------------------------------------------------------------------
//funcion para busqueda directa de los sevicios
//--------------------------------------------------------------------------------------------
    var getServicio  = function(row,valor){
        $.post('/compra/requisicion/buscarServicio/','valor='+valor,function(datos){

            if(datos)
            {
                $('#tipo'+row).val("SERVICIO");
                $('#codigo'+row).val(datos.id_servicio);
                $('#descripcion'+row).val(datos.nombre_servicio);
		$('#id'+row).val(datos.id_servicio);
                $('#marca'+row).val("N/A");
                $('#medida'+row).val("UNIDAD");
                $('#cantidad'+row).val('00.0');
		$('#total'+row).val('00.0');
                $('#cantidad'+row).focus();
                
            }else
            {
                alert("Servicio no esta registrado .....");

               $('#codigo'+row).focus();
            }

        },'json');

    }



	//----------------------------------------------------------------------------------------------
	var getDespacho  = function(row){
        $.post('/compra/recepcion/buscarDespachoId/','codigo='+row,function(datos){

            if(datos)
            {

                $('#fecha_despacho').val(datos.fecha_despacho);
                $('#deposito_origen').val(datos.origen);
                $('#transporte').val(datos.transporte);
            }else
            {
                alert("Error cargando Despacho .....");
            }

        },'json');

    }



//-------------------------------------------------------------------------------------------------------
//METODO QUE PERMITE AGREGAR DETALLE DE FATURA EN EL FORMULARIO
//-------------------------------------------------------------------------------------------------------

     $(document).on('click',"#agregar",function(){

             var count = $('#tabla >tbody >tr').length;
             var idPrd= count +1;

             var nuevaFila="<tr>";
                 
                 nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value=''   class='form-control  codigo input-sm' /></td>";
                 nuevaFila=nuevaFila+"<td><input type='text' name='tipo[]'  id='tipo"+idPrd+"' size='20'  class='form-control input-sm' value='' readonly='true'  /></td>";
                 nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control input-sm' value='' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='0'  /></td>";
                 nuevaFila=nuevaFila+"<td><input type='text' name='marca[]'  id='marca"+idPrd+"' data-id='"+idPrd+"'  class='form-control input-sm ' value='' readonly='true'  /></td>";
                 nuevaFila=nuevaFila+"<td><input type='text' name='medida[]' id='medida"+idPrd+"' data-id='"+idPrd+"'  class='form-control  input-sm' value=''  readonly='true' /></td>";
                 nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]'  id='cantidad"+idPrd+"' data-id='"+idPrd+"' class='form-control  text-right input-sm'  value='0'  /></td>";
                 
                 //nuevaFila=nuevaFila+"<td><input type='text' name='iva[]'  id='iva"+idPrd+"' data-id='"+idPrd+"' class='form-control  text-right input-sm'  value='0' readonly='true' /></td>";
                 //nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value='0' readonly='true'  /></td>";

		nuevaFila=nuevaFila+"</tr>";
             $("#tabla tbody").append(nuevaFila);

     });






//-----------------------------------------------------------------------------------------------------
//
//-----------------------------------------------------------------------------------------------------
	$(document).on('click','.cerrar',function(e){

		//var deposito = $(this).data('dep');
		var valor    = this.value;
		var id       = $('#id_fila').val();
		if($('#tipo').val()=='SERVICIO')
		  getServicio(id,valor);
		else
		  getProducto(id,valor);

		$('#guardar').attr('disabled',false);

	});

//--------------------------------------------------------------------------------------------
//
//--------------------------------------------------------------------------------------------
$(document).on("click","#eliminarFila",function(){
    $(this).parent().parent().remove();
});

//--------------------------------------------------------------------------------------------
//metodo que elimina solicitud
//--------------------------------------------------------------------------------------------
$(document).on("click",".eliminarSol",function(){
    
    var valor     = $(this).data('id');
    if(confirm("Desea realmente eliminar la solicitud")){
        $.post('/compra/requisicion/eliminarSolicitud/','req='+valor,function(datos){
            if(datos)
            {
                $(this).closest('tr').remove();

            }else
                alert("Error eliminando solicitud de requisicion");

        });
    }
});



//--------------------------------------------------------------------------------------------
//METODO QUE ACTICA LOS CAMPOS ACCION Y JUSTIFICAR EN ANALISIS DE Requisicion
//--------------------------------------------------------------------------------------------
	$(document).on("click",".habilitar",function(){
		var valor = $(this).val();
		var i     = $(this).data('id');



		if($('#id'+i).is(":checked"))
		{
			$('#accion'+i).attr("disabled",false);
			$('#justificacion'+i).attr("disabled",false);
			$('#grabar'+i).attr("disabled",false);

		}else
			{
				$('#accion'+i).attr('disabled',true);
				$('#justificacion'+i).attr('disabled',true);
				$('#grabar'+i).attr('disabled',true);
			}
	});

//--------------------------------------------------------------------------------------------
//
//--------------------------------------------------------------------------------------------
    $(document).on("click",".editar",function(){
       var valor = $(this).data('id');
       //var valor = this.value;
            $.ajax( {
                    url: '/compra/requisicion/buscarRequisicion/',
                    type: 'POST',
                    dataType : 'json',
                    async: true,
                    data: 'valor='+valor,
                    success:function(datos){
						
                            $("#tabla tbody").html('');
							var detalle = datos.det;
							var maestro = datos.mae;
                                //if(datos.length >0)
                                //{
                                    $('#nro').val(maestro.id_requisicion);
                                    $('#fecha').val(maestro.fecha_requisicion);
                                    $('#depo').val(maestro.nombre_deposito);
				    $('#unidad').val(maestro.nombre_unidad_operativa);
				    $('#plazo').val(maestro.plazo_requisicion);
				    $('#prioridad').val(maestro.prioridad_requisicion);
				    $('#motivo').val(maestro.motivo_requisicion);
				    $('#comentario').val(maestro.comentario_requisicion);
									
                                    for(i= 0;i < detalle.length;i++ )
                                    {
                                        var nuevaFila="<tr>";
                                        nuevaFila=nuevaFila+"<td>"+detalle[i].id_det_requisicion+"</td>";
                                        nuevaFila=nuevaFila+"<td>"+detalle[i].tipo_requisito+"</td>"
					nuevaFila=nuevaFila+"<td class='text-right'>"+detalle[i].id_requisito+"</td>"
                                        nuevaFila=nuevaFila+"<td>"+detalle[i].requisito+"</td>"
                                        
                                        nuevaFila=nuevaFila+"<td class='text-right'>"+detalle[i].cantidad_requisito+"</td>"
                                       
                                        nuevaFila=nuevaFila+"<td>"+detalle[i].condicion_requisito+"</td>"
                                        nuevaFila = nuevaFila+"<td></td>";
                                        nuevaFila=nuevaFila+"</tr>";
                                        $("#tabla tbody").append(nuevaFila);

                                    }
                                //}else
                                //{
                                 //   return true;
                                //}

                            },
                    error: function(xhr, status) {
                            alert('Disculpe, existe un problema');
                            }
                });

   });


//-------------------------------------------------------------------------------------------
//METODO DE MANIPULACION DE FORMULARIOS
//-------------------------------------------------------------------------------------------
//ACTIVA CAMPOS DEL FORMULARIO
	var activarCampos = function(){

		$('#responsable').attr('disabled',false);
		$('#rif').attr('disabled',false);
		$('#fecha_requerida').attr('disabled',false);
		$('#prioridad').attr('disabled',false);
		$('#plazo').attr('disabled',false);
		$('#motivo').attr('disabled',false);
		$('#comentario').attr('disabled',false);


	};
//------------------------------------------------------------------------------------------
//DESACTIVA CAMPOS DEL FORMULARIO
//------------------------------------------------------------------------------------------
	var desactivarCampos = function(){
		$('#responsable').attr('disabled',true);
		$('#rif').attr('disabled',true);
		$('#fecha_requerida').attr('disabled',true);
		$('#prioridad').attr('disabled',true);
		$('#plazo').attr('disabled',true);
		$('#motivo').attr('disabled',true);
		//$('#recepcion').attr('disabled',true);
		$('#comentario').attr('disabled',true);

	};
//METODO QUE ACTIVA LA CARGA LOS DEPOSITOS Y ACTIVACION DE CAMPOS
	$('#unidad').change(function(){
		getDeposito();
		activarCampos();
	});

//METODO QUE ACTIVA EL BOTON DE AGREGAR
    $('#prioridad').change(function(){
        $('#agregar').attr('disabled',false);
        $('#guardar').attr('disabled',false);
    });

// 

    $('#guardar').click(function(){
		setDatos();
	});



	$(document).on('change','#fecha_requerida',function(){
		var valor  = this.value;
		
		valor =  Date.parse(valor);
		
		var d1 = new Date(valor);
		var d = new Date();

		//var month = d.getMonth()+1;
		//var day = d.getDate();

		//var output = d.getFullYear() + '/' + (month<10 ? '0' : '') + month + '/' + (day<10 ? '0' : '') + day;

		var dia = days_between(d,d1);
		$('#plazo').val(dia);	
		
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

//-------------------------------------------------------------------------
//METODO QUE ACTIVA LA BUSQUEDA DE PRODUCTO
//-------------------------------------------------------------------------
$(document).on('keyup','#producto',function(){
   var pro = this.value;
   if(pro.length >2)
   {
        if($('#tipo').val()=='SERVICIO')
               getBusqueda2(pro);
        else
               getBusqueda1(pro);

   }
});

//--------------------------------------------------------------------------------------------
//METODO QUE ENVIA DATOS DEL FORMULARIO
//--------------------------------------------------------------------------------------------
	var  setDatos = function(){
		var msj = "0";

		$('#guardar').attr('disabled',true);

		if($('#responsable').val()==0)
		{
			alert('Requisicion sin Responsable ***');
            document.getElementById('cancelar').focus();

            return;

		}else{
				if($('#tipo').val()=='')
				{
					alert('Seleccione el tipo de Requisicion ***');
		            document.getElementById('tipo').focus();
				}else
					{
						if($('#plazo').val()== 0)
						{
							alert('Introduzca el Nº de dias de Plazo ***');
				            document.getElementById('plazo').focus();
						}else
							{
								if(confirm("¿Realmente desea guardar la nueva Requisicion de Compra ?"))
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
			                    }

							}
					}
			}

	};


    var setUpdate = function(valor){
		var accion = $('#accion'+valor).val();
		var justificacion = $('#justificacion'+valor).val();

		 $.ajax({
                    url: '/compra/requisicion/evaluarRequisicion/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'codigo='+$('#id'+valor).val()+'&acc='+$('#accion'+valor).val()+'&just='+$('#justificacion'+valor).val(),
					beforeSend: function() {
                                            $('#barra').html("Guardando datos ....");
					},complete: function () {
                                            $('#barra').html("");
					},success:function(datos){
                                            if(datos){
                                                $('#barra').html("Operacion Completada ....");
                                            }else
                                            {
                                                $('#barra').html("Error en Operacion");
                                            }
					},
                    error: function(xhr, status) {
                            alert('Disculpe, existiÃ³ un problema');
                            }
                });

	};

    $(document).on("click",".grabar",function(){
		var val = $(this).data('id');
		setUpdate(val);
	});
});
