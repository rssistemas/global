$(document).ready(function(){
    
   var getDatos = function(valor){
       $.post('/pdval/archivo/tipoProveedor/buscarTipoProveedor/','valor=' + valor,function(datos){

                    $('#descripcion').html('');
                    $('#id').html('');
                
                    $('#descripcion').val(datos.nombre_tipo_proveedor);
                    
                    $('#id').val(datos.id_tipo_proveedor);
                    $('#guardar').val('2');
                    

            },'json');
 	}; 
        
        
        
    var eliminar = function(valor){
    $.post('/pdval/archivo/tipoProveedor/eliminarTipoProveedor/','valor=' + valor,function(datos){
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
    
    
    $('img').click(function(e){
        var  valor = e.target.id;
        if(e.target.name == 'editar'){
            if(confirm("Desea realmente editar el registro ..."))
            {
                getDatos(valor);
            }    
        }   
    });  
    
    
});

