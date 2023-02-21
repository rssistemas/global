$(document).ready(function(){
    //---------------------------------------------------------------------------------------------------------
    //funciones para marca
    //---------------------------------------------------------------------------------------------------------
	$("#marca").autocomplete({
            source: '/almacen/producto/buscarMarca/', /* este es el script que realiza la busqueda */
            minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
            select:marcaSeleccionado,
            focus: marcaFoco
        }); 
		
       function marcaFoco(event, ui)
        {   
            $("#marca").val(ui.item.nombre);

            return false;
        }
        // ocurre cuando se selecciona un producto de la lista
        function marcaSeleccionado(event, ui)
        {
            //recupera la informacion del producto seleccionado
            var marca = ui.item.value;        
            var id = marca.id;

            //actualizamos los datos en el formulario

            $("#id_marca").val(id);
            // no quiero que jquery despliegue el texto del control porque 
            // no puede manejar objetos, asi que escribimos los datos 
            // nosotros y cancelamos el evento
            // (intenta comentando este codigo para ver a que me refiero)
            $("#marca").val(marca.nombre);
            return false;
        }
	
	//------------------------------------------------------------------------------	
	//funciones para rubro
	//-------------------------------------------------------------------------------
	$("#grupo").autocomplete({
            source: '/almacen/producto/buscarGrupo/', /* este es el script que realiza la busqueda */
            minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
            select:rubroSeleccionado,
            focus: rubroFoco
        }); 
		
       function rubroFoco(event, ui)
        {   
            $("#grupo").val(ui.item.nombre);

            return false;
        }
        // ocurre cuando se selecciona un producto de la lista
        function rubroSeleccionado(event, ui)
        {
            //recupera la informacion del producto seleccionado
            var rubro = ui.item.value;        
            var id = rubro.id;

            //actualizamos los datos en el formulario

            $("#id_grupo").val(id);
            // no quiero que jquery despliegue el texto del control porque 
            // no puede manejar objetos, asi que escribimos los datos 
            // nosotros y cancelamos el evento
            // (intenta comentando este codigo para ver a que me refiero)
            $("#grupo").val(rubro.nombre);
            return false;
        }
		
        		
	//-----------------------------------------------------------------------------------------	
	//funciones para clasificacion
	//-----------------------------------------------------------------------------------------
	$("#clasificacion").autocomplete({
            source: '/almacen/producto/buscarClasificacion/', /* este es el script que realiza la busqueda */
            minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
            select:clasificacionSeleccionado,
            focus: clasificacionFoco
        }); 
		
       function clasificacionFoco(event, ui)
        {   
            $("#clasificacion").val(ui.item.nombre);

            return false;
        }
        // ocurre cuando se selecciona un producto de la lista
        function clasificacionSeleccionado(event, ui)
        {
            //recupera la informacion del producto seleccionado
            var clasificacion = ui.item.value;        
            var id = clasificacion.id;

            //actualizamos los datos en el formulario

            $("#id_clasificacion").val(id);
            // no quiero que jquery despliegue el texto del control porque 
            // no puede manejar objetos, asi que escribimos los datos 
            // nosotros y cancelamos el evento
            // (intenta comentando este codigo para ver a que me refiero)
            $("#clasificacion").val(clasificacion.nombre);
            return false;
        }
        
	
	//-------------------------------------------------------------------------------	
	//funciones para PRESENTACION
	//-------------------------------------------------------------------------------
	$("#presentacion").autocomplete({
            source: '/almacen/producto/buscarPresentacion/', /* este es el script que realiza la busqueda */
            minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
            select:presentacionSeleccionado,
            focus: presentacionFoco
        }); 
		
       function presentacionFoco(event, ui)
        {   
            $("#presentacion").val(ui.item.nombre);

            return false;
        }
        // ocurre cuando se selecciona un producto de la lista
        function presentacionSeleccionado(event, ui)
        {
            //recupera la informacion del producto seleccionado
            var presentacion = ui.item.value;        
            var id = presentacion.id;

            //actualizamos los datos en el formulario

            $("#id_presentacion").val(id);
            // no quiero que jquery despliegue el texto del control porque 
            // no puede manejar objetos, asi que escribimos los datos 
            // nosotros y cancelamos el evento
            // (intenta comentando este codigo para ver a que me refiero)
            $("#presentacion").val(presentacion.nombre);
            return false;
        }
		
	//--------------------------------------------------------------------------
	//funcion validar codigo
	//---------------------------------------------------------------------------
        $("#codigo").blur(function(){
                if($("#codigo").val()!="")
                {
                        $.ajax( {  
                                url: '/almacen/producto/buscarCodigo/',
                                type: 'POST',
                                dataType : 'json',
                                async: false,
                                data: 'codigo='+$("#codigo").val(),
                                success:function(datos){
                                    if(datos.length > 0)
                                    {
                                            alert("El Código ya se encuentra Registrado.");
                                            $('#codigo').val("");
                                            $('#codigo').focus();

                                    }   

                                                },
                                error: function(xhr, status) {
                                                alert('Disculpe, existe un problema');
                                                }
                        });
                }
        });
        
        //-------------------------------------------------------------------------------	
	//funciones para unidad de medida
	//-------------------------------------------------------------------------------
	$("#medida").autocomplete({
            source: '/almacen/producto/buscarMedida/', /* este es el script que realiza la busqueda */
            minLength: 2, /* le decimos que espere hasta que haya 2 caracteres escritos */
            select:medidaSeleccionado,
            focus: medidaFoco
        }); 
		
       function medidaFoco(event, ui)
        {   
            $("#medida").val(ui.item.label);

            return false;
        }
        // ocurre cuando se selecciona un producto de la lista
        function medidaSeleccionado(event, ui)
        {
            //recupera la informacion del producto seleccionado
            var medida = ui.item.value;        
            var id = medida.id;

            //actualizamos los datos en el formulario

            $("#id_uni_med").val(id);
            // no quiero que jquery despliegue el texto del control porque 
            // no puede manejar objetos, asi que escribimos los datos 
            // nosotros y cancelamos el evento
            // (intenta comentando este codigo para ver a que me refiero)
            $("#medida").val(medida.nombre);
            return false;
        }
		
		
        $(":file").filestyle('buttonText', 'Buscar');
        $(":file").filestyle('icon', false);
	document.getElementById('files').addEventListener('change', previsualizar, false);
});