$(document).ready(function(){

    var setDatos = function(){
        $('#descripcion').val($('#descripcion').val().trim());
        if($('#descripcion').val()=='')
        {
            alert('Ingrese los datos obligatorios ***');
            document.getElementById('descripcion').focus();
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                $.post('/pdval/archivo/tipoTransporte/comprobarTipoTransporte/','valor=' + $("#descripcion").val(),function(cantidad){
                if( cantidad.total==0 )
                {
                    if(confirm("¿Realmente desea guardar el nuevo tipo de transporte?"))
                    {
                        $("#tipoTransporte").submit();
                        alert("Tipo de transporte exitosamente guardado");
                    }
                    else
                        document.location.reload();
                }
                if( cantidad.total>=1 )
                {
                    alert("El tipo de transporte que intenta registrar ya existe, no puede registrado nuevamente.");
                    document.getElementById('descripcion').focus();
                }
                },'json');
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                $.post('/pdval/archivo/tipoTransporte/comprobarTipoTransporte/','valor=' + $("#descripcion").val(),function(cantidad){
                if( cantidad.total==0 )
                {
                    if(confirm("¿Realmente desea editar el tipo de transporte? "+cantidad.total))
                    {
                        $("#tipoTransporte").submit();
                        alert("Tipo de transporte exitosamente Editado");
                    }
                    else
                        document.location.reload();
                }
                if( cantidad.total>=1 )
                {
                    if($("#descripcion").val()===$("#aux").val())
                    {
                        if(confirm("¿Realmente desea editar el tipo de transporte?"))
                        {
                            $("#tipoTransporte").submit();
                            alert("Tipo de transporte exitosamente editado");
                        }
                        else
                            document.location.reload();
                    }
                    else
                    {
                        if(confirm("¿Realmente desea editar el tipo de transporte?"))
                        {
                            $("#tipoTransporte").submit();
                            alert("Tipo de Transporte exitosamente Editado");
                        }
                        else
                            document.location.reload();
                    }
                }
                },'json');
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos

    var getDatos = function(valor){
        $.post('/pdval/archivo/tipoTransporte/buscarTipoTransporte/','valor=' + valor,function(datos){
            $('#descripcion').html('');
            $('#comentario').html('');
            $('#aux').html('');
            $('#id').html('');
            $('#descripcion').val(datos.nombre_tipo_trans);
            $('#aux').val(datos.nombre_tipo_trans);
            $('#comentario').val(datos.descripcion_tipo_trans);
            $('#id').val(datos.id_tipo_transporte);
            $('#guardar').val('2');
        },'json');
    };  //FIN DE LA FUNCION getDatos

    var eliminar = function(ref){
        $.post('/pdval/archivo/tipoTransporte/eliminarTipoTransporte/','valor=' + ref,function(filas){
        },'json');
    };

/******LLAMADOS A LOS METODOS PARA MANIPULAR EL FORMULARIO******/
    //lamado al boton desde el id del elemento boton
    $('#agregar').click(function(){
        setDatos();
    });

    $('#limpiar').click(function(){
        location.reload();
    });

    // llamados a clases boton para editar y eliminar
    $(".editar").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });    

    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        if(confirm("¿Realmente desea eliminar el registro?"))
        {
            eliminar(li.value);
        }
        location.reload();
    });

    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
});