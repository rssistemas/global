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
    
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });
    
    $(".editar").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });
    
    var getDatos = function(valor){
/*      $.post('/pdval/archivo/proveedor/buscarProveedor/','valor=' + valor,function(datos){
            $('#id').val(datos.id_proveedor);
            $('#nombre_pro').val(datos.rif_proveedor);
            $('#nacionalidad').val(datos.tipo_rif);
            $('#nombre_pro').val(datos.razon_social);
            $('#nombre_con').val(datos.contacto_proveedor);
            $('#telf_pro').val(datos.telefono_proveedor);
            $('#telf_con').val(datos.telefono_contacto);
            $('#correo_pro').val(datos.correo_proveedor);
            $('#nacionalidad').val(datos.rif_proveedor);
            $('#direccion').val(datos.direccion_fiscal);
            $('#estado').val(datos.estado_id);
            $('#municipio').val(datos.municipio_id);
            $('#sector').val(datos.sector_id);
            $('#parroquia').val(datos.parroquia_id);
            $('#tipo').val(datos.tipo_proveedor);
        }, 'json');*/
        $.post('/pdval/archivo/proveedor/comprobarUso/', 'valor=' + valor, function (resultado) {
            if (resultado.total > 0)
            {
                alert("El registro ya se encuentra en uso, el nombre y tipo de proveedor no sera editado");
                bloquear_formulario();
            }
        }, 'json');
    };  //FIN DE LA FUNCION getDatos

    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#tipo').attr('disabled', false);
        $('#nombre_pro').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#tipo').attr('disabled', true);
        $('#nombre_pro').attr('disabled', true);
    };
    
    
