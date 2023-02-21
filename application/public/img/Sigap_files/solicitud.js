$(document).ready(function(){
    
    
    //==========================================================================
    //METODO QUE PERMITE BUSCAR DATOS DE RUBRO SELECCIONADO DE FORMA DINAMICA
    //==========================================================================
    var getRubro  = function(row){
        $.post('/pdval/transaccion/logistica/buscarRubros/','term='+$('#rubro'+row).val(),function(datos){
          
            if(datos)
            {               
                $('#desc'+row).val(datos.comentario_rubro);
                $('#um'+row).val(datos.nombre_uni_med);
                $('#cantidad'+row).val('00.0');
                $('#cantidad'+row).focus();
            }else
            {
                alert("Rubro no registrado .....");
               
               $('#rubro'+row).focus();
            }
            
        },'json');
        
    }
    //==========================================================================
    
    //==========================================================================
    //FUNCION QUE PERMITE CARGAR LOS DATOS DEL DEPOSITO EN FORMA DINAMICA
    //==========================================================================
    var getDeposito = function(){
        $.post('/pdval/archivo/deposito/buscarDeposito/','valor=' + $('#deposito_origen').val(),function(datos){
                $('#ubicacion_deposito').html('');
                $('#telefono_deposito').html('');
                   //$('#municipio').html('');

                $('#ubicacion_deposito').val(datos.ubicacion_deposito);                
                $('#telefono_deposito').val(datos.telefono_deposito);
                

            },'json');
 	};
        
    //===========================================================================
    // FUNCION QUE ME PERMITE CREAR DE FORMA DINAMICA UNA FILA EN LA TABLA
    //===========================================================================
   $("#agregar").on('click', function(){
        
        
        //$.post('/pdval/transaccion/almacen/cargarMarca/','valor=0',function(datos){
            var count = $('#tabla >tbody >tr').length;
            var idAct= count + 1;
        //    var temp="";  
        //         for(i=0;i < datos.length;i++)
        //         {
        //             temp+="<option value'"+datos[i].id_marca+"' >"+datos[i].nombre_marca+"</option>";    
        //         }
                    
                 
            var nuevaFila="<tr>";
            nuevaFila=nuevaFila+"<td><button type='button' id='"+idAct+"' class='openModal' data-id='"+idAct+"' data-toggle='modal' data-target='#myModal'  ><i class='fa fa-search-plus'></i></button></td>";
            nuevaFila=nuevaFila+"<td><input type='text' name='codigo[]' id='codigo"+idAct+"' size='5'  class='form-control input-sm codigo' /></td>";
            nuevaFila=nuevaFila+"<td><input type='text' name='producto[]' id='producto"+idAct+"' class='form-control input-sm'  /><input type='hidden' name='id[]' id='id"+idAct+"'  /></td>"
            nuevaFila=nuevaFila+"<td><input name='marca[]' type='text' id='marca"+idAct+"'  class='form-control input-sm' /></td>"
           // nuevaFila=nuevaFila+"<td><select name='marca[]' id='marca"+idAct+"'  class='form-control input-sm'>"+temp+"</select></td>"
            nuevaFila=nuevaFila+"<td><input type='text' name='modelo[]' id='modelo"+idAct+"'  class='form-control input-sm'  /></td>"
           // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
            nuevaFila=nuevaFila+"<td><input type='text' name='cantidad[]' id='cantidad"+idAct+"' class='form-control input-sm' /></td>"
            nuevaFila = nuevaFila+"<td><button type='button'  id='eliminar'><i class='fa fa-close'></i></button></td>";
            nuevaFila=nuevaFila+"</tr>";
            $("#tabla tbody").append(nuevaFila);         
           // }
        //},'json');
            
            
        
        //alert('hay '+count+' Filas');
	//$("#tabla tbody tr:eq(0)").clone().removeClass('fila-base').appendTo("#tabla tbody");
        
        
    });
    
    var getBusqueda = function(valor){
        $.post('/pdval/transaccion/almacen/buscarProductoCatalogo/','item='+valor,function(datos){
           if(datos.length)     
           {
               var id = $('id_fila').val();
               $('#table_concepto').html("");
                var tabla = '';
                     tabla += '<table class="table  table-bordered">';
                     tabla += '<tr>';
                     tabla += '<td class="cabecera" width="20"></td>'
                     tabla += '<td class="cabecera" width="100">Codigo.</td>'
                     tabla += '<td class="cabecera" width="300">Nombre</td>'
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
                    url: '/pdval/transaccion/almacen/buscarProducto/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'codigo='+valor,
                    success:function(datos){
                                if(datos)
                                {
                                    $('#codigo'+id).val(datos.codigo_producto);
                                    $('#producto'+id).val(datos.nombre_producto);
                                    $('#marca'+id).val(datos.nombre_marca);
                                    $('#producto'+id).val(datos.nombre_producto);
                                    $('#modelo'+id).val(datos.modelo);
                                    $('#cantidad'+id).val(00);
                                    $('#id'+id).val(datos.id_producto);
                                }

                            },
                    error: function(xhr, status) {
                            alert('Disculpe, existió un problema');
                            }
                });
    });    
    
    
    $(document).on('keyup','#producto',function(){
       var pro = this.value; 
       if(pro.length >2)
       {
           getBusqueda(pro);
       }
   });
   
    $(document).on("click", ".openModal", function () {
        var myDNI = $(this).data('id');
        $(".modal #id_fila").val( myDNI );
    });
   //---------------------------------------------------------------------------- 
   
    $(document).on('change','.rubro',function(){
        var row = $(this).parent().parent().index();
        var cnt = row+1;
        //alert("prueba ......."+row);
        getRubro(cnt);
    });

    //.nombre de la clase asignada al boton elimina 
    $("#tabla").on('click','#eliminar',function(e){
        var li = e.target.parentNode;
        var parent = $(this).parents().parents().get(0);
        if(confirm("Realmente desea eliminar el registro ...."))
        { 
            //eliminarDetalle(li.value);
            $(parent).remove();
        }    
    });
    
    $("#deposito_origen").change(function(){
        getDeposito();
    });
    
    $(document).on("click",".editar",function(){
       var valor = $(this).data('id');
       //var valor = this.value;   
            $.ajax( {  
                    url: '/pdval/transaccion/logistica/buscarDetSolicitud/',
                    type: 'POST',
                    dataType : 'json',
                    async: false,
                    data: 'solicitud='+valor,
                    success:function(datos){
                            $("#tabla tbody").html('');
                                if(datos.length >0)
                                {
                                    $('#nro').val(datos[0].id_solicitud);
                                    $('#fecha').val(datos[0].fecha_solicitud);
                                    $('#proveedor').val(datos[0].nombre_deposito);
                                    for(i= 0;i < datos.length;i++ )
                                    {
                                        var nuevaFila="<tr>";
                                        nuevaFila=nuevaFila+"<td>"+datos[i].codigo_producto+"</td>";
                                        nuevaFila=nuevaFila+"<td>"+datos[i].nombre_producto+"</td>"
                                        nuevaFila=nuevaFila+"<td>"+datos[i].nombre_marca+"</td>"
                                       // nuevaFila=nuevaFila+"<td><select name='marca[]' id='marca"+idAct+"'  class='form-control input-sm'>"+temp+"</select></td>"
                                        nuevaFila=nuevaFila+"<td>"+datos[i].modelo+"</td>"
                                       // nuevaFila=nuevaFila+"<td><input type='text' name='precio[]' id='precio"+idAct+"' class='form-control input-sm'  /></td>"
                                        nuevaFila=nuevaFila+"<td>"+datos[i].cantidad+"</td>"
                                        nuevaFila = nuevaFila+"<td></td>";
                                        nuevaFila=nuevaFila+"</tr>";
                                        $("#tabla tbody").append(nuevaFila);         
                                        
                                    }   
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
      
    
});

