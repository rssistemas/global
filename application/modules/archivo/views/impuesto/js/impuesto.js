$(document).ready(function () {
    
	var proyecto ='globalAdm';
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
    	if(confirm("Desea realmente crear nuevo impuesto "))
    	{
        	habilitar_formulario();
        	limpiar_formulario();
        	$('#nombre').focus();
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
    
    
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });
    
    
    
    $("#tipo").change(function(e){
        var val  = this.value;
        
        if(val.length > 0)
        {
        	$("#condicion").val(val);
        }else
        	$("#condicion").val('');
        
    });
    
    
    $("#operador").change(function(e){
        var val  = this.value;
        var tipo = $('#tipo').val();
        
        if( tipo.length > 0 && val.length > 0 )
        {
        	$("#condicion").val(tipo +' '+val);
        }else
        	$("#condicion").val('');
        
    });
    
    $("#valor").change(function(e){
        var val  = this.value;
        var tipo = $('#tipo').val();
        var ope = $('#operador').val();
        
        if( tipo.length > 0  && ope.length > 0 && parseFloat(val) > 0 )
        {
        	$("#condicion").val(tipo +' '+ope+' '+val);
        }else
        	$("#condicion").val(tipo +' '+ope);
        
    });
    
    /*** METODOS PARA INTERACTUAR CON EL CONTROLADOR DE LA VISTA ***/
    //Para fijar valores por incluir nuevo o editar registro existente
    //----------------------------------------------------------------
    var setDatos = function () {
        $('#descripcion').val($('#descripcion').val().trim());
        $('#nombre').val($('#nombre').val().trim());
        $('#condicion').val($('#condicion').val().trim());
        
        if ($('#descripcion').val() == '' || $('#nombre').val()=='' || $('#valor').val()=='' || $('#porcentaje').val() == 0)
        {
            alert('Complete los datos obligatorios *');
            $('#nombre').focus();
        }
        else
        {
            if ($('#guardar').val() == 1) //guarda el registro nuevo
            {                
				if (confirm("¿Realmente desea guardar el nuevo impuesto ?"))
				{
					$("#form_impuesto").submit();
				}      
            }
            if ($('#guardar').val() == 2) // para guardar la edicion del registro
            {
               
                    $.post('/globalAdm/archivo/impuesto/comprobarImpuesto/','valor=' + $("#nombre").val(),function(datos){
                        if(datos.length > 0)
                        {
                            alert("El impuesto que intenta editar presenta conflicto con otra existente.");
                        }else
                        	{
                        		if(confirm("¿Realmente desea editar el impuesto ?"))
                            	{
                                	$("#form_impuesto").submit();
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
				url: '/globalAdm/archivo/impuesto/buscarImpuesto/',
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
    
    
    var restaurar = function(ref){
        if (confirm("¿Realmente desea Restaurar el rubro para producto?"))
        {
            $.post('/pdval/archivo/rubro/estatusRubro/','valor='+ref +'&estatus='+'1', function (filas){
                document.location.reload();
            }, 'json');
        }
    };
	
	/***** FUNCIONES PARA MANEJAR EVENTOS DENTRO DEL FOMULARIO ******/
    //limpia los valores de los campos de texto
    var limpiar_formulario = function(){
        $('#id').val('');
        $('#descripcion').val('');
		$('tipo').val('');
		$('valor').val('0');
		$('#nombre').val('');
        $('#condicion').val('');
        $('#aux').val('');
    };
    
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#nombre').attr('disabled', false);
        $('#descripcion').attr('disabled', false);
        $('#porcentaje').attr('disabled', false);
		$('#accion').attr('disabled', false);
		$('#nombre').attr('disabled', false);
        $('#tipo').attr('disabled', false);
        $('#operador').attr('disabled', false);
        $('#valor').attr('disabled', false);
        $('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#nombre').attr('disabled', true);
        $('#descripcion').attr('disabled', true);
		$('#accion').attr('disabled', true);
        $('#porcentaje').attr('disabled', true);
        $('#nombre').attr('disabled', true);
        $('#tipo').attr('disabled', true);
        $('#operador').attr('disabled', true);
        $('#valor').attr('disabled', true);
        $('#agregar').attr('disabled', true);
        $('#cancelar').attr('disabled', true);
    };
	
	
	
	var comprobar = function(valor){
			
		$.post('/pdval/archivo/rubro/comprobarRubro/', 'valor=' + $("#descripcion").val(), function (cantidad) {
                    if (cantidad.total == 0)
                    {
                        if (confirm("¿Realmente desea guardar el nuevo rubro para producto?"))
                        {
                            $("#form_rubro").submit();
                        }
                    }
                    if (cantidad.total >= 1)
                    {
                        alert("El Rubro para producto que intenta registrar ya existe, no puede ser registrado nuevamente.");
                        document.getElementById('descripcion').focus();
                    }
                }, 'json');
		
		
		
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