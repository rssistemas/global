$(document).ready(function(){

	var verGrafico = function(valor){
		
			$.ajax({  
					url: '/pdval/seguridad/usuario/grafControlAcceso/',
					type: 'POST',
					dataType : 'json',
					async: false,
					data: 'usuario='+valor,
					success:function(datos){
						
						var ctx = document.getElementById("canvas").getContext("2d");
						var lineChart = new Chart(ctx).Line(datos, {scaleFontSize : 13, scaleFontColor : "#ffa45e"});

						legend(document.getElementById("canvas"), datos, lineChart);
						
						
					},error: function(xhr, status) {
							alert('Disculpe, existió un problema');
							}
					}
			  );
									
    };

	$(document).on('click','.grafico',function(){
		var valor = this.value;
		verGrafico(valor);
                
	});

	
				
});	
