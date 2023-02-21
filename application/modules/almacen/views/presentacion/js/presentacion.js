$(document).ready(function(){

/*** ACCIONES LLAMADAS DESDE LA VISTA PARA MANIPULAR EL FORMULARIO ***/
/*** Llamado a botones desde el id del elemento boton (#nombre_id)  ***/
    //boton guardar
    $('#agregar').click(function(){
        setDatos();
    });
    //boton cancelar
    $('#cancelar').click(function(){
        location.reload();
    });
/*** Llamado al botones tipo clase desde el nombre de la clase (.nombre_clase) ***/
    //boton editar
    $(".editar").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });    
    //boton agregar nuevo
    $(".nuevo").click(function(e){
        habilitar_formulario();
        limpiar_formulario();
    });
    //boton eliminar
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        eliminar(li.value);
    });
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });
/*** METODOS PARA INTERACTUAR CON EL CONTROLADOR DE LA VISTA ***/
    //Para fijar valores por incluir nuevo o editar registro existente
    var setDatos = function(){
        $('#descripcion').val($('#descripcion').val().trim());
        $('#cantidad').val($('#cantidad').val().trim());
        $('#unidades').val($('#unidades').val().trim());
        if($('#descripcion').val()=='' || $('#cantidad').val()=='' ||
              $('#unidades').val()=='' ||  $('#medida').val()=='0')
        {
            alert('Complete los datos obligatorios *');
            document.getElementById('descripcion').focus();
        }
        else
        {
            if($('#cantidad').val()<=9999.99)
            {
                if($('#guardar').val()==1) //guarda el registro nuevo
                {
                    $.post('/globalAdm/almacen/presentacion/comprobarPresentacion/',
                    'valor=' + $("#descripcion").val()+
                    '&unidad='+$('#medida').val()+
                    '&cant_uni='+$('#cantidad').val()+
                    '&cant_pre='+$('#unidades').val(),function(cantidad){
                        if( cantidad.total==0 )
                        {
                            if(confirm("¿Realmente desea guardar la nueva presentación para producto?"))
                            {
                                $("#form_presentacion").submit();
                            }
                        }
                        if( cantidad.total>=1 )
                        {
                            alert("La presentación  que intenta registrar ya existe, no puede registrado nuevamente.");
                            document.getElementById('descripcion').focus();
                        }
                    },'json');
                }
                if($('#guardar').val()==2) // para guardar la edicion del registro
                {
                    var str1= new String($('#descripcion').val()).toLowerCase();// a minuscula
                    var str2= new String($('#aux').val()).toLowerCase();
                    if(omitir_tilde(str1)==omitir_tilde(str2))
                    {
                        if(confirm("¿Realmente desea editar la presentación para producto?"))
                        {
                            $("#form_presentacion").submit();
                        }
                    }
                    else
                    {
                        $.post('/pdval/archivo/presentacion/comprobarPresentacion/',
                            'valor=' + $("#descripcion").val()+
                            '&unidad='+$('#medida').val()+
                            '&cant_uni='+$('#cantidad').val()+
                            '&cant_pre='+$('#unidades').val(),function(cantidad){     
                        if( cantidad.total==0 )
                            {
                                if(confirm("¿Realmente desea editar la presentación para producto?"))
                                {
                                    $("#form_presentacion").submit();
                                }
                            }
                            if( cantidad.total>=1 )
                            {
                                alert("La Presentación para producto que intenta registrar ya existe, no puede ser registrado nuevamente.");
                                document.getElementById('descripcion').focus();
                            }
                        },'json');
                    }
                }//FIN DE LA OPCION EDITAR 2
            } //fin de margen numerico a 1000
            else
                alert("La cantidad no puede ser superior a 999.99");
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos
    
    //Para mostrar valores en la vista del fomulario
   var getDatos = function(valor){
        $.post('/pdval/archivo/presentacion/buscarPresentacion/','valor=' + valor,function(datos){
            if(datos)
            {
                limpiar_formulario();
                $('#id').val(datos.id_presentacion);
                $('#descripcion').val(datos.nombre_presentacion);
                $('#cantidad').val(datos.cantidad_uni_med);
                $('#unidades').val(datos.cantidad_presentacion);
                $('#aux').val(datos.nombre_presentacion);
                $('#medida').val(datos.uni_med_id);              
                $('#guardar').val('2');
            }
            else
            {
                limpiar_formulario();
                bloquear_formulario();
            }
        },'json');
        $.post('/pdval/archivo/presentacion/comprobarUso/', 'valor=' + valor, function (resultado) {
            if (resultado.total > 0)
            {
                alert("El registro ya se encuentra en uso, no puede ser editado");
                bloquear_formulario();
            }
            else
                habilitar_formulario();
        }, 'json');
    };  //FIN DE LA FUNCION getDatos


    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar la presentación para producto?"))
        {
            $.post('/pdval/archivo/presentacion/estatusPresentacion/','valor='+ref +'&estatus='+'1', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
    
    var eliminar = function(ref){
        $.post('/pdval/archivo/presentacion/comprobarUso/', 'valor=' + ref, function (resultado) {
            if (resultado.total > 0)
                alert("El registro ya se encuentra en uso, no puede ser eliminado.");
            else
            {
                if (confirm("¿Realmente desea eliminar la presentación para producto?"))
                {
                    $.post('/pdval/archivo/presentacion/estatusPresentacion/','valor='+ref +'&estatus='+'9', function (filas){
                        document.location.reload();
                    }, 'json');
                }
            }
        },'json');
    };
    
/***** FUNCIONES PARA MANEJAR EVENTOS DENTRO DEL FOMULARIO ******/
    //limpia los valores de los campos de texto
    var limpiar_formulario = function(){
        $('#id').val('');
        $('#descripcion').val('');
        $('#cantidad').val('');
        $('#unidades').val('');
        $('#medida').val('0');
        $('#aux').val('');
    };
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#descripcion').attr('disabled', false);
        $('#cantidad').attr('disabled', false);
        $('#unidades').attr('disabled', false);
        $('#medida').attr('disabled', false);
        $('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#descripcion').attr('disabled', true);
        $('#cantidad').attr('disabled', true);
        $('#unidades').attr('disabled', true);
        $('#medida').attr('disabled', true);
        $('#agregar').attr('disabled', true);
        $('#cancelar').attr('disabled', true);
    };
    //Omite tilde, mayuscula y otros tipos de acentuación
    var omitir_tilde = (function () {
        var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÇç",
                to = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuucc",
                mapping = {};

        for (var i = 0, j = from.length; i < j; i++)
            mapping[ from.charAt(i) ] = to.charAt(i);
        
        return function (str) {
            var ret = [];
            for (var i = 0, j = str.length; i < j; i++) {
                var c = str.charAt(i);
                if (mapping.hasOwnProperty(str.charAt(i)))
                    ret.push(mapping[ c ]);
                else
                    ret.push(c);
            }
            return ret.join('');
        }
        
    })();
    //Bloquea tecleo de ENTER
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
});  //FIN DEL JS DE LA VISTA