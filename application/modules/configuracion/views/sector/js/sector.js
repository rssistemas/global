$(document).ready(function(){

   var getDatos = function(valor){
       $.post('/configuracion/sector/buscarSector/','valor=' + valor,function(datos){
                $('#id').html('');
                $('#descripcion').html('');
                $('#aux').html('');

                $('#id').val(datos.id_sector);
                $('#descripcion').val(datos.descripcion_sector);
                $('#aux').val(datos.descripcion_sector);

				        $("#municipio option[value="+ datos.id_municipio +"]").attr("selected",true);
                $("#estado option[value="+ datos.estado_id +"]").attr("selected",true);
                $('#guardar').val('2');


            },'json');
 	};

 	var getEstados = function(){

 		$.post('/configuracion/sector/buscarEstados/','valor=0' ,function(datos){
                if(datos.length > 0)
                {
                	$("#estado").html('');
                	$('#estado').append('<option value="" >-Seleccione-</option>');
                	var cadena="";
                	for(i=0;i < datos.length;i++)
                	{
                		cadena = datos[i].descripcion_estado.toUpperCase();
                		$("#estado").append("<option value='"+datos[i].id_estado+"'>"+cadena+"</option>");
                	}

                }
                //$("#municipio option[value="+ datos.estado_id +"]").attr("selected",true);
                //$("#municipio").attr("disabled",true);

            },'json');
 	};

    var getMunicipios = function(){
    	$.post('/configuracion/sector/buscarMunicipios/','valor=' + $("#estado").val(),function(datos){
                if(datos.length > 0)
                {
                	$("#municipio").html('');
                	$('#municipio').append('<option value="0" >-Seleccione-</option>');
                	var cadena="";
                	for(i=0;i < datos.length;i++)
                	{
                		cadena = datos[i].descripcion_municipio.toUpperCase();
                		$("#municipio").append("<option value='"+datos[i].id_municipio+"'>"+cadena+"</option>");
                	}

                }
                //$("#municipio option[value="+ datos.estado_id +"]").attr("selected",true);
                //$("#municipio").attr("disabled",true);

            },'json');

    };

    var getParroquias = function(){

    	$.post('/configuracion/parroquia/buscarParroquias/','valor=' + $("#estado").val(),function(datos){
                if(datos.length > 0)
                {
                	$("#parroquia").html('');
                	$('#parroquia').append('<option value="0" >-Seleccione-</option>');
                	var cadena="";
                	for(i=0;i < datos.length;i++)
                	{
                		cadena = datos[i].descripcion_parroquia.toUpperCase();
                		$("#parroquia").append("<option value='"+datos[i].id_parroquia+"'>"+cadena+"</option>");
                	}

                }
                //$("#municipio option[value="+ datos.estado_id +"]").attr("selected",true);
                //$("#municipio").attr("disabled",true);

            },'json');

    };


  var eliminar = function(valor){
        $.post('/pdval/configuracion/sector/eliminarSector/','valor=' + valor,function(datos){
            if(datos)
            {
               document.location.reload();
            }else
                document.location.reload();
            },'json');
 	};

      //.nombre de la clase asignada al boton

    $(".eliminar").click(function(e){
        var li = $(this).val();

            eliminar(li);

    });


     //boton agregar nuevo
    $(".nuevo").click(function(e){
        habilitar_formulario();
        limpiar_formulario();
        getEstados();
        $('#descripcion').focus();
    });

    //boton cancelar
    $('#cancelar').click(function(){
    	bloquear_formulario();
        limpiar_formulario();

    });
    //boton guardar
     $('#agregar').click(function(){
        $("#estado").attr("disabled",true);
        setDatos();
    });

    //boton editar
    $(".editar").click(function(e){

        var li = $(this).val();
        habilitar_formulario();
        getEstados()
        getMunicipios();
        getDatos(li);

    });

   //metodo que carga los municiopios
    $("#estado").change(function(){
    	getMunicipios();

    });

    //metodo que carga las parroquias
	$("#municipio").change(function(){
    	//getParroquias;
    });

     var limpiar_formulario = function(){
        $('#id').val('');
        $('#descripcion').val('');
        $('#aux').val('');
		    $('#estado').html('');
		    $('#municipio').html('');

    };
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#descripcion').attr('disabled', false);
        $('#estado').attr('disabled', false);
        $('#municipio').attr('disabled', false);
       	$('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#descripcion').attr('disabled', true);
        $('#estado').attr('disabled', true);
        $('#municipio').attr('disabled', true);
        $('#agregar').attr('disabled', true);
        $('#cancelar').attr('disabled', true);
    };

 	var setDatos = function(){
        $('#descripcion').val($('#descripcion').val().trim());


        if($('#descripcion').val()=='' || $('#municipio').val()=='')
        {
            alert('Complete los datos obligatorios *');
            document.getElementById('parroquia').focus();
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                $.post('/configuracion/sector/comprobarSector/','municipio='+$('#municipio').val()+'&descripcion=' + $("#descripcion").val(),function(cantidad){
                    if( cantidad.total==0 )
                    {
                        if(confirm("¿Realmente desea guardar el nuevo Sector?"))
                        {
                            $("#sector").submit();
                        }
                    }
                    if( cantidad.total>=1 )
                    {
                        alert("El Sector que intenta registrar ya existe, no puede registrado nuevamente.");
                        document.getElementById('descripcion').focus();
                    }
                },'json');
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                var str1= new String($('#nombre').val()).toLowerCase();// a minuscula
                var str2= new String($('#aux').val()).toLowerCase();
                if(omitir_tilde(str1)==omitir_tilde(str2))
                {
                    if(confirm("¿Realmente desea editar El nombre del sector ?"))
                    {
                        $("#sector").submit();
                    }
                }
                else
                {
                    $.post('/configuracion/sector/comprobarSector/','parroquia='+$('#parroquia').val()+'&descripcion=' + $("#descripcion").val(),function(cantidad){
                        if( cantidad.total==0 )
                        {
                            if(confirm("¿Realmente desea editar los Datos del sector ?"))
                            {
                                $("#sector").submit();
                            }
                        }
                        if( cantidad.total>=1 )
                        {
                            alert("El sector que intenta registrar ya existe, no puede ser registrado nuevamente.");
                            document.getElementById('municipio').focus();
                        }
                    },'json');
                }
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos
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
        };

    })();
    //Bloquea tecleo de ENTER
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
});