/*******PARA ELIMINAR LOS REGISTROS DEL LISTADO DE LA VISTA PRINCIPAL**********/
    var eliminar = function(ref){
        $.post('/pdval/archivo/proveedor/comprobarUso/', 'valor=' + ref, function (resultado) {
            if (resultado.total > 0)
            {
                alert("El registro ya se encuentra en uso, no puede ser eliminado.");
            }
            else if (resultado.total===0)
            if (confirm("¿Realmente desea eliminar el registro del proveedor?"))
            {
                $.post('/pdval/archivo/proveedor/estatusProveedor/','valor='+ref +'&estatus='+'9', function (filas){
                    document.location.reload();
                }, 'json');
            }
        },'json');
    };
    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar el Proveedor?"))
        {
            $.post('/pdval/archivo/proveedor/estatusProveedor/','valor='+ref +'&estatus='+'1', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
    /******** PARA ENVIAR LOS VALORES DE LOS ELEMENTOS DEL FORMULARIO AL CONTROLADOR**********/
    var setDatos = function(){
        $('#nombre_pro').val($('#nombre_pro').val().trim());
        $('#nombre_con').val($('#nombre_con').val().trim());
        $('#telf_pro').val($('#telf_pro').val().trim());
        $('#telf_con').val($('#telf_con').val().trim());
        $('#correo_pro').val($('#correo_pro').val().trim());
        $('#direccion').val($('#direccion').val().trim());
        if($('#nombre_pro').val()==''     || $('#nombre_con').val()==''  || $('#telf_pro').val()==''
            || $('#telf_con').val()==''   || $('#correo_pro').val()==''  || $('#nacionalidad').val()=='0' 
            || $('#direccion').val()==''  || $('#estado').val()=='0'     || $('#municipio').val()=='0'     
            || $('#sector').val()=='0'    || $('#parroquia').val()=='0'  || $('#tipo').val()=='0'  )
        {
            alert('Complete los datos obligatorios *');
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                if(confirm("¿Realmente desea guardar el nuevo Proveedor?"))
                {
                    habilitar_bloqueados();
                    $("#form_proveedor_agregar").submit();                    
                }
            }//FIN DE LA OPCION GUARDAR NUEVO 1
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar el Proveedor?"))
                {                    
                    habilitar_bloqueados();
                    $("#form_proveedor_editar").submit();
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos
    
/************LLAMADO PARA BUSCAR PROVEEDOR UNA VEZ seleccionado la nacionalidad********/
    $('#nacionalidad').change(function(){
        $('#cedula').val($('#cedula').val().trim());
        if($('#nacionalidad').val()!='0' && $('#cedula').val()!='' )
            if($('#cedula').val()>1000000)
            {
                if($('#nacionalidad').val()!='0' && $('#cedula').val()!='' )
                {
                    $.post('/pdval/archivo/proveedor/comprobarProveedor/','tipo=' + $("#nacionalidad").val()+'&rif='+ $("#cedula").val(),function(datos){
                        if(!datos.total==0) //si ya existe un deposito con el mismo nombre y tipo
                        {
                            alert("El número de rif que intenta registrar ya existe, no puede registrado nuevamente.");
                            document.getElementById('cedula').focus();
                            $('#cedula').val('');
                        }
                    },'json');                    
                }
                else
                    if( $('#cedula').val()==='' )
                        alert('Ingrese el número de identificación para continuar con el registro.');
                    else if($('#nacionalidad').val()=='0')
                        alert('Seleccione el tipo de identificación para continuar con el registro');            
            }
            else
                alert("El número de identificación debe ser superior al ingresado");
    });
    
    /********LLAMADO PARA BUSCAR TRABAJADOR UNA VEZ ingresado el numero de cedula*******/
    $('#cedula').change(function(){
        $('#cedula').val($('#cedula').val().trim());
        if($('#cedula').val()>1000000)
        {
            if( $('#nacionalidad').val()!='0' && $('#cedula').val()!='' )
            {
                $.post('/pdval/archivo/proveedor/comprobarProveedor/','tipo=' + $("#nacionalidad").val()+'&rif='+ $("#cedula").val(),function(datos){
                    if(!datos.total==0) //si ya existe un deposito con el mismo nombre y tipo
                    {
                        alert("El número de rif que intenta registrar ya existe, no puede registrado nuevamente.");
                        document.getElementById('cedula').focus();
                        $('#cedula').val('');
                    }
                },'json');                    
            }
            else
                if( $('#cedula').val()==='' )
                    alert('Ingrese el número de identificación para continuar con el registro.');
                else
                    alert('Seleccione el tipo de identificación para continuar con el registro');            
        }
        else
            alert("El número de identificación debe ser superior al ingresado");
    });
    
    var habilitar_bloqueados = function(){
        //readonly para no desabilitar los campos solo bloquear escritura
        $('#nacionalidad').attr('disabled',false);
        $('#cedula').attr('readonly',false);
        $('#nombre_pro').attr('readonly',false);
        $('#tipo').attr('disabled',false);
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
            getParroquia(); //se llamara a cargar sus correspondientes parroquias
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
            getSector(); //se llamara a cargar sus correspondientes sectores
        }
    });
    /*****CARGARA TODOS LOS MUNICIPIOS CORRESPONDIENTES AL ESTADO SELECCIONADO*******/
    var getMunicipio = function(){
        $.post('/pdval/configuracion/municipio/buscarMunicipios/','valor='+$("#estado").val(),function(datos){
            $('#municipio').html('');
            $('#municipio').append('<option value="0" >-Seleccione-</option>');
            $('#parroquia').html('');
            $('#parroquia').append('<option value="0" >-Seleccione-</option>');
            $('#sector').html('');
            $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                for(i = 0; i < datos.length;i++)
                {
                    if(datos[i].estatus_municipio==1)
                        $('#municipio').append('<option value="'+datos[i].id_municipio+'" >' +datos[i].descripcion_municipio+ '</option>');
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
        $.post('/pdval/configuracion/parroquia/buscarParroquias/','valor='+$("#municipio").val(),function(datos){
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
        $.post('/pdval/configuracion/sector/buscarSectores/','valor='+$("#parroquia").val(),function(datos){
            $('#sector').html('');
                $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                for(i = 0; i < datos.length;i++)
                {
                    if(datos[i].estatus_sector==1)
                        $('#sector').append('<option value="'+datos[i].id_sector+'" >' +datos[i].descripcion_sector+ '</option>');
                }
            }
            else
            {
                alert("Parroquia sin Sectores, Seleccione una Parroquia con Sectores.");
            }
        },'json');
    };

    //VALIDACION DEL CAMPO CORREO
    $('#correo_pro').change(function(){
        if(!$('#correo_pro').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }
        else
             getCorreo();
    });
    
    var getCorreo = function(){ 
        if(!(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/.test($('#correo_pro').val())))
        {
            alert("Formato no permitido, ingrese correctamente su correo electrónico.");
            $('#correo_pro').val('');
            $('#correo_pro').focus();
        }
    };
 
    //BLOQUEA TECLA ENTER PARA ENVIO DE FORMULARIO
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
    
 });