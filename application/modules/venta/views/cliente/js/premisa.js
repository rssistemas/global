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
    	limpiar_formulario();
    	bloquear_formulario();
    	
        //location.reload();
    });
/*** Llamado al botones tipo clase desde el nombre de la clase (.nombre_clase) ***/
    //boton editar
    $(".editar").click(function(){
        var li = this.value;
        if(confirm("Desea editar la premisa ? ")){
        	getDatos(li);
        	$('#guardar').val('2');
        	$('#nombre').focus();	
        }
        	
        
    });    
    //boton agregar nuevo
    $(".nuevo").click(function(e){
    	if(confirm("Desea realmente crear nueva premisa de venta"))
    	{
        	habilitar_formulario();
        	limpiar_formulario();
        	$('#nombre').focus();
       }
    });
    //boton eliminar
    $(".eliminar").click(function(e){
        var li = this.value;
        if(confirm("Desea realmente eliminar la premisa"))
        {
        	eliminar(li);	
        }
        
    });
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });
/*** METODOS PARA INTERACTUAR CON EL CONTROLADOR DE LA VISTA ***/
    //Para fijar valores por incluir nuevo o editar registro existente
    var setDatos = function () {
        
        if ( $('#premisa').val() == 0)
        {
            alert('Complete los datos obligatorios *');
            $('#premisa').focus();
        }
        else
        {
            if ($('#guardar').val() == 1) //guarda el registro nuevo
            {                
				if (confirm("¿Realmente desea asignar la  premisa ?"))
				{
					$("#form-premisa").submit();
				}      
            }
            if ($('#guardar').val() == 2) // para guardar la edicion del registro
            {
               
                    $.post('/globalAdm/venta/premisa/comprobarPremisa/','valor=' + $("#nombre").val(),function(datos){
                        if(datos.length > 0)
                        {
                            alert("La premisa que intenta editar presenta conflicto con otra existente.");
                        }else
                        	{
                        		if(confirm("¿Realmente desea editar la premisa?"))
                            	{
                                	$("#form-premisa").submit();
                            	}
                        	}
                        
                    },'json');
                
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos

    //ENVIAR DATOS A LA VISTA DEL FORMULARIO
	
	
	 
	
    var getDatos = function (valor) {
		$.ajax( 
			{  
				url: '/globalAdm/venta/cliente/buscarPremisaCliente/',
				type: 'POST',
				dataType : 'json',
				async: false,
				data: 'premisa='+valor+'&cliente='+$('#cliente').val(),
				success:function(datos){
					  if(datos)
						{
							limpiar_formulario();
							
							$('#id').val(datos.id_det_cliente);
							$("#premisa option[value="+ datos.id_premisa +"]").attr("selected",true);
							$('#aux').val(datos.nombre_premisa);							
							
							
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
        $('#premisa').val('');		
        $('#aux').val('');
    };
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        
        $('#premisa').attr('disabled', false);
        $('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        
        $('#premisa').attr('disabled', true);
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