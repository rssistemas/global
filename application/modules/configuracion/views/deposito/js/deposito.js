$(document).ready(function(){
    /******LLAMADOS A LOS METODOS PARA MANIPULAR EL FORMULARIO******/
    //lamado al boton desde el id del elemento boton
    $('#agregar').click(function(){
        setDatos();
    });
    // llamados a clases boton para editar y eliminar
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        eliminar(li.value);
    });

    // llamados a clases boton para editar y eliminar
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });

    $(".editar").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });

    var getDatos = function(valor){
        //alert("sdfsf");
        $.post('/almacen/deposito/comprobarUso/', 'valor=' + valor, function (resultado) {
            alert(resultado.total);
            if (resultado.total > 0)
            {
                alert("El registro ya se encuentra en uso, el nombre y tipo de depósito no sera editado");
                bloquear_formulario();
            }
        }, 'json');
    };  //FIN DE LA FUNCION getDatos
        //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#tipo').attr('disabled', false);
        $('#nombre').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#tipo').attr('disabled', true);
        $('#nombre').attr('disabled', true);
    };
/*******PARA ELIMINAR LOS REGISTROS DEL LISTADO DE LA VISTA PRINCIPAL**********/
    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar el Depósito?"))
        {
            $.post('/almacen/deposito/estatusDeposito/','valor='+ref+'&estatus='+'1', function (filas){
                document.location.reload();
            }, 'json');
        }
    };

    var eliminar = function(ref){
        $.post('/almacen/deposito/comprobarUso/', 'valor=' + ref, function (resultado) {
            if (resultado.total > 0)
            {
                alert("El registro ya se encuentra en uso, no puede ser eliminado.");
            }
            else if (resultado.total===0)
            if (confirm("¿Realmente desea eliminar el registro del depósito?"))
            {
                $.post('/almacen/deposito/estatusDeposito/', 'valor=' + ref+'&estatus='+'9', function (filas) {
                    document.location.reload();
                }, 'json');
            }
        },'json');
    };

    var setDatos = function(){

        $('#nombre').val($('#nombre').val().trim());
        $('#ubicacion').val($('#ubicacion').val().trim());

        $('#telefono').val($('#telefono').val().trim());
        $('#fax').val($('#fax').val().trim());
        if( $('#nombre').val()=='' || $('#ubicacion').val()=='' || $('#tipo').val()=='-'
            || $('#telefono').val()=='' || $('#sector').val()=='0' ||
            $('#parroquia').val()=='0'||$('#municipio').val()=='0' ||$('#estado').val()=='0' )
        {        //Si alguno de los datos obligatorios estan vacios
            alert('Complete los datos obligatorios *');
        }else //si todos los campos obligatorios estan llenados
        {
            if($('#guardar').val()==1) //si el valor de guardar es 0 desde el agregar
            {
                if(confirm("¿Realmente desea guardar el nuevo Depósito?"))
                {
                    $('#form_deposito_agregar').submit();
                }
            }
            if($('#guardar').val()==2) //si el valor de guardar es 2 desde el editar
            {
                if(confirm("¿Realmente desea editar el Depósito?"))
                {
                    $("#form_deposito_editar").submit();
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos

    /**** CUANDO SE SELECCIONE UN ITEM DEL COMBO TIPO ******/
    $('#tipo').change(function(){
        $('#nombre').val($('#nombre').val().trim());
        if($('#tipo').val())//si se ha seleccionado un item del combo
        {
            if(!$('#nombre').val()=='' ) //si el campo nombre tiene valor
            {
                $.post('/almacen/deposito/comprobarDeposito/','nombre=' + $("#nombre").val()+'&tipo='+ $("#tipo").val(),function(datos){
                    if(!datos.total==0) //si ya existe un deposito con el mismo nombre y tipo
                    {
                        alert("El nombre y tipo de Depósito que intenta registrar ya existe, no puede registrado nuevamente.");
                        $('#nombre').val("");
                        $('#tipo').val("-");
                        document.getElementById('nombre').focus();
                    }
                },'json');
            }
        }
    });

    /****** CUANDO SE DEJE DE ESCRIBIR EN EL CAMPO NOMBRE Y SE PASE OTRO ELEMENTO *******/
    $('#nombre').change(function(){
        $('#nombre').val($('#nombre').val().trim());
        if($('#nombre').val()=='' && $('#tipo').val()=='-')//si el nombre y tipo no han sido llenados
        {
            alert('Complete el nombre y tipo de depósito para continuar con el registro.');
        }
        else// si el nombre y tipo fueron llenados
        {
            if($('#tipo').val()=='-')//Si el combo tipo de deposito no fue seleccionado
            {
                alert('Seleccione el tipo de depósito.');
                document.getElementById('tipo').focus();
            }
            else //si el combo tipo de deposito fue seleccionado
            {
                $.post('/almacen/deposito/comprobarDeposito/','nombre=' + $("#nombre").val()+'&tipo='+ $("#tipo").val(),function(datos){
                    if(!datos.total==0) //si ya existe un deposito con el mismo nombre y tipo
                    {
                        alert("El nombre y tipo de Depósito que intenta registrar ya existe, no puede registrado nuevamente.");
                        $('#nombre').val("");
                        $('#tipo').val("-");
                        document.getElementById('nombre').focus();
                    }
                },'json');
            }
        }
    });

   //VALIDACION DEL CAMPO CORREO
    $('#correo').change(function(){
        if(!$('#correo').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }
        else
             getCorreo();
    });

    var getCorreo = function(){
        if(!(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/.test($('#correo').val())))
        {
            alert("Formato no permitido, ingrese correctamente su correo electrónico.");
            $('#correo').val('');
            $('#correo').focus();
        }
        else
        {
            $.post('/seguridad/usuario/comprobarCorreo/','correo=' + $("#correo").val() ,function(datos){
                if(datos.total >0)
                {
                    alert("El correo electrónico que ingreso ya esta en uso, introduzca otro.");
                    document.getElementById('correo').value="";
                    document.getElementById('correo').focus();
                }
            },'json');
        }
    };

    /**** SELECCION EN EL COMBO ESTADO ******/
    $('#estado').change(function(){
        if( $('#estado').val() === '0') //si el item seleccionado fue el default -SELECCIONE-
        {
            alert('Seleccione un Estado para continuar con el registro');
            $('#municipio').html('');
            $('#municipio').append('<option value="0" >-Seleccione-</option>');
            $('#parroquia').html('');
            $('#parroquia').append('<option value="0" >-Seleccione-</option>');
            $('#sector').html('');
            $('#sector').append('<option value="0" >-Seleccione-</option>');
        }
        else //si se selecciono un item diferente al default
        {
            getMunicipio(); //se llamara a cargar sus correspondientes municipios
        }
    });
    /**** SELECCION EN EL COMBO MUNICIPIO ******/
    $('#municipio').change(function(){
        if($('#municipio').val()==='0') //si el item seleccionado fue el default -SELECCIONE-
        {
            alert('Seleccione un Municipio para continuar con el registro');
            $('#parroquia').html('');
            $('#parroquia').append('<option value="0" >-Seleccione-</option>');
            $('#sector').html('');
            $('#sector').append('<option value="0" >-Seleccione-</option>');
        }
        else //si se selecciono un item diferente al default
        {
              getSector();//se llamara a cargar sus correspondientes parroquias
        }
    });
    /**** SELECCION EN EL COMBO PARROQUIA ******/
    // $('#parroquia').change(function(){
    //     if($('#parroquia').val()==='0') //si el item seleccionado fue el default -SELECCIONE-
    //     {
    //         $('#sector').html('');
    //         $('#sector').append('<option value="0" >-Seleccione-</option>');
    //         alert('Seleccione una Parroquia para continuar con el registro');
    //     }
    //     else //si se selecciono un item diferente al default
    //     {
    //         getSector(); //se llamara a cargar sus correspondientes sectores
    //     }
    // });
    /*****CARGARA TODOS LOS MUNICIPIOS CORRESPONDIENTES AL ESTADO SELECCIONADO*******/
    var getMunicipio = function(){
        $.post('/configuracion/municipio/buscarMunicipios/','valor='+$("#estado").val(),function(datos){
            $('#municipio').html('');
            $('#municipio').append('<option value="0" >-Seleccione-</option>');
            $('#parroquia').html('');
            $('#parroquia').append('<option value="0" >-Seleccione-</option>');
            $('#sector').html('');
            $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                var cadena = "";
                for(i = 0; i < datos.length;i++)
                {
                    	cadena = datos[i].descripcion_municipio.toUpperCase();
                    if(datos[i].estatus_municipio==1)
                        $('#municipio').append('<option value="'+datos[i].id_municipio+'" >' +cadena+ '</option>');
                }
            }
            else
            {
                alert("Estado sin Municipios, seleccione un Estado con Municipios.");

            }
        },'json');

    };
    /*****CARGARA TODAS LAS PARROQUIAS CORRESPONDIENTES AL MUNICIPIO SELECCIONADO*******/
    var getParroquia = function(){
        $.post('/configuracion/parroquia/buscarParroquias/','valor='+$("#municipio").val(),function(datos){
                $('#parroquia').html('');
                $('#parroquia').append('<option value="0" >-Seleccione-</option>');
                $('#sector').html('');
                $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                for(i = 0; i < datos.length;i++)
                {
                    if(datos[i].estatus_parroquia==1)
                        $('#parroquia').append('<option value="'+datos[i].id_parroquia+'" >' +datos[i].descripcion_parroquia+ '</option>');
                }
            }else
            {
                alert("Municipio sin Parroquias, Seleccione un Municipio con Parroquias.");
            }
        },'json');
    };
    /*****CARGARA TODOS LOS SECTORES CORRESPONDIENTES A LA PARROQUIA SELECCIONADO*******/
    var getSector = function(){
        $.post('/configuracion/sector/buscarSectores/','valor='+$("#parroquia").val(),function(datos){
            $('#sector').html('');
                $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                  var cadena = "";
                for(i = 0; i < datos.length;i++)
                {
                    cadena = datos[i].descripcion_sector.toUpperCase();
                    if(datos[i].estatus_sector==1)
                        $('#sector').append('<option value="'+datos[i].id_sector+'" >' +cadena+ '</option>');
                }
            }
            else
            {
                alert("Parroquia sin Sectores, Seleccione una Parroquia con Sectores.");
            }
        },'json');
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

    //BLOQUEA TECLA ENTER PARA ENVIO DE FORMULARIO
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });

});
