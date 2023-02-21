$(document).ready(function(){
    
   var getDatos = function(valor){
       $.post('/pdval/configuracion/cargo/buscarCargo/','valor=' + valor,function(datos){
                $('#id').html('');
                $('#descripcion').html('');
               // $('#medida').html('');
               
                $('#id').val(datos.id_cargo);                
                $('#descripcion').val(datos.nombre_cargo);
                $('#medida').val(datos.departamento_id);              
                
                $('#guardar').val('2');
            },'json');
 	}; 
       
    
    var eliminar = function(valor){
        $.post('/pdval/configuracion/cargo/eliminarCargo/','valor=' + valor,function(datos){
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
    if(confirm("¿Realmente desea eliminar el registro?"))
    { 
        eliminar(li.value);
    }    
    });
        
    
});

