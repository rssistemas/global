<?php
//-------------------------------------------------------------------------------------------------------------------------------------------------------
//Autor:Rafael Perez
//Comment:Extending class of exception, the message is personalized and the error log is created
//parameters: 
//--------------------------------------------------------------------------------------------------------------------------------------
class errorsys extends Exception
{
        
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
        
        parent::__construct(); 
        $this->message = $message;
    }

     
    //-------------------------------------------------------------------------
    //method that generates the error message and shows it
    //-------------------------------------------------------------------------
    public  function errorMessage() {
        // Mensaje de error
        $m="";
               
        $errorMsg = 'Error en la línea '
        .$this->getLine().' en el archivo : '
        .$this->getFile() .'<b> '
        .$this->getMessage(). '</b>';
        
       //$this->errorLog($errorMsg);
        
        $m=  $m."<div class='alert alert-warning alert-dismissible' role='alert'>";
        $m = $m ."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
        $m = $m ."<strong> ! </strong> " .$errorMsg." </div>";
        
        return $m;
    }
    
}        
?>