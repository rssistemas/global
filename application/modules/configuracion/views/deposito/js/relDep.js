$(document).ready(function(){
    
   
        $(".editar").click(function(e){
        var li = e.target.parentNode;
        if(confirm("Realmente desea Moodificar el registro ...."))
        {
            $('#guardar').val('2');
            $('#trabajador').attr('disabled',false);
            $('#cancelar').attr('disabled',false);
            $('#enviar').attr('disabled',false);
            $('#nombre').focus();
            getDatos(li.value);
        }    
    });   
    
    
     $("#agregar").click(function(){
        if(confirm("Realmente desea crear  una ubicacion al trabajador   ...."))
        { 
            $('#guardar').val('1');
            $('#trabajador').attr('disabled',false);            
            $('#cancelar').attr('disabled',false);
            $('#enviar').attr('disabled',false);
            $('#nombre').focus();
        }
        
    });
    $("#cancelar").click(function(){
         
            $('#guardar').val('1');
            $('#trabajador').attr('disabled',true);
            $('#cancelar').attr('disabled',true);
            $('#enviar').attr('disabled',true);
            $('#nombre').val('');
            
        
        
    });
    
    $("#enviar").click(function(){
        $('#relacion').submit();
    }); 
    
    
});