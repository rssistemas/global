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
        var li = this.value;
        getDatos(li);
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
    //boton guardar
    var setDatos = function(){
        $('#nombre').val($('#nombre').val().trim());
        $('#comentario').val($('#comentario').val().trim());
        if($('#nombre').val()=='' || $('#comentario').val()=='')
        {
            alert('Complete los datos obligatorios *');
            document.getElementById('nombre').focus();
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                $.post('/archivo/tipoGasto/comprobarTgasto/','valor=' + $("#nombre").val(),function(cantidad){
                    if( cantidad.total==0 )
                    {
                        if(confirm("¿Realmente desea guardar el Nuevo Tipo de Gasto?"))
                        {
                            $("#tgasto").submit();
                        }
                    }
                    if( cantidad.total>=1 )
                    {
                        alert("El Tipo de Gasto que intenta registrar ya existe, no puede registrado nuevamente.");
                        document.getElementById('nombre').focus();
                    }
                },'json');
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                var str1= new String($('#nombre').val()).toLowerCase();// a minuscula
                var str2= new String($('#aux').val()).toLowerCase();
                if(omitir_tilde(str1)==omitir_tilde(str2))
                {
                    if(confirm("¿Realmente desea editar el Tipo de Gasto?"))
                    {
                        $("#tgasto").submit();
                    }
                }
                else
                {
                    $.post('/archivo/tipoGasto/comprobarTgasto/','valor=' + $("#nombre").val(),function(cantidad){
                        if( cantidad.total==0 )
                        {
                            if(confirm("¿Realmente desea editar el nuevo Tipo de Gasto?"))
                            {
                                $("#tgasto").submit();
                            }
                        }
                        if( cantidad.total>=1 )
                        {
                            alert("El Tipo de Gasto que intenta registrar ya existe, no puede ser registrado nuevamente.");
                            document.getElementById('nombre').focus();
                        }
                    },'json');
                }
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos

    var getDatos = function(valor){
        $.post('/archivo/tipoGasto/buscarTgasto/','valor=' + valor,function(datos){
            if(datos)
            {
                limpiar_formulario();
                $('#id').val(datos.id_tipo_gasto);
                $('#nombre').val(datos.nombre_tipo_gasto);
                $('#aux').val(datos.nombre_tipo_gasto);
                $('#comentario').val(datos.comentario_tipo_gasto);
                $('#guardar').val('2');
                habilitar_formulario();
            }
            else
            {
                limpiar_formulario();
                bloquear_formulario();
            }
        },'json');
        
        
               
    };  //FIN DE LA FUNCION getDatos


    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar el Tipo de Documento?"))
        {
                $.post('/archivo/tipoDocumento/estatusTipoDocumento/','valor='+ ref+'&estatus='+'1',function(filas){
                document.location.reload();
            }, 'json');
        }
    };
    
    var eliminar = function(ref){
        $.post('/archivo/tipoDocumento/comprobarUso/', 'valor=' + ref, function (resultado) {
            if (resultado.total > 0)
            {
                alert("El registro ya se encuentra en uso, no puede ser eliminado.");
            }
            else if (resultado.total===0)
            if (confirm("¿Realmente desea eliminar el Tipo de Documento?"))
            {
                $.post('/archivo/tipoDocumento/estatusTipoDocumento/','valor='+ref+'&estatus='+'9',function(filas){
                    document.location.reload();
                }, 'json');
            }
        },'json');
    };
    /***** FUNCIONES PARA MANEJAR EVENTOS DENTRO DEL FOMULARIO ******/
    //limpia los valores de los campos de texto
    var limpiar_formulario = function(){
        $('#id').val('');
        $('#nombre').val('');      
	$('#comentario').val(''); 
        $('#aux').val('');
    };
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#nombre').attr('disabled', false);
        $('#comentario').attr('disabled', false);
        $('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#nombre').attr('disabled', true);
	$('#comentario').attr('disabled', true);
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