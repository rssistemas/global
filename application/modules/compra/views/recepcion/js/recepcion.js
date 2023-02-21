$(document).ready(function(){
   
   //-----------------------------------------------------------------------------------
   //carga los tipos de documentos
   //----------------------------------------------------------------------------------
    var getDocumento = function(){
        $.post('/globalAdm/almacen/recepcion/buscarDocumento/','tdoc='+$('#tdoc').val()+'&nro_doc='+$('#nro_doc').val()+'&prov='+$('#id_proveedor').val(),function(datos){
          
            if(datos.length > 0 )
            {
                alert("Este documento ya esta registrado .....");
                $('#nro_doc').val('');
                $('#nro_doc').focus();
            }            
        },'json');
        
    };
	
	//----------------------------------------------------------------------
	//carga depositos asociados a una unidad de produccion
	//----------------------------------------------------------------------
	var getDeposito = function(){
		 
        $.ajax( {  
                    url: '/almacen/recepcion/buscarDepositoUnidad/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'unidad='+$("#unidad").val(),
                    success:function(datos){
                                if(datos.length > 0)
                                {
                                    $('#almacen').html("");
                                    $('#almacen').append('<option value="" >-Seleccione-</option>');                            
                                    for(i=0;i < datos.length;i++)
                                    {
                                        cadena = datos[i].nombre_deposito.toUpperCase();
                                        $("#almacen").append("<option value='"+datos[i].id_deposito+"'>"+cadena+"</option>");
                                    }
									
                                }else
                                    {
                                            alert("Usuario sin depositos asignados en esta unidad operativa");
                                            desactivarCampos();

                                    }

                            },
                    error: function(xhr, status) {
                            alert('Disculpe, existe un problema');
                            }
                });
		
		
	};
	
	//--------------------------------------------------------------------------
	//funcion que carga las ordenes de compra EN VENTANA MODAL 
	//-------------------------------------------------------------------------
	var getOrden = function(valor){
        $.post('/globalAdm/almacen/recepcion/buscarOrden/','proveedor='+valor,function(datos){
          
            if(datos.length > 0 )
            {               
				$('#table_concepto').html("");
                var tabla = '';
                     tabla += '<table class="table  table-bordered">';
                     tabla += '<thead><tr>';
                     tabla += '<td class="cabecera" width="10"></td>';
                     tabla += '<td class="cabecera" width="90">Codigo.</td>';
                     tabla += '<td class="cabecera" width="150">Fecha</td>';
                     //tabla += '<td class="cabecera" width="110"></td>';  
                     tabla += '</tr></thead>';
                
                
                var tr = '<tbody>';    
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td><input type="checkbox" class="" name="nro_orden'+i+'" id="nro_orden'+i+'" value="'+datos[i].id_orden_compra+'" /></td><td>'+datos[i].id_orden_compra+'</td><td>'+datos[i].fecha_creacion+'</td>';
                    tr += '</tr>';
                }
                    
                tabla += tr;
                tabla += '</tbody></table>';
                
                $('#table_concepto').html( tabla );	
				$('#contador').val(i);	
            }else
            {
               alert("Proveedor sin orden de compra");
               $('#proveedor').focus();
            }
            
        },'json');
        
    };
//-------------------------------------------------------------------------------------------
//funcion para busqueda modal incremental de los productos
//------------------------------------------------------------------------------------------
    var getBusqueda = function(valor){
        $.post('/globalAdm/almacen/recepcion/buscarProductoCatalogo/','item='+valor,function(datos){
           if(datos.length)     
           {
               var id = $('id_fila').val();
               $('#table_concepto_pro').html("");
                var tabla = '';
                     tabla += '<table class="table  table-bordered">';
                     tabla += '<thead><tr>';
                     tabla += '<td class="cabecera" width="10"></td>';
                     tabla += '<td class="cabecera" width="90">Codigo.</td>';
                     tabla += '<td class="cabecera" width="350">Nombre</td>';
                     tabla += '<td class="cabecera" width="40"></td>';
                     //tabla += '<td class="cabecera" width="110"></td>';  
                     tabla += '</tr></thead>';
                
                
                var tr = '';    
                for (i = 0; i < datos.length; i++){
                    tr += '<tr>';
                    tr += '<td align="center"><td>'+datos[i].codigo_producto+'</td><td>'+datos[i].nombre_producto+'</td><td><button class="cerrar_prod" id="'+id+'" data-dismiss="modal" value="'+datos[i].id_det_producto+'"><i class="fa fa-arrow-down"></i></button></td>';
                    tr += '</tr>';
                }
                    
                tabla += tr;
                tabla += '</table>';
                
                $('#table_concepto_pro').html( tabla );
           }else
           {
               
           }

            },'json');

    };
	
//------------------------------------------------------------------------------------
//funcion para busqueda directa de lOS DETALLE DE LAS ORDENES DE COMPRA
//------------------------------------------------------------------------------------
var getProducto  = function(row){

   $.ajax( {  
                url: '/compra/ordencompra/buscarOrdenCompra/',
                type: 'POST',
                dataType : 'json',
                async: false,
                data: 'codigo='+row,
                success:function(datos){
                    
                        $('#origen').val(datos[0].razon_social_proveedor);
                        $('#fecha_doc').val(datos[0].fecha_creacion);
                        $('#condicion_doc').val(datos[0].condicion_orden_compra);
                        $('#comentario').val(datos[0].comentario_orden_compra);
                        $('#id_origen').val(datos[0].proveedor_id);
                        
                        $("#tabla >tbody").html('');
                        var subtotal = 0;
                        var iva = 0;
                        var total = 0;
                        for(i=0;i < datos.length;i++)
                        {   
                            
                                var count = $('#tabla >tbody >tr').length;
                                var idPrd= count +1;	
                                var nuevaFila="<tr>";
                                //nuevaFila=nuevaFila+"<td></td>";		
                                nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value='"+datos[i].codigo_producto+"' readOnly='true'  class='form-control  codigo input-sm' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control input-sm ' value='"+datos[i].nombre_producto+"' readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value='"+datos[i].det_producto_id+"'  /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='marca[]' id='marca"+idPrd+"' class='form-control  text-left input-sm' value="+datos[i].nombre_marca+" readOnly='true' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control  text-right input-sm ' value='"+datos[i].precio+"' readOnly='true' /></td>";            
                                nuevaFila=nuevaFila+"<td><input type='text' name='tsa_iva[]'  id='tsa_iva"+idPrd+"' data-id='"+idPrd+"' class='form-control  calculo text-right input-sm ' readOnly='true' value='"+datos[i].tasa_impuesto+"' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"'  class='form-control  text-right input-sm ' value='"+datos[i].cantidad+"' readOnly='true' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='recibido[]' id='recibido"+idPrd+"' class='form-control  text-right input-sm '   /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='monto[]' id='monto"+idPrd+"' class='form-control  text-right input-sm ' value="+datos[i].monto+" readOnly='true' /></td>";

                                nuevaFila=nuevaFila+"</tr>";

                                $("#tabla tbody").append(nuevaFila);   

                        }

                },
                error: function(xhr, status) {
                                alert('Disculpe, existió un problema');
                                }
        });	
};
  
//------------------------------------------------------------------------------
//comprueba existencia de documento de compra
//------------------------------------------------------------------------------
var getFactCompra = function(control,proveedor){
        
        $.post('/compra/compras/buscarCompraProveedor/','valor='+control+'&proveedor='+proveedor,function(datos){
            if(datos.length > 0)
            {
                alert('Documento ya Registrado');               
                
            }else
                return true;
    
        },'json');
    
} 




//------------------------------------------------------------------------------
	
	var getDespacho  = function(row){
        $.post('/globalAdm/almacen/recepcion/buscarDespachoId/','codigo='+row,function(datos){
          
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
        
    };
    
	//----------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------
	var getDetDespacho = function(valor){

            $.ajax( {  
                        url: '/pdval/transaccion/almacen/buscarDetDespacho/',
                        type: 'POST',
                        dataType : 'json',
                        async: false,
                        data: 'codigo='+valor+'&deposito='+$('#deposito').val(),
                        success:function(datos){
                            $("#listado").html('');
                             for(i=0;i < datos.length;i++)
                            {       
                                var tabla="";        
                                tabla = "<div class='panel panel-default'>";
                                tabla = tabla +"<div class='panel-heading'><input type='hidden' name='producto[]' id='producto"+i+"'  value='"+datos[i].id_producto+"' /><span> Producto : "+datos[i].nombre_producto+"   |  <label class='form-inline '></label></span></div>";
                                tabla = tabla +"<div class='table-responsive'><table class='table table-bordered' id='tabla"+i+"'>";
                                tabla = tabla +"<thead><tr >";
                                tabla = tabla +"<th width='30'></th><th width='300'>Presentacion</th><th width='200'>Marca</th>";
                                tabla = tabla +"<th width='150'>Modelo</th>";
                                tabla = tabla +"<th width='120'>Cant.</th><th width='120'>Unidades</th></tr></thead>";
                                tabla = tabla +"<tbody>";
                                
                                var detalle = datos[i].detalle;
                                var nuevaFila = "";
                                for(j=0; j < detalle.length;j++)
                                {
                                    nuevaFila = nuevaFila+"<tr>";
                                    nuevaFila = nuevaFila+"<td><input type='checkbox' name='id[]' id='"+i+"-"+j+"' class='seleccion' value='"+detalle[j].id_detalle+"' /></td>";
                                    nuevaFila = nuevaFila+"<td><input type='text' name='presentacion"+i+"[]' id='presentacion"+i+"-"+j+"'   class='form-control input-sm codigo' value='"+detalle[j].nombre_presentacion+"' disabled='true' /><input type='hidden' name='id_presentacion"+i+"[]' id='id_presentacion"+i+"-"+j+"'  value='"+detalle[j].id_presentacion+"' /></td>";
                                    //nuevaFila=nuevaFila+"<td><input type='text' name='producto[]' id='producto"+j+"' class='form-control input-sm' value='"+detalle[j].nombre_producto+"' readOnly='true'  /></td>"                                  
                                    nuevaFila = nuevaFila+"<td><input name='marca[]' type='text' id='marca"+i+"-"+j+"'  class='form-control input-sm' value='"+detalle[j].marca+"' disabled='true' /></td>";
                                    nuevaFila = nuevaFila+"<td><input type='text' name='modelo[]' id='modelo"+i+"-"+j+"'  class='form-control input-sm' value='"+detalle[j].modelo+"' disabled='true'  /></td>";
                                    //nuevaFila = nuevaFila+"<td><input type='text' name='existencia[]' id='existencia"+i+"-"+j+"' class='form-control input-sm text-right' value='"+detalle[j].existencia+"' readOnly='true' /></td>"
                                    nuevaFila = nuevaFila+"<td><input type='text' name='cantidad"+i+"[]' id='cantidad"+i+"-"+j+"' class='form-control input-sm text-right asignado' value='"+detalle[j].cantidad+"' readOnly='true'  data-id='"+i+"-"+j+"' /></td>";
                                    nuevaFila = nuevaFila+"<td><input type='text' name='subtotal"+i+"[]' id='subtotal"+i+"-"+j+"' class='form-control input-sm text-right calculo"+i+"' value='"+detalle[j].cantidad_uni_med+"' readOnly='true' /></td>";
                                    nuevaFila = nuevaFila+"</tr>";
                                                                        
                                }
                                tabla = tabla + nuevaFila;
                                tabla = tabla +"</tbody></table></div>";
                                tabla = tabla +"<div class='panel-footer'><label class='form-inline '> Total de Productos : <input type='text' name='total[]' id='total"+i+"' class='form-control input-sm text-right ' value='"+datos[i].total_producto+"' readOnly='true' /> </label></div></div>";    
                                $("#listado").append(tabla);
                            }
                            
                                   
                        },
                        error: function(xhr, status) {
                                alert('Disculpe, existió un problema');
                                }
                    }
                  );
          
                
    };        
   //===========================================================================
   // FUNCION QUE ME PERMITE CREAR DE FORMA DINAMICA UNA FILA EN LA TABLA
   //===========================================================================
    $("#add").on('click', function(){

		 var count = $('#tabla >tbody >tr').length;
		 var idAct= count + 1;
			 
		 var nuevaFila="<tr>";
		 nuevaFila=nuevaFila+"<td><button type='button' id='"+idAct+"' class='openModal ' data-id='"+idAct+"' data-toggle='modal' data-target='#myprod'  ><i class='fa fa-search-plus'></i></button></td>";
		 nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idAct+"' size='5'  class='form-control input-sm codigo' /></td>";
		 nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idAct+"' class='form-control input-sm'  /><input type='hidden' name='id[]' id='id"+idAct+"'  /></td>";
		 nuevaFila=nuevaFila+"<td><input name='precio[]' type='text' id='precio"+idAct+"'  class='form-control input-sm' /></td>";
		 nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idAct+"' class='form-control input-sm num_deci text-right' /></td>";
		 nuevaFila=nuevaFila+"<td><input type='text' name='tsa_iva[]' id='tsa_iva"+idAct+"' class='form-control input-sm num_deci calculo text-right' /></td>";
		 nuevaFila=nuevaFila+"<td><input type='text' name='recibido[]' id='recibido"+idAct+"' class='form-control input-sm num_deci text-right' /></td>";
		 nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar'><i class='fa fa-close'></i></button></td>";
		 nuevaFila=nuevaFila+"</tr>";
		 $("#tabla tbody").append(nuevaFila);         
	 
        
     });
    
	//---------------------------------------------------------------------------------------------
	//CARGA EL DETALLE DE LA ORDEN DE COMPRA SELECIONADA
	//---------------------------------------------------------------------------------------------
	
	 $(document).on('change',".orden",function(){
        
             $.ajax( {  
                        url: '/globalAdm/compra/ordencompra/buscarOrdenCompra/',
                        type: 'POST',
                        dataType : 'json',
                        async: false,
                        data: 'codigo='+$('#orden_compra').val(),
                        success:function(datos){
                            $("#tabla >tbody").html('');
							
                            var subtotal = 0;
                            var iva = 0;
                            var total = 0;
							
                            for(i=0;i < datos.length;i++)
                            {   
                                //subtotal = subtotal + datos[i].monto;
                                //iva = iva + (datos[i].total - datos[i].monto);
                                //total = datos[i].total;	
                                var count = $('#tabla >tbody >tr').length;
                                var idPrd= count +1;	
                                var nuevaFila="<tr>";	
                                nuevaFila=nuevaFila+"<td></td>";	
                                nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idPrd+"' size='10' data-id='"+idPrd+"' value="+datos[i].codigo_producto+" readOnly='true'  class='form-control  codigo' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='descripcion[]' id='descripcion"+idPrd+"' class='form-control ' value="+datos[i].nombre_producto+" readOnly='true' /><input type='hidden' name='id[]' id='id"+idPrd+"' value="+datos[i].det_producto_id+"  /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='precio[]'  id='precio"+idPrd+"'  class='form-control  text-right' value="+datos[i].precio+" readOnly='true' /></td>";            
                                nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idPrd+"'  class='form-control  text-right' value="+datos[i].cantidad+" readOnly='true' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='monto[]' id='monto"+idPrd+"' class='form-control  text-right' value="+datos[i].monto+" readOnly='true' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='tsa_iva[]'  id='tsa_iva"+idPrd+"' data-id='"+idPrd+"' class='form-control  calculo text-right' readOnly='true' value="+datos[i].tasa_impuesto+" /></td>";								
                                nuevaFila=nuevaFila+"<td><input type='text' name='total[]' id='total"+idPrd+"' class='form-control  text-right' value="+datos[i].total+" readOnly='true' /></td>";
                                nuevaFila=nuevaFila+"<td><input type='text' name='recibido[]' id='recibido"+idPrd+"' class='form-control  text-right'   /></td>";

                                nuevaFila=nuevaFila+"</tr>";

                                $("#tabla tbody").append(nuevaFila);   
								
								
                            }
                            // $('#total').val(parseFloat(total).toFixed(2));
							// $('#subtotal').val(parseFloat(subtotal).toFixed(2));
							// $('#total_iva').val(parseFloat(iva).toFixed(2));                                 
															 
                        },
                        error: function(xhr, status) {
                                alert('Disculpe, existió un problema');
                                }
                    });			
		    
    });
	
	
	
//------------------------------------------------------------------------------
//evento para las recepciones internas
//------------------------------------------------------------------------------
    $("#deposito_prov").on('change', function(){
        
        $.ajax( {  
                    url: '/globalAdm/almacen/recepcion/buscarDespachoDeposito/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'deposito='+$("#deposito").val(),
                    success:function(datos){
                                if(datos.length > 0)
                                {
                                    //$('#deposito_origen').val(datos[0].origen);
                                    //$('#fecha_despacho').val(datos[0].fecha_despacho);
                                    
                                    for(i=0; i < datos.length;i++)
                                    {
                                         $('#despacho').append('<option value="'+datos[i].id_despacho+'" >' +datos[i].id_despacho+'->'+datos[i].origen +'</option>');
                                        
                                    }    
                                    
                                    
                                }

                            },
                    error: function(xhr, status) {
                            alert('Disculpe, existe un problema');
                            }
                });
    
    });
    
    $(document).on('change','#despacho',function(){
        var valor = $('#despacho').val();
        getDespacho(valor);
        getDetDespacho(valor);
        
    });
     
    $(document).on("click","#eliminar",function(){
		$(this).parent().parent().remove(); 
	});
	
	
//------------------------------------------------------------------------
//funcion que envia el producto seleccionado de la busqueda al padre 
//-------------------------------------------------------------------------
   
   $(document).on('click','.cerrar',function(e){
	   var parametro="";
	   var valor="";
		//var count = $('#table_concepto >tbody >tr').length;	
		var count= $('#contador').val();
		//alert(count);
		for(i=0;i<count;i++)
		{
			if($('#nro_orden'+i).is(':checked'))
			{
				parametro = parametro + '"'+$('#nro_orden'+i).val()+'"';
				valor = valor +$('#nro_orden'+i).val()+',';
				if(i+1 < count)
				{
					parametro += ',';
				}	
			}		
			
		}

		getProducto(parametro);	
		$('#orden_compra').val(valor);

    });  


	$(document).on('click','.cerrar_prod',function(e){
	var  valor = this.value;
	var id = $('#id_fila').val(); 
    //alert(id);
        $.ajax( {  
				url: '/globalAdm/almacen/recepcion/buscarProducto/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'codigo='+valor,
				success:function(datos){
							if(datos)
							{
								$('#codigo'+id).val(datos.codigo_producto);
								$('#descripcion'+id).val(datos.nombre_producto+'('+datos.nombre_prentacion+')');
								$('#precio'+id).val(0.0);
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
	
    // ocurre cuando se selecciona un proVEEDOR de la lista
    function proveedorSeleccionado(event, ui)
    {
        //recupera la informacion del producto seleccionado
        var proveedor = ui.item.value;        
        var id = proveedor.id;

        //actualizamos los datos en el formulario
        
        $("#id_proveedor").val(id);
        // no quiero que jquery despliegue el texto del control porque 
        // no puede manejar objetos, asi que escribimos los datos 
        // nosotros y cancelamos el evento
        // (intenta comentando este codigo para ver a que me refiero)
        $("#proveedor").val(proveedor.nombre);
        return false;
    }
	
   

//-------------------------------------------------------------
//funcion que envia parametro a ventana modal cuando hace click
////-------------------------------------------------------------
    $(document).on("click", ".openModal", function () {
       var valor = $(this).data('id');
        $('#id_fila').val(valor);
	
	});
//-------------------------------------------------------------
//EVENTO QUE ACTIVA BUSQUEDA DE ORDEN DE COMPRA MEDIANTE BOTON 
//-------------------------------------------------------------
	$(document).on("click", "#bto_cpra", function () {
        var prv = $("#ord_cpra").val();
        getProducto(prv);
        $('#guardar').attr('disabled',false);
	
	});
//-------------------------------------------------------------
//ACTIVA BUSQUEDA DE PRODUCTOS AL ESCRIBIRE EN CAMPO
//-------------------------------------------------------------
    $(document).on('keyup','#producto',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
   });

//-------------------------------------------------------------
//ACTIVA BUSQUEDA DE PRoVEEDOR AL ESCRIBIRE EN CAMPO
//-------------------------------------------------------------
    $(document).on('keyup','#bus_proveedor',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
   });
    
    
    
//------------------------------------------------------------
//ACTIVA COMPROBACION DE DOCUMENTO
//------------------------------------------------------------
    
   $(document).on('blur','#nro_doc',function(){
       var prv = $('#id_origen').val();
       var id  = this.value;
       
       if(id >0 )
       {
           getFactCompra(id,prv);          
       }
   });
   
//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
    $(document).on("click",".editar",function(){
       var valor = $(this).data('id');
       //var valor = this.value;   
            $.ajax( {  
                    url: '/globalAdm/almacen/recepcion/buscarRecepcion/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'codigo='+valor,
                    success:function(datos){
                            $("#tabla tbody").html('');
                            $('#enlace').html('');
                                if(datos.length >0)
                                {
                                	var id = datos[0].id_recepcion;
                                	
                                    $('#nro').val(datos[0].id_recepcion);
                                    $('#fecha').val(datos[0].fecha_recepcion);
                                    $('#deposito').val(datos[0].nombre_deposito);
                                    $('#proveedor').val(datos[0].razon_social_proveedor);
                                    
                                    for(i= 0;i < datos.length;i++ )
                                    {
                                        var nuevaFila="<tr>";
                                        nuevaFila=nuevaFila+"<td>"+datos[i].codigo_producto+"</td>";
                                        nuevaFila=nuevaFila+"<td>"+datos[i].nombre_producto+"("+datos[i].nombre_presentacion+")"+"</td>";
                                        nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].precio_producto+"</td>";
                                       // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
                                        nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].cantidad_producto+"</td>";
					nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].monto_producto+"</td>";
					nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].tsa_iva_producto+"</td>";
					nuevaFila=nuevaFila+"<td class='text-right'>"+datos[i].total_producto+"</td>";
                                        nuevaFila=nuevaFila+"</tr>";
                                        $("#tabla tbody").append(nuevaFila);         
                                        
                                    } 
                                    var url = '<a class="navbar-brand" target="_blank" href="/globalAdm/reporte/index/impIR/'+id+'" title="Imprimir Orden de Compra"><button><i class="fa fa-print"></i></button></a>';
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
   
//--------------------------------------------------------
//
//--------------------------------------------------------
    $('#guardar').click(function(){
        
        var msj = prompt("Introduzca un comentario de  la recepcion","no aplica");
        if(msj != null)
        {
            $('#comentario_rec').val(msj);
        }    
       $('#form_recepcion').submit(); 
        
    });


   
//---------------------------------------------------------
//activa campo nro_doc
//---------------------------------------------------------
    $('#tdoc').change(function(){
        $('#nro_doc').attr('disabled',false);
    });
//---------------------------------------------------------
//
//---------------------------------------------------------
    
    $('#nro_doc').change(function(){
       // getDocumento();
    });
//----------------------------------------------------------
//ACTIVA BUSQUEDA DE DEPOSITOS Y CARGA COMBO 
//----------------------------------------------------------
    $('#unidad').change(function(){
            getDeposito();
            activarCampos();			
    });
//-----------------------------------------------------------
//
//----------------------------------------------------------    
    $('#proveedor').blur(function(){
            //getOrden();

    });
 //---------------------------------------------------------
 //ACTIVA CAMPO DEPENDIENDO DEL VALOR SELECCIONADO
 //---------------------------------------------------------
    $('#operacion').change(function(){
        var valor = $(this).val();
       
        if(valor=='PROVEEDORES')
        {
            $('#act_ord_cpra').attr('disabled',false);
        }    
       if(valor=='INTERNA')
        {
            $('#act_ord_cpra').attr('disabled',true);
            $('#ord_cpra').attr('disabled',true);
            $('#bto_cpra').attr('disabled',true);
        }
       
    });
//---------------------------------------------------------
//METODO QUE ACTIVA EL BOTON Y CAMPO DE ORDEN COMPRA
//---------------------------------------------------------
    $('#act_ord_cpra').click(function(){
            if($('#act_ord_cpra').prop('checked'))
            {	
                    $('#ord_cpra').attr('disabled',false);
                    $('#bto_cpra').attr('disabled',false);
            }else
                    {
                            $('#ord_cpra').attr('disabled',true);
                            $('#bto_cpra').attr('disabled',true);
                    }	
    });
    
//--------------------------------------------------------      
//    
//-------------------------------------------------------   
	var activarCampos = function(){
		$('#almacen').attr('disabled',false);
                $('#operacion').attr('disabled',false);
                $('#documento').attr('disabled',false);
               // $('#nro_doc').attr('disabled',false);
		$('#proveedor').attr('disabled',false);
		$('#id_proveedor').attr('disabled',false);
		$('#tdoc').attr('disabled',false);
		$('#comentario').attr('disabled',false);
		
	};
	var desactivarCampos = function(){
		$('#deposito').attr('disabled',true);
                $('#operacion').attr('disabled',true);
                $('#documento').attr('disabled',true);
                $('#nro_doc').attr('disabled',true);
		$('#proveedor').attr('disabled',true);
		$('#id_proveedor').attr('disabled',true);
		$('#tdoc').attr('disabled',true);
		$('#ord_cpra').attr('disabled',true);
		$('#comentario').attr('disabled',true);
	};
	
	
    $(document).on('change','.codigo',function(){
        var row = $(this).parent().parent().index();
        var cnt = row+1;
        //alert("prueba ......."+row);
        getProducto(cnt);
    });
    
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
		

		//$('#total'+id).val(parseFloat(mto_total).toFixed(2));
		
		
		var count = $('#tabla >tbody >tr').length;
		for(i=1;i <= count;i++)
		{
			cant = parseFloat($('#cantidad'+i).val());
			pre = parseFloat($('#precio'+i).val());
			tiva = parseFloat($('#tsa_iva'+i).val());
			
			if(cant > 0 && pre > 0)
			{
				mto = pre * cant;
			}
			if(tiva > 0)
			{
				mto_total = mto * ((tiva/100)+1);			
			}
			
			
			acu_total = acu_total + parseFloat(mto_total);	
			acu_mto   = acu_mto + mto ;
			acu_iva   = acu_iva + (mto_total - mto);
		}		
			$('#total').val(parseFloat(acu_total).toFixed(2));
			$('#subtotal').val(parseFloat(acu_mto).toFixed(2));
			$('#total_iva').val(parseFloat(acu_iva).toFixed(2));
		
	});
        
        
    //----------------------------------------------------------------------
    //METODO QUE DESPLIEGA VENTANA CUANDO PRESIONAS F4
    //----------------------------------------------------------------------
           
    $(document).on('keyup keypress', '#ord_cpra', function(e) {
            if(e.which == 13) {
                e.preventDefault();
                return false;
            }
            if(e.which == 115) {
                //e.preventDefault();
                //var myDNI = $(this).data('id');
                //$(".modal #id_fila").val( myDNI );
                $("#myModal").modal();
                //$("#producto").focus();
                //alert("Pulsaste f5");
                return false;
            }
        });

        
    
});