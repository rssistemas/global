$(document).ready(function(){

    $(document).on('click','.checkbox',function(){
        var fila  = $(this).data('id');
        var id    = $(this).val();


        $.post('/compra/requisicion/cargarAnalisis/','req='+id,function(datos){
          var inv = "";
          inv = datos[0].inv;
          var det = "";
          det = datos[0].detalle;
          var i = 0;
          var j = 0;
          var cadena = "";
		
		   $('.tabla_detalle').html("");	 	

          cadena = cadena + '<div id="det<?php echo $i ?>" class="tabla_detalle" >';
          cadena = cadena + '<div class="panel-body">';
          cadena = cadena +    '<div class="col-md-6">';
          cadena = cadena +      '<div class="box box-warning">';
          cadena = cadena +          '<div class="box-header with-border">';
          cadena = cadena +            '<div class="box-title"><h4>Detalle Requisicion</h4></div>';
          cadena = cadena +          '</div>';
          cadena = cadena +          '<div class="box-body">';
          cadena = cadena +           '<table class="table table-bordered">';
          cadena = cadena +            '<thead><tr class="bg-light-blue-active">';
          cadena = cadena +              '<th></th>';
          cadena = cadena +              '<th>Tipo</th>';
          cadena = cadena +              '<th>Descripcion</th>';
          cadena = cadena +              '<th>Cant.</th>';
          cadena = cadena +              '<th></th>';
          cadena = cadena +            '</tr></thead>';
          cadena = cadena +            '<tbody>';
          for(i = 0; i < det.length;i++){
          cadena = cadena +                '<tr>';
          if(det[i].condicion =='POR EVALUAR'){
          cadena = cadena +                  '<td><input name="req[]" id="req'+i+'" type="checkbox" class="id_req" value="'+det[i].id_detalle+'" data-id="'+i+'"/></td>';
          }else {
          cadena = cadena +                  '<td><input name="req[]" id="req'+i+'" type="checkbox" class="id_req" value="'+det[i].id_detalle+'" disabled="true" checked="true"  data-id="'+i+'" /></td>';
          }
          cadena = cadena +                  '<td><span class="h6 small">'+det[i].tipo+'</span></td>';
          cadena = cadena +                  '<td><span class="h6 small">'+det[i].descripcion+'</span></td>';
          cadena = cadena +                  '<td><span class="h6 small">'+det[i].cantidad+'</span></td>';
          cadena = cadena +                  '<td><select name="valor[]" id="valor'+i+'" class="from-control accion" disabled="true" data-id="'+i+'"><option value="-">-SELECCIONE-</option><option value="RECHAZADO">RECHAZADO</option><option value="APROBADO">APROBADO</option></select></td>';
          cadena = cadena +                '</tr>'
          }
          cadena = cadena +            '</tbody>';
          cadena = cadena +            '</table>';
          cadena = cadena +          '</div>';
          cadena = cadena +      '</div>';
          cadena = cadena +    '</div>';

          cadena = cadena +    '<div class="col-md-6">';
          cadena = cadena +      '<div class="box box-danger">';
          cadena = cadena +        '<div class="box-header with-border">';
          cadena = cadena +          '<div class="box-title"><h4>Inf. de Inventario</h4></div>';
          cadena = cadena +        '</div>';
          cadena = cadena +        '<div class="box-body">';
          cadena = cadena +          '<table class="table table-bordered">';
          cadena = cadena +          '<thead>';
          cadena = cadena +            '<th>Producto</th>';
          cadena = cadena +            '<th>Almacen</th>';
          cadena = cadena +            '<th>Exist.</th>';

          cadena = cadena +          '</thead>';
          cadena = cadena +          '<tbody>';
          cadena = cadena +              '<tr>';
          for(j = 0; j < inv.length;j++){
          cadena = cadena +                '<td><span class="h6 small">'+inv[j].nombre_producto+'</span></td>';
          cadena = cadena +                '<td><span class="h6 small">'+inv[j].nombre_deposito+'</span></td>';
          cadena = cadena +                '<td><span class="h6 small">'+inv[j].existencia+'</span></td>';

          cadena = cadena +              '</tr>';
          }
          cadena = cadena +          '</tbody>';
          cadena = cadena +          '</table>';
          cadena = cadena +        '</div>';
          cadena = cadena +      '</div>';
          cadena = cadena +    '</div>';


          cadena = cadena +  '</div>';

          cadena = cadena +'</div>';


          $("#visor_req"+fila).append(cadena);





        },'json');

    });


    $(document).on('click','.id_req',function(){

        if( $(this).is(':checked') ) {
          var fila  = $(this).data('id');
          var id    = $(this).val();

          $('#valor'+fila).attr('disabled',false);
        }
    });

    $(document).on('change','.accion',function(){

          var fila  = $(this).data('id');
          var id    = $('#req'+fila).val();
          var val   = $(this).val();
          
        if(val!='-')
		{	
          msj = prompt("Justifique la Accion ");
          if(msj)
          {
            $.ajax( {
                    url: '/compra/requisicion/evaluarRequisito/',
                    type: 'POST',
                    dataType : 'json',
                    async: true,
                    data: 'codigo='+id+'&comentario='+msj+'&valor='+val,
                    success:function(datos){
                      if(datos){
                          $('#req'+fila).attr('disabled',true);
                          $('#valor'+fila).attr('disabled',true);
                        }
                      },
                    error: function(xhr, status) {
                            alert('Disculpe, existe un problema');
                            }
                });

          }
        }  
    });

});
