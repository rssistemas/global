$(document).ready(function(){

	// window.onload = function(){
		// var data;
		// var options = {responsive:true};
		// var valor = 0;
		
		// $.ajax( {  
				// url: '/pdval/transaccion/logistica/grafCondSol/',
				// type: 'POST',
				// dataType : 'json',
				// async: false,
				// data: 'condicion=0',
				// success:function(datos){
					// data = datos;
				// if(data.length>0)
				// {    
					// var ctx = document.getElementById("doughnutChart").getContext("2d");
					// var doughnutChart = new Chart(ctx).Doughnut(data);

					// legend(document.getElementById("doughnutLegend"), data, doughnutChart);
				// }    
			// },error: function(xhr, status) {
					// alert('Disculpe, existió un problema');
					// }
			// }
		  // );

	// };

	var speedCanvas = document.getElementById("speedChart");

	Chart.defaults.global.defaultFontFamily = "Lato";
	Chart.defaults.global.defaultFontSize = 18;

	var speedData = {
	  labels: ["0s", "10s", "20s", "30s", "40s", "50s", "60s"],
	  datasets: [{
		label: "Car Speed (mph)",
		data: [0, 59, 75, 20, 20, 55, 40],
	  }]
	};

	var chartOptions = {
	  legend: {
		display: true,
		position: 'top',
		labels: {
		  boxWidth: 80,
		  fontColor: 'black'
		}
	  }
	};

	var lineChart = new Chart(speedCanvas, {
	  type: 'line',
	  data: speedData,
	  options: chartOptions
	});
	
	
});	

        