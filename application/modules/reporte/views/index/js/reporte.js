function activarRango(rep)
{
        document.getElementById('inicio').disabled=false;
        document.getElementById('final').disabled=false;
        document.getElementById('enviar').disabled=false;
        
        document.getElementById('reporte').value=rep;
        
}
function enviarReporte()
{
    var obj = document.getElementById('form_reporte'); 
    obj.submit();
}