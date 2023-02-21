$(document).ready(function(){

    /*DECLARACION de FUNCIONES DE ENVIO (para fijar datos) setDatos
    Y RECEPCION DE DATOS getDatos*/
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
                $.post('/pdval/archivo/marcaTransporte/comprobarMarcaTransporte/','valor=' + $("#descripcion").val(),function(cantidad){
                if( cantidad.total==0 )
                {
                    if(confirm("¿Realmente desea guardar la nueva marca de transporte?"))
                    {
                        $("#marcaTransporte").submit();
                        alert("Marca de Transporte exitosamente guardado");
                    }
                    else
                        document.location.reload();
                }
                if( cantidad.total>=1 )
                {
                    alert("La marca de transporte que intenta registrar ya existe, no puede registrado nuevamente.");
                    document.getElementById('descripcion').focus();
                }
                },'json');
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                $.post('/pdval/archivo/marcaTransporte/comprobarMarcaTransporte/','valor=' + $("#descripcion").val(),function(cantidad){
                if( cantidad.total==0 )
                {
                    if(confirm("¿Realmente desea editar la nueva marca de transporte?"))
                    {
                        $("#marcaTransporte").submit();
                        alert("Marca de transporte exitosamente Editado");
                    }
                    else
                        document.location.reload();
                }
                if( cantidad.total>=1 )
                {
                    if($("#descripcion").val()===$("#aux").val())
                    {
                        if(confirm("¿Realmente desea editar la nueva marca de transporte?"))
                        {
                            $("#marcaTransporte").submit();
                            alert("Marca de transporte exitosamente Editado");
                        }
                        else
                            document.location.reload();
                    }
                    else
                    {
                        if(confirm("¿Realmente desea editar la nueva marca de transporte?"))
                        {
                            $("#marcaTransporte").submit();
                            alert("Marca de transporte exitosamente Editado");
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
        $.post('/pdval/archivo/marcaTransporte/buscarMarcaTransporte/','valor=' + valor,function(datos){
            $('#id').html('');
            $('#descripcion').html('');
            $('#aux').html('');
            $('#id').val(datos.id_marca);
            $('#descripcion').val(datos.nombre_marca);
            $('#aux').val(datos.nombre_marca);
            $('#guardar').val('2');
        },'json');
    };  //FIN DE LA FUNCION getDatos

    var eliminar = function(ref){
        $.post('/pdval/archivo/marcaTransporte/eliminarMarcaTransporte/','valor=' + ref,function(filas){
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

    //Bloquea tecleo de ENTER
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });

});  //FIN DEL JS DE LA VISTA