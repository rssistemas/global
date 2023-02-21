<?php
class email{
 
    private $_email;
    private $_error;
    private $_img;
	
    public function __construct() {
        	
			
        require 'class.phpmailer.php';
        //Crear una instancia de PHPMailer
        $this->_email = new PHPMailer();
        //Definir que vamos a usar SMTP
        $this->_email->IsSMTP();    
        //Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
        // 0 = off (producción)
        // 1 = client messages
        // 2 = client and server messages
        $this->_email->SMTPDebug  = 0;
        //Ahora definimos gmail como servidor que aloja nuestro SMTP
        $this->_email->Host       = 'smtp.gmail.com';
        //El puerto será el 587 ya que usamos encriptación TLS
        $this->_email->Port       = 465;
        //Definmos la seguridad como TLS
        $this->_email->SMTPSecure = 'ssl';
        
        $this->_email->IsHTML(true);
        //Tenemos que usar gmail autenticados, así que esto a TRUE
        $this->_email->SMTPAuth   = true;
        //Definimos la cuenta que vamos a usar. Dirección completa de la misma
        $this->_email->Username   = "sigap.pdval@gmail.com";
        //Introducimos nuestra contraseña de gmail
        $this->_email->Password   = "sigap2016";
        
        $this->_error = "";
    }
    
    public function setUser($valor)
    {
        //definimos el usuario o correo del usuario
        $this->_email->Username   = $valor;
    }
    public function setPassword($clave)
    {
        //definimos contrasena
        $this->_email->Password   = $clave;
    }
    public function setRemitente($correo,$nombre)
    {
        //Definimos el remitente (dirección y, opcionalmente, nombre)
        $this->_email->SetFrom($correo, $nombre);
    }
    public function setCopia($correo,$nombre)
    {
        //Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
        $this->_email->AddReplyTo($correo,$nombre);
    }
    
    public function setDestinatario($correo,$nombre)
    {
        $this->_email->AddAddress($correo, $nombre);
    }
    public function setMensaje($mensaje)
    {
        //Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
        $this->_email->MsgHTML($mensaje);
    }
    public function setMensajeAlt($mensaje)
    {
        //Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
        $this->_email->AltBody = $mensaje;
    }
    public function setImagen($img)
    {
        $this->_email->AddEmbeddedImage($img, 'imagen',$img,'base64','image/jpeg');
    }
    public function setAsunto($asunto)
    {
        $this->_email->Subject=$asunto;
    }        
    public function enviar()
    {
        /*Lo primero es añadir al script la clase phpmailer desde la ubicación en que esté*/
        //Enviamos el correo
        if(!$this->_email->Send()) {
            $this->_error = "Error: " . $this->_email->ErrorInfo;
            return FALSE;
        } else {
          return TRUE;
        }        
    }
    public function getError()
    {
        return $this->_error;
        
    }
            
    public function buscarContenido($nombre,$directorio)
    {
        $contenido = file_get_contents('correomaquetado.html');
        return $contenido;
    }
    public function mensaje_html($mensaje,$origen,$enlace,$firma)
    {
	$cuerpo="<html><head><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'></head>";
	$cuerpo.="<body><div style='position:relative; margin:0; padding:0; width:600px; height:440px; border:1px;'>";
 	$cuerpo.="<div style='position:relative; margin:0; padding:0; width:600px; height:100px; top:10px;'>";
 	$cuerpo.="<img src='cid:imagen' width='150' height='90' alt='img_mail' /></div>";
        $cuerpo.="<div style='position:relative; margin:0; padding:0; width:580px; height:250px; top:20px; left: 10px;border-bottom:solid 1px #CCC; border-top:solid #CCC;' ><br />";
	$cuerpo.="<span>".strip_tags($firma)."    Informa: "."</span><br /><br />";
        $cuerpo.="<span>".wordwrap($mensaje, 100, "<br />\n")."</span><br /><br />";
        $cuerpo.="<span>Atte:".strip_tags($origen)."</span><br /><br /></div>";
	$cuerpo.="<span>visitanos en :".strip_tags($enlace)."</span></div>";
        $cuerpo.="<div style='position:relative; margin:0; padding:0; width:600px; height:50px; top:30px;'></div></div>";
	$cuerpo.="</body></html>";
	
	return $cuerpo;
    }
    
}
?>