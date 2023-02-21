$(document).ready(function(){
	
	var getDepositos = function(){
		$.post('/globalAdm/almacen/deposito/relacionDeposito/','valor=' + $("#unidad").val(),function(datos){
            if(datos.length)
            {
                for(i=0; i < datos.length;i++)
                {
                     $('#deposito').append('<option value="'+datos[i].id_deposito+'" >' +datos[i].nombre_deposito+'</option>');
                    
                } 
                
                    
            }

            },'json');
		
		
		
	};
	
	
	var getProducto = function(valor){
		
		$.post('/globalAdm/venta/listaprecio/buscarProducto/','codigo=' + valor,function(datos){
            	
            	 $("#dep").val(datos.nombre_deposito);
            	
                 $("#nombre").val(datos.nombre_producto);
                 $("#marca").val(datos.nombre_marca);
                 $("#modelo").val(datos.modelo);
                 $("#costo").val(datos.costo_stock);
                 $("#utilidad").val(datos.utilidad_stock);
                 $("#precio").val(datos.precio_stock);
                 $("#existe").val(datos.cantidad); 
                 
                 $("#stock").val(datos.id_stock);              
                 
           

            },'json');
	};
	
	
	
	
	
	
	$('#unidad').change(function(){
		
		getDepositos();
	});
	
	$(".editar").click(function(){
		
		var myDNI = $(this).data('id');
		$(".modal #id_fila").val( myDNI );
		$("#myModal").modal();
		
		getProducto(myDNI);
	    
	    return false;
         
		
	});
	//---------------------------------------------------------------------------
	//METODO QUE RECALCULA LA UTILIDAD
	//---------------------------------------------------------------------------
	$("#utilidad").blur(function(){
		
		var util = this.value;
		var costo = $("#costo").val();
		var precio= $("#precio").val();
		var npre = 0;
		
		npre = parseFloat(costo) * ((parseFloat(util) / 100)+1);
		if(precio != npre )	
			$("#precio").val(npre.toFixed(2));
			
		
	});
	
	$("#guardar").click(function(){
		
		if(confirm("La utilidad y el precio de venta seràn actualizados desea continuar....."))
		{
			$("#frm-utilidad").submit();			
		}
	});
	
	
	
	$(document).on('keyup keypress', 'form input[type="text"]', function(e) {
            if(e.which == 13) {
                e.preventDefault();
                return false;
            }
            
        });
	

});