$(document).ready(function () {
    
	
    $('#agregar').click(function(){
        setDatos();
    });
    //boton cancelar
    $('#cancelar').click(function(){
        
    });
	
	/*** Llamado al botones tipo clase desde el nombre de la clase (.nombre_clase) ***/
    //boton editar
    $(".editar").click(function(){
        var li = this.value;
        if(confirm("Desea editar el impuesto ? ")){
        	getDatos(li);
        	$('#guardar').val('2');
        	$('#nombre').focus();	
        }
        	
        
    });    
    
    
    //boton agregar nuevo
    $(".nuevo").click(function(e){
    	if(confirm("Desea realmente crear nuevo Servicio "))
    	{
        	habilitar_formulario();
        	limpiar_formulario();
        	$('#clasificacion').focus();
       }
    });
    
    
    //boton eliminar
    $(".eliminar").click(function(e){
        var li = this.value;
        if(confirm("Desea realmente eliminar impuesto"))
        {
        	eliminar(li);	
        }
        
    });
    
    
    
    
    /*** METODOS PARA INTERACTUAR CON EL CONTROLADOR DE LA VISTA ***/
    //Para fijar valores por incluir nuevo o editar registro existente
    //----------------------------------------------------------------
    var setDatos = function () {
        $('#comentario').val($('#comentario').val().trim());
        $('#nombre').val($('#nombre').val().trim());
        
        if ($('#comentario').val() == '' || $('#nombre').val()=='' || $('#clasificacion').val()==0 || $('#grupo').val() == 0)
        {
            alert('Complete los datos obligatorios *');
            $('#grupo').focus();
        }
        else
        {
            if ($('#guardar').val() == 1) //guarda el registro nuevo
            {                
				if (confirm("¿Realmente desea guardar el nuevo Servicio ?"))
				{
					$("#form_servicio").submit();
				}      
            }
            if ($('#guardar').val() == 2) // para guardar la edicion del registro
            {
               
                    $.post('/archivo/servicio/comprobarServicio/','valor=' + $("#nombre").val(),function(datos){
                        if(datos.length > 0)
                        {
                            alert("El impuesto que intenta editar presenta conflicto con otra existente.");
							$('#nombre').focus();
						}else
                        	{
                        		if(confirm("¿Realmente desea editar el Servicio ?"))
                            	{
                                	$("#form_servicio").submit();
                            	}
                        	}
                        
                    },'json');
                
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos

	//---------------------------------------------------------------------------------------------------
	//METODO QUE ENVIAR DATOS A LA VISTA DEL FORMULARIO
	//---------------------------------------------------------------------------------------------------
    var getDatos = function (valor) {
		$.ajax( 
			{  
				url: '/archivo/servicio/buscarServicio/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'valor='+parseInt(valor),
				success:function(datos){
					  if(datos)
						{
							limpiar_formulario();
							
							$('#id').val(datos.id_impuesto);
							$('#nombre').val(datos.nombre_impuesto);
							$('#descripcion').val(datos.descripcion_impuesto);
							$('#porcentaje').val(datos.tasa_impuesto);
							$('#valor').val(datos.comparador_impuesto);
							//$('#comentario').val(datos.comentario_premisa);
							$('#condicion').val(datos.tipo_impuesto+' '+datos.operador_impuesto+' '+datos.comparador_impuesto);
							$("#operador option[value="+ datos.operador_impuesto +"]").attr("selected",true);
							$("#tipo option[value="+ datos.tipo_impuesto +"]").attr("selected",true);
							$("#accion option[value="+ datos.accion_impuesto +"]").attr("selected",true);
							$('#aux').val(datos.nombre_impuesto);							
							
							
							habilitar_formulario();
							
						}else
						{
							limpiar_formulario();
							bloquear_formulario();
						}
				},
				error: function(xhr, status) {
						alert('Disculpe, existió un problema buscando premisa');
						}
			});
		
        
    };  //FIN DE LA FUNCION getDatos

    
	var getGrupo = function(){
			
		$.post('/archivo/servicio/cargarGrupo/', 'valor=' + $("#clasificacion").val(), function (datos) {
			if(datos.length > 0)
			{
				    $('#grupo').html("");
					$('#grupo').append('<option value="" >-Seleccione-</option>');                            
					for(i=0; i < datos.length;i++)
					{
						 $('#grupo').append('<option value="'+datos[i].id_grupo+'" >'+datos[i].nombre_grupo+'</option>');
						
					} 	
					
			}
                    
        }, 'json');
		
		
		
	};
	
	
	
    var eliminar = function (ref) {        
        $.post('/globalAdm/venta/premisa/relacionPremisa/', 'valor=' + ref, function (resultado) {
            if(resultado.length > 0)
            {
                alert("El registro ya se encuentra en uso, no puede ser eliminado.");
            }
            else               
            	{
	                $.post('/globalAdm/venta/premisa/eliminarPremisa/','valor='+ref, function (filas){
	                    document.location.reload();
	                }, 'json');
            	}
        }, 'json');
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
        $('#clasificacion').attr('disabled', false);
        $('#grupo').attr('disabled', false);
		$('#comentario').attr('disabled', false);
		
        $('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#nombre').attr('disabled', true);
        $('#clasificacion').attr('disabled', true);
		$('#grupo').attr('disabled', true);
        $('#comentario').attr('disabled', true);
        
        $('#agregar').attr('disabled', true);
        $('#cancelar').attr('disabled', true);
    };
	
	
	
		
	
	$('#clasificacion').change(function(){
		getGrupo();		
	});
	
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
});  //FIN DEL JS DE LA VISTA