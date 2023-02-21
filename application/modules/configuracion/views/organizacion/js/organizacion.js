$(document).ready(function(){
    
    var getMunicipio = function(){
        $.post('/configuracion/municipio/buscarMunicipios/','valor='+$("#estado").val(),function(datos){
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
        $.post('/configuracion/parroquia/buscarParroquias/','valor=' + $("#municipio").val(),function(datos){
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
        $.post('/configuracion/sector/buscarSectores/','valor=' + $("#parroquia").val(),function(datos){
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

    
    var setDatos = function(){
        
        $('#nombre').val($('#nombre').val().trim());
        $('#descripcion').val($('#descripcion').val().trim());

        $('#direccion').val($('#direccion').val().trim());
        $('#local').val($('#local').val().trim());
        $('#correo').val($('#correo').val().trim());
        
        if($('#nombre').val()==''    || $('#descripcion').val()==''
            || $('#direccion').val()=='' || $('#local').val()==''
            || $('#correo').val()==''    || $('#estado').val()=='-'   || $('#municipio').val()=='-'
            || $('#sector').val()=='-'   || $('#parroquia').val()=='-'
            || $('#correo').val()=='')
        {
            alert('Complete los datos obligatorios ***');
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                if(confirm("¿Realmente desea guardar la informacion de Organizacion ?"))
                {
                    //ya no se necesita activar los campos cedula y nacionalidad
                    $("#form_modulo_agregar").submit();                    
                }
                else
                    document.getElementById('pr').focus();
            }
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar el Trabajador?"))
                {                    
                    //ya no se necesita activar los campos cedula y nacionalidad
                    
                    
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos
    
    
    
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
     $('#restaurar').click(function(){
        location.reload();
    });
    
    $('#agregar').click(function(){
        setDatos();
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