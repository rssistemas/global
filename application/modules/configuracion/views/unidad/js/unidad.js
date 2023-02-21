$(document).ready(function(){

    var eliminar = function(ref){
        $.post('/archivo/unidad/eliminarDeposito/','valor=' + ref,function(filas){
            document.location.reload();
        },'json');
    };

    var setDatos = function(){
        $('#nombre').val($('#nombre').val().trim());
        $('#direccion').val($('#direccion').val().trim());
        $('#telefono').val($('#telefono').val().trim());


        if( $('#nombre').val()=='' || $('#direccion').val()=='' )
        {
            alert('Complete los datos obligatorios ***');
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                if(confirm("¿Realmente desea guardar los datos?"))
                {

                    $('#form-unidad').submit();
                    //alert("Registro de depósito exitosamente guardado");
                }
                else
                    document.getElementById('nombre').focus();
            }
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar la Unidad Operativa?"))
                {
                    $("#form-unidad").submit();
                   //alert("Depósito exitosamente Editado");
                }
                else
                    document.getElementById('nombre').focus();
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos


/******LLAMADOS A LOS METODOS PARA MANIPULAR EL FORMULARIO******/
    //lamado al boton desde el id del elemento boton
    $('#limpiar').click(function(){
        if($('#guardar').val()==1)
            location.reload();
        if($('#guardar').val()==2)
        {
            location.reload();
        }
    });

    $('#agregar').click(function(){
        setDatos();
    });

    $('#tipo').change(function(){
        $('#nombre').val($('#nombre').val().trim());

        if($('#tipo').val() && $('#guardar').val()==1)
        {
            if(!$('#nombre').val()=='' )
            {

                $.post('/pdval/archivo/deposito/comprobarDeposito/','nombre=' + $("#nombre").val()+'&tipo='+ $("#tipo").val(),function(datos){
                    if(datos.total==0)
                    {
                        document.getElementById('tipo').disabled="false";
                        document.getElementById('nombre').disabled="false";
                        document.getElementById('ubicacion').disabled="";
                        document.getElementById('ubicacion').focus();
                        document.getElementById('correo').disabled="";
                        document.getElementById('telefono').disabled="";
                        document.getElementById('fax').disabled="";
                        document.getElementById('agregar').disabled="";
                    }
                    else
                    {
                        alert('Ya existe un registro con el nombre y tipo de depósito.');
                        document.getElementById('nombre').focus();
                    }
                },'json');
            }
        }
        else
        {

        }

    });

    $('#nombre').change(function(){
        $('#nombre').val($('#nombre').val().trim());
        if($('#nombre').val()=='' && $('#tipo').val()=='-')
        {
            alert('Ingrese seleccion el tipo e ingrese el nombre del depósito para continuar con el registro.');
        }
        else
        {
            if($('#tipo').val()=='-')
            {
                alert('Seleccione el tipo de depósito.');
            }
            else if($('#nombre').val()=='')
            {
                alert('Ingrese el nombre del depósito.');
            }
            else
            {
                $.post('/pdval/archivo/deposito/comprobarDeposito/','nombre=' + $("#nombre").val()+'&tipo='+ $("#tipo").val(),function(datos){
                    if(datos.total==0)
                    {
                        document.getElementById('tipo').disabled="false";
                        document.getElementById('nombre').disabled="false";
                        document.getElementById('ubicacion').disabled="";
                        document.getElementById('ubicacion').focus();
                        document.getElementById('correo').disabled="";
                        document.getElementById('telefono').disabled="";
                        document.getElementById('fax').disabled="";
                        document.getElementById('agregar').disabled="";
                    }
                    else
                    {
                        alert('Ya existe un registro con el nombre y tipo de depósito.');
                        document.getElementById('nombre').focus();
                    }
                },'json');
            }
        }
    });


	/**** SELECCION EN EL COMBO ESTADO ******/
    $('#estado').change(function(){
        if( $('#estado').val() === '0') //si el item seleccionado fue el default -SELECCIONE-
        {
            alert('Seleccione un Estado para continuar con el registro');
            $('#municipio').html('');
            $('#municipio').append('<option value="0" >-Seleccione-</option>');
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
            getSector(); //se llamara a cargar sus correspondientes parroquias
        }
    });
    /**** SELECCION EN EL COMBO PARROQUIA ******/
    $('#parroquia').change(function(){
        if($('#parroquia').val()==='0') //si el item seleccionado fue el default -SELECCIONE-
        {
            $('#sector').html('');
            $('#sector').append('<option value="0" >-Seleccione-</option>');
            alert('Seleccione una Parroquia para continuar con el registro');
        }
        else //si se selecciono un item diferente al default
        {
            //getSector(); //se llamara a cargar sus correspondientes sectores
        }
    });
    /*****CARGARA TODOS LOS MUNICIPIOS CORRESPONDIENTES AL ESTADO SELECCIONADO*******/
    var getMunicipio = function(){
        $.post('/configuracion/municipio/buscarMunicipios/','valor='+$("#estado").val(),function(datos){
            $('#municipio').html('');
            $('#municipio').append('<option value="0" >-Seleccione-</option>');
            $('#sector').html('');
            $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                for(i = 0; i < datos.length;i++)
                {
                    if(datos[i].estatus_municipio==1)
                        	cadena = datos[i].descripcion_municipio.toUpperCase();
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
            }
            else
            {
                alert("Municipio sin Parroquias, Seleccione un Municipio con Parroquias.");
            }
        },'json');
    };
    /*****CARGARA TODOS LOS SECTORES CORRESPONDIENTES A LA PARROQUIA SELECCIONADO*******/
    var getSector = function(){
        $.post('/configuracion/sector/buscarSectores/','valor='+$("#municipio").val(),function(datos){
            $('#sector').html('');
                $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                for(i = 0; i < datos.length;i++)
                {
                    if(datos[i].estatus_sector==1)
                      	cadena = datos[i].descripcion_sector.toUpperCase();
                        $('#sector').append('<option value="'+datos[i].id_sector+'" >' +cadena+ '</option>');
                }
            }
            else
            {
                alert("Municipio sin Sectores, Seleccione una Municipio con Sectores.");
            }
        },'json');
    };


    // llamados a clases boton para editar y eliminar
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        if(confirm("¿Realmente desea eliminar el registro?"))
        {
            eliminar(li.value);
        }
        location.reload();
    });


    //BLOQUEA TECLA ENTER PARA ENVIO DE FORMULARIO
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });

 });
