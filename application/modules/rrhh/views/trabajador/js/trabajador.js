$(document).ready(function(){
    
    var getTrabajador = function(){
         
        $.post('/pdval/archivo/trabajador/comprobarTrabajador/','cedula=' + $("#cedula").val()+'&tipo=' + $("#nacionalidad").val(),function(datos){
                if(datos.total==0)
                {
                    //readonly para no desabilitar los campos solo bloquear escritura
                    $('#cedula').attr('readonly',true);
                    $('#nacionalidad').attr('readonly',true);
                    
                    $('#pri_nombre').attr('disabled',false);
                    $('#pri_nombre').focus();
                    $('#seg_nombre').attr('disabled',false);
                    $('#pri_apellido').attr('disabled',false);
                    $('#seg_apellido').attr('disabled',false);
                    $('#fecha_nac').attr('disabled',false);
                    $('#sexo').attr('disabled',false);
                    $('#estado_civil').attr('disabled',false);
                    $('#lugar_nac').attr('disabled',false);
                    $('#cargo').attr('disabled',false);
                    $('#licencia').attr('disabled',false);
                    $('#estado').attr('disabled',false);
                    $('#direccion').attr('disabled',false);
                    $('#celular').attr('disabled',false);
                    $('#local').attr('disabled',false);
                    $('#correo').attr('disabled',false);
                    $('#agregar').attr('disabled',false);
                    $('#grado_licencia').attr('disabled',false);
                    $('#ubicacion').attr('disabled',false);
                    $('#agregar').attr('disabled',false);
                }
                else
                {
                    alert('Ya existe un trabajador con este nÃºmero de identificaciÃ³n personal.');
                    document.getElementById('cedula').focus();
                    document.getElementById('cedula').value="";
                    
                }
            },'json');        
    };
    var getMunicipio = function(){
        $.post('/pdval/configuracion/municipio/buscarMunicipios/','valor='+$("#estado").val(),function(datos){
            if(datos.length > 0)
            {
                $('#municipio').html('');
                document.getElementById('municipio').disabled="";
                $('#municipio').append('<option value=" " >-Seleccione-</option>');
                for(i = 0; i < datos.length;i++)
                {
                     $('#municipio').append('<option value="'+datos[i].id_municipio+'" >' +datos[i].descripcion_municipio+ '</option>');
                }
            }else
            {
                alert("Estado sin Municipios.");
                $('#municipio').html('');
                $('#parroquia').html('');
                $('#sector').html('');
            }
        },'json');
    };

    var getParroquia = function(){
        $.post('/pdval/configuracion/parroquia/buscarParroquias/','valor=' + $("#municipio").val(),function(datos){
            if(datos.length > 0)
            {
                $('#parroquia').html('');
                document.getElementById('parroquia').disabled="";
                $('#parroquia').append('<option value=" " >-Seleccione-</option>');
                for(i = 0; i < datos.length;i++)
                {
                     $('#parroquia').append('<option value="'+datos[i].id_parroquia+'" >' +datos[i].descripcion_parroquia+ '</option>');
                }
            }else
            {
                alert("Municipio sin parroquias .............");
                $('#parroquia').html('');
                $('#sector').html('');
            }
        },'json');
    };

    var getSector = function(){
        $.post('/pdval/configuracion/sector/buscarSectores/','valor=' + $("#parroquia").val(),function(datos){
            if(datos.length > 0)
            {
                $('#sector').html('');
                document.getElementById('sector').disabled="";
                $('#sector').append('<option value=" " >-Seleccione-</option>');
                for(i = 0; i < datos.length;i++)
                {
                     $('#sector').append('<option value="'+datos[i].id_sector+'" >' +datos[i].descripcion_sector+ '</option>');
                }
            }else
            {
                alert("Parroquia sin Sectores .............");
                $('#sector').html('');
            }
        },'json');
    };

    //los llamados
    $('#estado').change(function(){

        if(!$('#estado').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getMunicipio();

    });
    
    $('#municipio').change(function(){

        if(!$('#municipio').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getParroquia();

    });
    
    $('#parroquia').change(function(){

        if(!$('#parroquia').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getSector();

    });
    
    
 
    $('#correo').change(function(){

        if(!$('#correo').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }else
             getCorreo();

    });
    
    //LLAMADO PARA BUSCAR TRABAJADOR POR SU NUMERO DE CEDULA
    $('#cedula').change(function(){
        if($('#cedula').val()=='')
        {
            alert('Ingrese el nÃºmero de identificaciÃ³n para completar los datos.');
        }
        else
        {
           getTrabajador();
        }
    });

    var eliminar = function(ref){
        $.post('/pdval/archivo/trabajador/eliminarTrabajador/','valor=' + ref,function(filas){
        },'json');
    };

    var setDatos = function(){
        
        $('#pri_nombre').val($('#pri_nombre').val().trim());
        $('#seg_nombre').val($('#seg_nombre').val().trim());
        $('#pri_apellido').val($('#pri_apellido').val().trim());
        $('#seg_apellido').val($('#seg_apellido').val().trim());
        $('#lugar_nac').val($('#lugar_nac').val().trim());
        $('#licencia').val($('#licencia').val().trim());
        $('#direccion').val($('#direccion').val().trim());
        $('#celular').val($('#celular').val().trim());
        $('#local').val($('#local').val().trim());
        $('#correo').val($('#correo').val().trim());
        if($('#pri_nombre').val()==''    || $('#pri_apellido').val()==''
            || $('#direccion').val()=='' || $('#fecha_nac').val()==''
            || $('#sexo').val()=='-'     || $('#cargo').val()=='-'    
            || $('#estado').val()=='-'   || $('#municipio').val()=='-'
            || $('#sector').val()=='-'   || $('#parroquia').val()=='-'
            || $('#correo').val()=='')
        {
            alert('Complete los datos obligatorios ***');
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                if(confirm("¿Realmente desea guardar el nuevo trabajador?"))
                {
                    //ya no se necesita activar los campos cedula y nacionalidad
                    $("#form_agregar").submit();                    
                }
                else
                    document.getElementById('pri_nombre').focus();
            }
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar el Trabajador?"))
                {                    
                    //ya no se necesita activar los campos cedula y nacionalidad
                    $("#form_agregar").submit();
                    
                }
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
            $('#pri_nombre').val('');
            $('#seg_nombre').val('');
            $('#pri_apellido').val('');
            $('#seg_apellido').val('');
            $('#fecha_nac').val('dd/mm/aaaa')
            $('#lugar_nac').val('');
            $('#estado_civil').val('-');
            $('#sexo').val('-');
            $('#cargo').val('-');
            $('#licencia').val('');
            $('#direccion').val('');
            $('#celular').val('');
            $('#local').val('');
            $('#correo').val('');
            $('#estado').val('-');
            $('#municipio').val('-');
            $('#parroquia').val('-');
            $('#sector').val('-');
            document.getElementById('municipio').disabled="true";
            document.getElementById('parroquia').disabled="true";
            document.getElementById('sector').disabled="true";
        }
    });
    
    $('#restaurar').click(function(){
        location.reload();
    });
    
    $('#agregar').click(function(){
        setDatos();
    });
    
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