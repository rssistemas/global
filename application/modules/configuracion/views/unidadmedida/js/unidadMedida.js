$(document).ready(function(){
    
   var getDatos = function(valor){
       $.post('/pdval/configuracion/unidadMedida/buscarUnidadMedida/','valor=' + valor,function(datos){

                    $('#descripcion').html('');
                    $('#simbolo').html('');
                    $('#id').html('');
                
                    $('#descripcion').val(datos.nombre_uni_med);
                    $('#simbolo').val(datos.simbolo_uni_med);
                    $('#id').val(datos.id_uni_med);
                    $('#guardar').val('2');
                    

            },'json');
 	}; 
       var eliminar = function(valor){
        $.post('/pdval/configuracion/unidadMedida/eliminarUnidadMedida/','valor=' + valor,function(datos){
            if(datos)
            {        
               document.location.reload();
            }else
                document.location.reload();
            },'json');
 	}; 
        
    $(".boton").click(function(e){
        var li = e.target.parentNode;
        getDatos(li.value);
    });
    
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        if(confirm("Realmente desea eliminar el registro ...."))
        { 
            eliminar(li.value);
        }    
    });    
    
});

