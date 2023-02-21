$(document).ready(function(){
 	
    var setDatos = function(){
        $('#modelo').val($('#modelo').val().trim());
        $('#capacidad').val($('#capacidad').val().trim());
        if( $('#modelo').val()=='' || $('#capacidad').val()=='')
        {
            alert('Complete los datos obligatorios ***');
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                if(!confirm("¿Realmente desea guardar el nuevo transporte?"))
                {
                    //$('#guardar').val('gu');
                    $('placa').attr('disabled',false);
                    $("#form_transporte").submit();
                    alert("Registro de transporte exitosamente guardado");
                }
            }//FIN DE LA OPCION GUARDAR
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar el Transporte?"))
                {
                    $('#guardar').val('ed');
                    $('placa').attr('disabled',false);
                    $("#form_transporte").submit();
                    alert("Transporte exitosamente Editado");
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos

    var eliminar = function(ref){
        $.post('/pdval/archivo/transporte/eliminarTransporte/','valor='+ref,function(filas){
        },'json');
    };

    /******LLAMADOS A LOS METODOS PARA MANIPULAR EL FORMULARIO******/
    //lamado al boton desde el id del elemento boton
    $('#agregar').click(function(){
        setDatos();
    });

    $('#limpiar').click(function(){
        location.reload();
    });

    $('#restaurar').click(function(){
        alert("saasd");
    });

    $('#placa').change(function(){
        $('#placa').val($('#placa').val().trim());
        if($('#placa').val()=='')
        {
            alert('Ingrese el número de placa para continuar con el registro.');
        }
        else
        {
            $.post('/pdval/archivo/transporte/comprobarTransporte/','placa=' + $("#placa").val(),function(datos){
                if(datos.total==0)
                {
                    document.getElementById('placa').disabled="false";
                    document.getElementById('modelo').disabled="";
                    document.getElementById('modelo').focus();
                    document.getElementById('marca').disabled="";
                    document.getElementById('tipo').disabled="";
                    document.getElementById('capacidad').disabled="";
                    document.getElementById('medida').disabled="";
                    document.getElementById('agregar').disabled="";
                }
                else
                {
                    alert('Ya existe un transporte con este número de placa.');
                    document.getElementById('placa').focus();
                }
            },'json');
        }
    });

    /*************************************************************/
    // llamados a clases boton para editar y eliminar
    $(".eliminar").click(function(e){
        var li = e.target.parentNode;
        if(confirm("¿Realmente desea eliminar el registro?"))
        {
            eliminar(li.value);
        }
//        location.reload();
    });

    //BLOQUEA TECLA ENTER PARA ENVIO DE FORMULARIO
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            return false;
        }
    });

 });