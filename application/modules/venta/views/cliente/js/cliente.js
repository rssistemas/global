$(document).ready(function(){
    
    var getCliente = function(){
         
        $.post('/venta/cliente/buscarCliente/','cedula=' + $("#cedula").val()+'&tipo=' + $("#nacionalidad").val(),function(datos){
                if(!datos)
                {
                    //readonly para no desabilitar los campos solo bloquear escritura
                    $('#cedula').attr('readonly',true);
                    $('#nacionalidad').attr('readonly',true);
                    
                    $('#razon_social').attr('disabled',false);
                    $('#razon_social').focus();
                    $('#denominacion').attr('disabled',false);
                    $('#estado').attr('disabled',false);
                    $('#municipio').attr('disabled',false);
                    $('#parroquia').attr('disabled',false);
                    $('#sector').attr('disabled',false);
                    $('#direccion').attr('disabled',false);
                    $('#celular').attr('disabled',false);
                    $('#local').attr('disabled',false);
                    $('#correo').attr('disabled',false);
                    $('#contribuyente').attr('disabled',false);
                    $('#credito').attr('disabled',false);
                    $('#limite').attr('disabled',false);
                    
                    $('#agregar').attr('disabled',false);
				
					
                    
                }
                else
                {
                    alert('Ya existe un Cliente con este numero de identificacion ....');
                    document.getElementById('cedula').focus();
                    document.getElementById('cedula').value="";
                    
                }
            },'json');        
    };
    var getMunicipio = function(){
        $.post('/venta/cliente/buscarMunicipioEstado/','valor='+$("#estado").val(),function(datos){
            if(datos.length > 0)
            {
                $('#municipio').html('');
                document.getElementById('municipio').disabled="";
                $('#municipio').append('<option value="" >-Seleccione-</option>');
                for(i = 0; i < datos.length;i++)
                {
                	var cadena = datos[i].descripcion_municipio.toUpperCase();
                     $('#municipio').append('<option value="'+datos[i].id_municipio+'" >' +cadena+ '</option>');
                }
            }else
            {
                alert("Estado sin Municipios.");
                //$('#municipio').html('');
                //$('#parroquia').html('');
                //$('#sector').html('');
            }
        },'json');
    };

    var getParroquia = function(){
        $.post('/venta/cliente/buscarParroquiaMunicipio/','valor=' + $("#municipio").val(),function(datos){
            if(datos.length > 0)
            {
                $('#parroquia').html('');
                document.getElementById('parroquia').disabled="";
                $('#parroquia').append('<option value="" >-Seleccione-</option>');
                for(i = 0; i < datos.length;i++)
                {
                     var cadena = datos[i].descripcion_parroquia.toUpperCase();
                     $('#parroquia').append('<option value="'+datos[i].id_parroquia+'" >' +cadena+ '</option>');
                }
            }else
            {
                alert("Municipio sin parroquias .............");
                //$('#parroquia').html('');
                //$('#sector').html('');
            }
        },'json');
    };

    var getSector = function(){
        $.post('/venta/cliente/buscarSectorParroquia/','valor=' + $("#parroquia").val(),function(datos){
            if(datos.length > 0)
            {
                $('#sector').html('');
                document.getElementById('sector').disabled="false";
                $('#sector').append('<option value=" " >-Seleccione-</option>');
                for(i = 0; i < datos.length;i++)
                {
                     $('#sector').append('<option value="'+datos[i].id_sector+'" >' +datos[i].descripcion_sector+ '</option>');
                }
            }else
            {
                alert("Parroquia sin Sectores .............");
                //$('#sector').html('');
            }
        },'json');
    };
    
    
    var getCorreo = function(){
        $.post('/venta/cliente/comprobarCorreo/','valor=' + $("#correo").val(),function(datos){
            if(datos)
            {
                alert("Este correo ya esta en uso.............");
                $('#correo').val('');
                $('#correo').focus();
            }
        },'json');
    };


   //metodo que carga los municiopios 
    $("#estado").change(function(){
    	getMunicipio();   	
    });   
    
    //metodo que carga las parroquias
	$("#municipio").change(function(){
    	getParroquia();
    });
    
    $('#parroquia').change(function(){
        getSector();
    });
    
    
 
    $('#correo').change(function(){
		 getCorreo();

    });
    
    //LLAMADO PARA BUSCAR TRABAJADOR POR SU NUMERO DE CEDULA
    $('#cedula').change(function(){
        if($('#cedula').val()=='')
        {
            alert('Ingrese el numero de identificacion para completar los datos.');
        }
        else
        {
           getCliente();
        }
    });
	

    var eliminar = function(ref){
        $.post('/venta/cliente/eliminar/','valor=' + ref,function(filas){
        	if(filas)
        	{
        		alert("Cliente eliminado ");	
        	}else
        		alert("Error eliminando cliente");       
        },'json');    
    };

    var setDatos = function(){
        
        $('#razon_social').val($('#razon_social').val().trim());
        $('#denominacion').val($('#denominacion').val().trim());
        $('#direccion').val($('#direccion').val().trim());
        $('#celular').val($('#celular').val().trim());
        $('#local').val($('#local').val().trim());
        $('#correo').val($('#correo').val().trim());
        
        if($('#razon_social').val()==''  || $('#denominacion').val()==''
            || $('#direccion').val()=='' || $('#contribuyente').val()=='-'    
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
                if(confirm("¿Realmente desea guardar el nuevo Cliente?"))
                {
                    //ya no se necesita activar los campos cedula y nacionalidad
                    $("#form_cliente").submit();                    
                }
                else
                    document.getElementById('pri_nombre').focus();
            }
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar el Cliente?"))
                {                    
                    //ya no se necesita activar los campos cedula y nacionalidad
                    $("#form_cliente").submit();
                    
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
            $('#fecha_nac').val('dd/mm/aaaa');
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
		//$("#form_agregar").submit();		
    });
    
    // llamados a clases boton para editar y eliminar
    $(".eliminar").click(function(e){
        var li = $(this).val();
        if(confirm("¿Realmente desea eliminar el registro?"))
        {
            eliminar(li);
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