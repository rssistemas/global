$(document).ready(function(){
    
   var getDatos = function(valor){
       //alert(valor);
       $.post('/pdval/configuracion/departamento/buscarDepartamento/','valor=' + valor,function(datos){
            $('#descripcion').html('');
            $('#telefono').html('');
            $('#id').html('');

            $('#descripcion').val(datos.descripcion_departamento);
            $('#telefono').val(datos.telefono_departamento);                  
            $('#id').val(datos.id_departamento);
            $('#guardar').val('2');
            },'json');
 	}; 
        
    var eliminar = function(valor){
        $.post('/pdval/configuracion/departamento/eliminarDepartamento/','valor=' + valor,function(datos){
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