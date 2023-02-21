$(document).ready(function(){
	
	//------------------------------------------------------------------------------------------
	//METODO QUE ENVIA FORMULARIO 
	//------------------------------------------------------------------------------------------
	var setDatos = function(){
        $('#descripcion').val($('#descripcion').val().trim());
        
        if($('#descripcion').val()=='')
        {
            alert('Complete los datos obligatorios *');
            document.getElementById('descripcion').focus();
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                $.post('/compra/planificacion/comprobarConcepto/','descripcion=' + $("#descripcion").val(),function(cantidad){
                    if( cantidad.total==0 )
                    {
                        if(confirm("¿Realmente desea guardar el nuevo Concepto de Planificacion?"))
                        {
                            $("#concepto").submit();
                        }
                    }
                    if( cantidad.total>=1 )
                    {
                        alert("El Concepto que intenta registrar ya existe, no puede registrado nuevamente.");
                        document.getElementById('descripcion').focus();
                    }
                },'json');
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                var str1= new String($('#descripcion').val()).toLowerCase();// a minuscula
                var str2= new String($('#aux').val()).toLowerCase();
                if(omitir_tilde(str1)==omitir_tilde(str2))
                {
                    if(confirm("¿Realmente desea editar El Concepto de Planificacion ?"))
                    {
                        $("#concepto").submit();
                    }
                }
                else
                {
                    $.post('/compra/planificacion/comprobarConcepto/','descripcion=' + $("#descripcion").val(),function(cantidad){
                        if( cantidad.total==0 )
                        {
                            if(confirm("¿Realmente desea editar los Datos del Concepto de Planificacion ?"))
                            {
                                $("#concepto").submit();
                            }
                        }
                        if( cantidad.total>=1 )
                        {
                            alert("El concepto que intenta registrar ya existe, no puede ser registrado nuevamente.");
                            document.getElementById('descripcion').focus();
                        }
                    },'json');
                }
            }
        }
    };
	
	//-----------------------------------------------------------------------------------------------
	//METODO QUE CARGA DATOS DE CONCEPTO EN FORMULARIO
	//-----------------------------------------------------------------------------------------------
	var getDatos = function(valor){
       
       $.post('/compra/planificacion/buscarConcepto/','valor=' + valor,function(datos){
           
                    $('#id').html('');
                    $('#descripcion').html('');
                    
                    $('#id').val(datos.id_concepto);                
                    $('#descripcion').val(datos.descripcion_concepto);
					$('#comentario').val(datos.comentario_concepto_pln);
					$('#aux').val(datos.descripcion_concepto);
                    $('#guardar').val('2');
            },'json');
 	};
	
	//----------------------------------------------------------------------------------------------
	//METODO QUE ELIMINA O DESACTIVA CONCEPTO
	//----------------------------------------------------------------------------------------------

    var eliminar = function(valor){
    	
    	if(confirm("¿Realmente desea Eliminar el Concepto?"))
	    {
	    		alert("El registro sera desactivado ...");
	    	    $.post('/compra/planificacion/eliminarConcepto/','valor=' + valor,function(datos){
	            if(datos)
	            {        
	               document.location.reload();
	            }else
	                document.location.reload();
	            },'json');
	    }        
 	};


	//-------------------------------------------------------------------------------------------
	//ACTIVA CAMPOS DEL FORMULARIO "CONCEPTO PLANIFICACION"
	//-------------------------------------------------------------------------------------------
	var activarCampos = function(){
		$('#descripcion').attr('disabled',false);
		$('#comentario').attr('disabled',false);
				
	};
	//------------------------------------------------------------------------------------------
	//DESACTIVA CAMPOS DEL FORMULARIO "CONCEPTO PLANIFICACION"
	//------------------------------------------------------------------------------------------
	var desactivarCampos = function(){
		$('#descripcion').attr('disabled',true);
		$('#comentario').attr('disabled',true);		
	};
	//------------------------------------------------------------------------------------------
	//METODO QUE LIMPIA VALORES DEL FOMULARIO
	//------------------------------------------------------------------------------------------
	 var limpiar_formulario = function(){
        $('#id').val('');
        $('#descripcion').val('');
		$('#comentario').val('');
        $('#aux').val('');        
    };	
	//--------------------------------------------------------------------------------------------
	//METODO QUE PREPARA FORMULARIO PARA REGISTRO NUEVO
	//--------------------------------------------------------------------------------------------
	$('#nuevo').click(function(){
		activarCampos();
		limpiar_formulario()
		$('#descripcion').focus();	
	});
		

    $(".eliminar").click(function(e){
        var li = $(this).val();
         
            eliminar(li);       
    });
	
	
	 //boton cancelar
    $('#cancelar').click(function(){
    	bloquear_formulario();
        limpiar_formulario();
    
    });
    //boton guardar
     $('#agregar').click(function(){
        setDatos();
    });
    
    //boton editar
    $(".editar").click(function(e){
        var li = $(this).val();
        activarCampos()
        getDatos(li);
        
    });    
	
	
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
	//-----------------------------------------------------------------------------------------------
    //Bloquea tecleo de ENTER
	//-----------------------------------------------------------------------------------------------
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
	
	

});