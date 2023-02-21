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
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
            eliminar(li.value);   
    });
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
            restaurar(li.value);  
    });
    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar el Recurso?"))
        {
            $.post('/pdval/configuracion/recurso/estatusRecurso/','valor='+ref +'&estatus='+'1', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
    var eliminar = function(ref){
        if (confirm("¿Realmente desea Eliminar el Recurso?"))
        {
            $.post('/pdval/configuracion/recurso/estatusRecurso/','valor='+ref +'&estatus='+'9', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
    var setDatos = function(){
        $('#nombre').val($('#nombre').val().trim());
        $('#descripcion').val($('#descripcion').val().trim());
        $('#url').val($('#url').val().trim());
        $('#icono').val($('#icono').val().trim());
        $('#posicion').val($('#posicion').val().trim());
        if( $('#nombre').val()=='' || $('#descripcion').val()=='' || $('#url').val()=='' 
            || $('#icono').val()=='' || $('#posicion').val()=='' || $('#modulo').val()=='0'  )
        {        //Si alguno de los datos obligatorios estan vacios
            alert('Complete los datos obligatorios *');
        }
        else //si todos los campos obligatorios estan llenados
        {
            if($('#guardar').val()==1) //si el valor de guardar es 0 desde el agregar
            {
                if(confirm("¿Realmente desea guardar el nuevo recurso?"))
                {
                    $('#form_recurso_agregar').submit();
                }
            }
            if($('#guardar').val()==2) //si el valor de guardar es 2 desde el editar
            {
                if(confirm("¿Realmente desea editar el recurso?"))
                {
                    $("#form_recurso_editar").submit();
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos

    
    var getDatos = function(){
        $.post('/pdval/configuracion/recurso/buscarRecurso/','valor=' + valor,function(datos){
        if(datos)
        {
            document.getElementById('id').value=datos.id_recurso;
            document.getElementById('nombre').value=datos.nombre_recurso;
            document.getElementById('descripcion').value=datos.descricpion_recurso;
            document.getElementById('url').value=datos.url_recurso;
            document.getElementById('icono').value=datos.icon_recurso;
            document.getElementById('posicion').value=datos.posicion_recurso;
           // document.getElementById('modulo').value=datos.modulo_id;
            $('#modulo').val(datos.modulo_id);
//            CODIGO PARA MOSNTRAR VALOR DE COMBO DESDE BD          
//           $("#sexo option[value="+ datos.sexo_persona +"]").attr("selected",true);             		 				
        }
            },'json');
        };


 });
