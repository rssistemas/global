$(document).ready(function(){
    //lamado al boton desde el id del elemento boton
    $('#agregar').click(function(){
        setDatos();
    });
    $('#limpiar').click(function(){
        location.reload();
    });
    $(".editar").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });  
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        eliminar(li.value);
    });
    var setDatos = function(){
        $('#nombre').val($('#nombre').val().trim());
        $('#descripcion').val($('#descripcion').val().trim());
        $('#url').val($('#url').val().trim());
        $('#icono').val($('#icono').val().trim());
        $('#posicion').val($('#posicion').val().trim());
        $('#clave').val($('#clave').val().trim());
        if( $('#nombre').val()=='' || $('#descripcion').val()=='' || $('#url').val()=='' 
            || $('#icono').val()=='' || $('#posicion').val()=='' || $('#clave').val()==''  )
        {        //Si alguno de los datos obligatorios estan vacios
            alert('Complete los datos obligatorios *');
        }
        else //si todos los campos obligatorios estan llenados
        {
            if($('#guardar').val()==1) //si el valor de guardar es 0 desde el agregar
            {
                if(confirm("¿Realmente desea guardar el nuevo Módulo?"))
                {
                    $('#form_modulo_agregar').submit();
                }
            }
            if($('#guardar').val()==2) //si el valor de guardar es 2 desde el editar
            {
                if(confirm("¿Realmente desea editar el Módulo?"))
                {
                    $("#form_modulo_editar").submit();
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos

    var getDatos = function(){
        $.post('/pdval/configuracion/modulo/buscarModulo/','valor=' + valor,function(datos){
            if(datos)
            {
                document.getElementById('id').value=datos.id_modulo;
                document.getElementById('nombre').value=datos.nombre_modulo;
                document.getElementById('descripcion').value=datos.descricpion_modulo;
                document.getElementById('url').value=datos.url_modulo;
                document.getElementById('icono').value=datos.icon_modulo;
                document.getElementById('posicion').value=datos.posicion_modulo;
                document.getElementById('clave').value=datos.clave_modulo;
            }
        },'json');
    };

    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar el Modulo?"))
        {
            $.post('/pdval/configuracion/modulo/estatusModulo/','valor='+ref +'&estatus='+'1', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
    
    var eliminar = function(ref){
        if (confirm("¿Realmente desea Eliminar el Modulo?"))
        {
            $.post('/pdval/configuracion/modulo/estatusModulo/','valor='+ref +'&estatus='+'9', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
    
 });
