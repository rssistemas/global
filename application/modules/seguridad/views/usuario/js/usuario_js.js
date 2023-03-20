$(document).ready(function(){
    /******LLAMADOS A LOS METODOS PARA MANIPULAR EL FORMULARIO******/
    //lamado al boton desde el id del elemento boton
    $('#agregar').click(function(){
        setDatos();
    });  
     //boton agregar recurso
    $('#agregar_recurso').click(function(){
        setRecurso();
    });
    
    
     //PARA VALIDAR LA ASIGNACION DE RECURSOS AL ROL 
    var setRecurso = function(){
        if($('#recurso').val()=='0' )
        {
            alert('Complete los datos obligatorios *');
        }
        else
        {
            if($('#guardar').val()==1) //guarda el registro nuevo
            {
                if(confirm("¿Realmente desea Asignar este recurso al Usuario?"))
                {
                    $("#form-permiso").submit();
                }
            }
            if($('#guardar').val()==2) // para guardar la edicion del registro
            {
                if(confirm("¿Realmente desea Editar este recurso al Usuario?"))
                {
                    $("#form-permiso").submit();
                }
            }//FIN DE LA OPCION EDITAR 2
        }//FIN DEL ELSE PRINCIPAL DE LA FUNCION
    };  //FIN DE LA FUNCION setRecurso
    
/******** PARA ENVIAR LOS VALORES DE LOS ELEMENTOS DEL FORMULARIO AL CONTROLADOR**********/
    var setDatos = function(){
        $('#respuesta').val($('#respuesta').val().trim());
        $('#pwd1').val($('#pwd1').val().trim());
        $('#pwd2').val($('#pwd2').val().trim());
        $('#correo').val($('#correo').val().trim());
        $('#telefono').val($('#telefono').val().trim());
        if($('#respuesta').val()=='' || $('#correo').val()=='' || $('#rol').val()=='0' || $('#pregunta').val()=='0')
        {
            alert('Complete los datos obligatorios *');
        }
        else
        {
			if($('#pwd1').val() == $('#pwd2').val() )
			{			
				if($('#guardar').val()==1) //guarda el registro nuevo
				{
					if(confirm("¿Realmente desea guardar el nuevo usuario?"))
					{
						$("#form_usuario_agregar").submit();                    
					}
				}//FIN DE LA OPCION GUARDAR NUEVO 1
			}else
				{
					aler("Las contraseñas son diferentes .... ! Corrija para continuar ");
					$('#pwd1').focus();
				}
            if($('#guardar').val()==2) //guarda el registro editado
            {
                if(confirm("¿Realmente desea editar el usuario?"))
                {                    
                    $("#form_usuario_editar").submit();
                }
            }//FIN DE LA OPCION EDITAR 2
        }
    };  //FIN DE LA FUNCION setDatos
    
    
	

    
/***EVENTO DESPUES DE LA INTRODUCCION DEL LOGIN *****/
    $('#login_usuario').change(function(){
        if(!$('#login_usuario').val())
        {
           $('#login_usuario').html('');
        }
        else
            getLogin();
                
        });
        
/***EVENTO DESPUES DE LA INTRODUCCION DE LA DIRECCION DE CORREO *****/
    $('#correo').change(function(){
        if(!$('#correo').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }
        //else
            // getCorreo();
    });
    
    var getCorreo = function(){ 
        if(!(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/.test($('#correo').val())))
        {
            alert("Formato no permitido, ingrese correctamente su correo electrónico.");
            $('#correo').val('');
            $('#correo').focus();
        }
        else
        {
            $.post('/pdval/seguridad/usuario/comprobarCorreo/','correo=' + $("#correo").val() ,function(datos){
                if(datos.total >0)
                {
                    alert("El correo electrónico que ingreso ya esta en uso, introduzca otro.");
                    document.getElementById('correo').value="";
                    document.getElementById('correo').focus();
                }
            },'json');
        } 
    };

/***EVENTO DESPUES DE LA INTRODUCCION DE LA CLAVE 1 *****/
    $('#clave1').change(function(){
        if(!(/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/.test($('#clave1').val())))
        {
            alert("Su clave debe contener Mayuscula, minuscula, número, simbolo y la longitud minima es de 8 caracteres.");
            $('#clave1').val('');
            $('#clave1').focus();
        }
    });

/***EVENTO DESPUES DE LA INTRODUCCION DE LA CLAVE 1 *****/
    $('#clave2').change(function(){
        if(!(/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/.test($('#clave2').val())))
        {
            alert("Su clave debe contener Mayuscula, minuscula, número, simbolo y la longitud minima es de 8 caracteres.");
            $('#clave2').val('');
            $('#clave2').focus();
        }
        else
        {
            if($('#clave2').val() != $('#clave1').val())
            {
                alert("Vuelva a introducir la clave, estas deben ser iguales.");
                $('#clave2').val('');
                $('#clave2').focus();
            }
        }
    });
  
 //FUNCION QUE ELIMINA LOGICAMENTE EL USUARIO DESDE EL INDEX DEL USUARIO
    $(document).on('click','.eliminar',function(e){
        var valor = this.value;
        if(confirm('¿Realmente desea Eliminar el registro del usuario?'))
        {
           $.ajax( {  
                url: '/pdval/seguridad/usuario/eliminarUsuario/',
                type: 'POST',
                dataType : 'json',
                async: false,
                data: 'codigo='+ valor,
                success:function(datos){
                      if(!datos)
                        {
                            alert("Problemas eliminando usuario, vuelva a interntarlo nuevamente.");
                        }else
                        {
                            alert("El Usuario eliminado corectamente");
                        }
                },
                error: function(xhr, status) {
                        alert('Disculpe, existió un problema');
                        }
                });
            location.reload();   
       }
    });
        
    //BLOQUEA LA TECLA ENTER
    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.which == 13)
        {
            e.preventDefault();
            return false;
        }
    });
    
    
    
        


    
 });
