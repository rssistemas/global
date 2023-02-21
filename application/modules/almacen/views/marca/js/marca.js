$(document).ready(function(){

    var setDatos = function(){
        $('#descripcion').val($('#descripcion').val().trim());
        if($('#descripcion').val()=='')
        {
            alert('Ingrese los datos obligatorios ***');
            document.getElementById('descripcion').focus();
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                $.post('/globalAdm/almacen/marca/comprobarMarca/','valor=' + $("#descripcion").val(),function(cantidad){
                if( cantidad.total==0 )
                {
                    if(confirm("¿Realmente desea guardar la nueva marca de producto?"))
                    {
                        $("#marca").submit();
                        alert("Marca exitosamente guardada");
                    }
                    else
                        document.location.reload();
                }
                if( cantidad.total>=1 )
                {
                    alert("La marca de producto que intenta registrar ya existe, no puede registrado nuevamente.");
                    document.getElementById('descripcion').focus();
                }
                },'json');
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                $.post('/globalAdm/almacen/marca/comprobarMarca/','valor=' + $("#descripcion").val(),function(cantidad){
                if( cantidad.total==0 )
                {
                    if(confirm("¿Realmente desea editar la marca de producto?"))
                    {
                        $("#marca").submit();
                        alert("Marca de producto exitosamente Editada");
                    }
                    else
                        document.location.reload();
                }
                if( cantidad.total>=1 )
                {
                    if($("#descripcion").val()==$("#aux").val())
                    {
                        alert("La marca se guardara sin modificación.");
                        $("#marca").submit();
                    }
                    else
                    {
                        if(confirm("¿Realmente desea editar la marca de producto?"))
                        {
                            $("#marca").submit();
                            alert("Marca de producto exitosamente Editada");
                        }
                        else
                            document.location.reload();
                    }
                }
                },'json');
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setDatos

    var getDatos = function(valor){
        $.post('/globalAdm/almacen/marca/buscarMarca/','valor=' + valor,function(datos){
        	
            $('#id').html('');
            $('#descripcion').html('');
            $('#aux').html('');
            $('#id').val(datos.id_marca);
            $('#descripcion').val(datos.nombre_marca);
            $('#aux').val($('#descripcion').val());
            $('#guardar').val('2');
        },'json');
    };  //FIN DE LA FUNCION getDatos

    var eliminar = function(ref){
        $.post('/globalAdm/almacen/marca/eliminarMarca/','valor=' + ref,function(filas){
        },'json');
    };


/******LLAMADOS A LOS METODOS PARA MANIPULAR EL FORMULARIO******/
    //lamado al boton desde el id del elemento boton
     $('#agregar').click(function(){
        setDatos();
    	});
    
	    $('#cancelar').click(function(){
	        if(confirm("Desea cancelar la operacion ..")){
	        bloquear_formulario();
	        limpiar_formulario();
	       }
	       // location.reload();
	    });
    
     $(".editar").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });    
    //boton agregar nuevo
    $(".nuevo").click(function(e){
    	if(confirm("Desea crear una  marca")){
        habilitar_formulario();
        limpiar_formulario();
       }
    });
    //boton eliminar
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        eliminar(li.value);
    });
    $(".restaurar").click(function(e){
        var li = e.target.parentNode;
        restaurar(li.value);
    });
    
    //Bloquea tecleo de ENTER
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
    
    /***** FUNCIONES PARA MANEJAR EVENTOS DENTRO DEL FOMULARIO ******/
    //limpia los valores de los campos de texto
    var limpiar_formulario = function(){
        $('#id').val('');
        $('#descripcion').val('');
        
        $('#aux').val('');
    };
    //habilita los elementos del formulario
    var habilitar_formulario = function(){
        $('#descripcion').attr('disabled', false);
        
        $('#agregar').attr('disabled', false);
        $('#cancelar').attr('disabled', false);
    };
    //bloquea los elementos del formulario
    var bloquear_formulario = function(valor){
        $('#descripcion').attr('disabled', true);
        
        $('#agregar').attr('disabled', true);
        $('#cancelar').attr('disabled', true);
	};

});  //FIN DEL JS DE LA VISTA