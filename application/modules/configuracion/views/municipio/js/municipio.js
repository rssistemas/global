$(document).ready(function(){
    
   var getDatos = function(valor){
       $.post('/configuracion/municipio/buscarMunicipio/','valor=' + valor,function(datos){
                $('#id').html('');
                $('#descripcion').html('');
                $('#aux').html('');
                
                $('#id').val(datos.id_municipio);                
                $('#descripcion').val(datos.descripcion_municipio);
                $('#aux').val(datos.descripcion_municipio);
                $("#estado option[value="+ datos.estado_id +"]").attr("selected",true);
                $("#estado").attr("disabled",true);
                
                $('#guardar').val('2');
            },'json');
 	}; 
        
  var eliminar = function(valor){
        
        if(confirm("¿Realmente desea eliminar el  Municipio?"))
        {
        	alert("El Municipio se desactivara ....");
	        $.post('/configuracion/municipio/eliminarMunicipio/','valor=' + valor,function(datos){
	            if(datos)
	            {        
	               document.location.reload();
	            }else
	                document.location.reload();
	            },'json');
	            
	     }
	     
 	};    
     
    
    
    $(".boton").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });
     //.nombre de la clase asignada al boton

    $(".eliminar").click(function(e){
        var li = $(this).val();
         
            eliminar(li);
          
    });
        
    //boton agregar nuevo
    $(".nuevo").click(function(e){
        habilitar_formulario();
        limpiar_formulario();
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
        getDatos(li);
        
    });    
    
     var limpiar_formulario = function(){
        $('#id').val('');
        $('#descripcion').val('');
        $('#aux').val('');        
    };
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#descripcion').attr('disabled', false);
        $('#estado').attr('disabled', false);
       	$('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#descripcion').attr('disabled', true);
        $('#estado').attr('disabled', true);
        $('#agregar').attr('disabled', true);
        $('#cancelar').attr('disabled', true);
    }; 

 	var setDatos = function(){
        $('#descripcion').val($('#descripcion').val().trim());
        
        
        if($('#descripcion').val()=='' || $('#estado').val()=='')
        {
            alert('Complete los datos obligatorios *');
            document.getElementById('estado').focus();
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                $.post('/configuracion/municipio/comprobarMunicipio/','descripcion=' + $("#descripcion").val()+'&estado='+$('#estado').val(),function(cantidad){
                    if( cantidad.total==0 )
                    {
                        if(confirm("¿Realmente desea guardar el nuevo Municipio?"))
                        {
                            $("#municipio").submit();
                        }
                    }
                    if( cantidad.total>=1 )
                    {
                        alert("El Municipio que intenta registrar ya existe, no puede registrado nuevamente.");
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
                    if(confirm("¿Realmente desea editar El nombre del Municipio ?"))
                    {
                        $("#municipio").submit();
                    }
                }
                else
                {
                    $.post('/configuracion/municipio/comprobarMunicipio/','descripcion=' + $("#descripcion").val(),function(cantidad){
                        if( cantidad.total==0 )
                        {
                            if(confirm("¿Realmente desea editar los Datos del Municipio ?"))
                            {
                                $("#municipio").submit();
                            }
                        }
                        if( cantidad.total>=1 )
                        {
                            alert("El Municipio que intenta registrar ya existe, no puede ser registrado nuevamente.");
                            document.getElementById('estado').focus();
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

