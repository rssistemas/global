$(document).ready(function(){
    
   
        $(".editar").click(function(e){
        var li = e.target.parentNode;
        if(confirm("¿Realmente desea Moodificar el registro del Producto?"))
        {
            $('#guardar').val('2');
            $('#nombre').attr('disabled',false);
            $('#cancelar').attr('disabled',false);
            $('#enviar').attr('disabled',false);
            $('#nombre').focus();
            getDatos(li.value);
        }    
    });   
    
    
     $("#agregar").click(function(){
        if(confirm("¿Realmente desea Asignar la Presentación para el Producto?"))
        { 
            $('#guardar').val('1');
            $('#nombre').attr('disabled',false);            
            $('#cancelar').attr('disabled',false);
            $('#enviar').attr('disabled',false);
            $('#nombre').focus();
        }
        
    });
    $("#cancelar").click(function(){
            $('#guardar').val('1');
            $('#nombre').attr('disabled',true);
            $('#cancelar').attr('disabled',true);
            $('#enviar').attr('disabled',true);
            $('#nombre').val('');
    });
    
    $("#enviar").click(function(){
        $('#relacion').submit();
    }); 
    
    
});