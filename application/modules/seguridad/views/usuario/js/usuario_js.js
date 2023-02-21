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
        $('#cedula').val($('#cedula').val().trim());
        $('#respuesta').val($('#respuesta').val().trim());
        $('#pwd1').val($('#pwd1').val().trim());
        $('#pwd2').val($('#pwd2').val().trim());
        //$('#email').val($('#email').val().trim());
        if($('#cedula_persona').val()=='' || $('#respuesta').val()=='' || $('#correo').val()=='' || $('#rol').val()=='0' || $('#pregunta').val()=='0' )
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
    
    
	

//---------------------------------------------------
/***EVENTO VALIDA CORREO DE USUARIO*****/
//-----------------------------------------------------
    $('#correo').change(function(){
        if(!$('#correo').val())
        {
            //$('#nombre_representante').html('sin resultados');
        }
        else
        {
            $.ajax( {  
                url: '/seguridad/usuario/buscarUsuario/',
                type: 'POST',
                dataType : 'json',
                async: false,
                data: 'cedula=' + $("#cedula").val()+'&tipo=' + $("#nacionalidad").val(),
                success:function(datos){
                        if(!datos)
                        {
                            $.post('/seguridad/usuario/buscarPersona/','cedula=' + $("#cedula").val()+'&tipo=' + $("#nacionalidad").val(),function(datosPer){
                                if(datosPer)
                                {
                                    $('#id_persona').val(datosPer.id_persona);
                                    $('#pri_nombre').val(datosPer.pri_nombre_persona);
                                    $('#seg_nombre').val(datosPer.seg_nombre_persona);
                                    $('#pri_apellido').val(datosPer.pri_apellido_persona);
                                    $('#seg_apellido').val(datosPer.seg_apellido_persona);
                                    $('#fecha_nacimiento').val(datosPer.fecha_nac_persona);
                                    $('#lugar_nacimiento').val(datosPer.lugar_nac_persona);
                                    $("#sexo option[value="+ datosPer.sexo_persona +"]").attr("selected",true);
                                    $('#rol').attr('disabled',false);
                                    $('#correo').attr('disabled',false);
                                    $('#pregunta').attr('disabled',false);
                                    $('#respuesta').attr('disabled',false);
                                }
                                else
                                {
                                    activarCampos();	
									document.getElementById('pri_nombre').focus();
                                }
                            },'json');    
                        }
                        else
                        {
                            alert("El Usuario ya se encuentra registrado.");
                            document.getElementById('cedula').value="";
                            document.getElementById('cedula').focus();
                        }
                    },
                    error: function(xhr, status) {
                            alert('Disculpe, existió un problema');
                            }
            });    
        }//FIN DEL ELSE PRINCIPAL
    });
    
/***EVENTO DESPUES DE LA INTRODUCCION DEL LOGIN *****/
    $('#login_usuario').change(function(){
        if(!$('#login_usuario').val())
        {
           $('#login_usuario').html('');
        }
        else
            getLogin();
                
        });
        
    var getLogin = function(){
        $.post('/pdval/seguridad/usuario/comprobarAlias/','nombre=' + $("#login_usuario").val() ,function(datos){
            if(datos)
            {
                if(datos.total > 0)
                {    
                    alert("El login de usuario ya esta registrado, ingrese otro nuevamente.");
                    document.getElementById('login_usuario').value="";
                    document.getElementById('login_usuario').focus();
                }    
            }
        },'json');
    };   

/***EVENTO DESPUES DE LA INTRODUCCION DE LA DIRECCION DE CORREO *****/
    $('#correo').change(function(){
        if(!$('#correo').val())
        {
                //$('#nombre_representante').html('sin resultados');
        }
        else
             getCorreo();
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
    
    
    
        
    /***EVENTO DESPUES DE LA SELECCION DEL ESTADO *****/
    $('#estado').change(function(){
         getMunicipio();
    });
    
/***EVENTO DESPUES DE LA SELECCION DEL MUNICIPIO *****/
    $('#municipio').change(function(){
         getParroquia();
    });
    
/***EVENTO DESPUES DE LA SELECCION DE LA PARROQUIA *****/
    $('#parroquia').change(function(){
         getSector();
    });    

    /*****CARGARA TODOS LOS MUNICIPIOS CORRESPONDIENTES AL ESTADO SELECCIONADO*******/
    var getMunicipio = function(){
		var valor = $("#estado").val();
		if(valor > 0)
		{
			 $.ajax({  
                url: '/configuracion/municipio/buscarMunicipios/',
                type: 'POST',
                dataType : 'json',
                async: false,
                data: 'valor='+valor,
                success:function(datos){
			
					$('#municipio').html('');
					$('#municipio').append('<option value="0" >-Seleccione-</option>');
					$('#parroquia').html('');
					$('#parroquia').append('<option value="0" >-Seleccione-</option>');
					$('#sector').html('');
					$('#sector').append('<option value="0" >-Seleccione-</option>');
					if(datos.length > 0)
					{
						for(i = 0; i < datos.length;i++)
						{
							if(datos[i].estatus_municipio==1)
								$('#municipio').append('<option value="'+datos[i].id_municipio+'" >' +datos[i].descripcion_municipio+ '</option>');
						}
					}
					else
					{
						alert("Estado sin Municipios, seleccione un Estado con Municipios.");
					}
				}	
			},'json');
		};	
    };


	 /*****CARGARA TODAS LAS PARROQUIAS CORRESPONDIENTES AL MUNICIPIO SELECCIONADO*******/
    var getParroquia = function(){
		var valor = $("#municipio").val();
		if(valor > 0)
		{
			
			$.post('/configuracion/parroquia/buscarParroquias/','valor='+valor,function(datos){
					$('#parroquia').html('');
					$('#parroquia').append('<option value="0" >-Seleccione-</option>');
					$('#sector').html('');
					$('#sector').append('<option value="0" >-Seleccione-</option>');
				if(datos.length > 0)
				{
					for(i = 0; i < datos.length;i++)
					{
						if(datos[i].estatus_parroquia==1)
							$('#parroquia').append('<option value="'+datos[i].id_parroquia+'" >' +datos[i].descripcion_parroquia+ '</option>');
					}
				}
				else
				{
					alert("Municipio sin Parroquias, Seleccione un Municipio con Parroquias.");
				}
			},'json');
		};	
    };
    /*****CARGARA TODOS LOS SECTORES CORRESPONDIENTES A LA PARROQUIA SELECCIONADO*******/
    var getSector = function(){
        $.post('/configuracion/sector/buscarSectores/','valor='+$("#parroquia").val(),function(datos){
				$('#sector').html('');
                $('#sector').append('<option value="0" >-Seleccione-</option>');
            if(datos.length > 0)
            {
                for(i = 0; i < datos.length;i++)
                {
                    if(datos[i].estatus_sector==1)
                        $('#sector').append('<option value="'+datos[i].id_sector+'" >' +datos[i].descripcion_sector+ '</option>');
                }
            }
            else
            {
                alert("Parroquia sin Sectores, Seleccione una Parroquia con Sectores.");
            }
        },'json');
    };

    
 });
