<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 26/05/2019
 * @time 09:14:35 PM
 */
class validate
{
      static function getTexto($valor)//metodo que filtra un valor tipo texto no recomendado para  inserciones con el metodo prepare de pdo
        {
                if(isset($_POST[$valor]) && !empty($_POST[$valor]))
                {
                        $_POST[$valor] = htmlspecialchars($_POST[$valor],ENT_QUOTES);
                        return $_POST[$valor];
                }else
                        return '';

        }

        static function getInt($valor)//metodo que filtra  un valor entero 
        {
                if(isset($_POST[$valor]) && !empty($_POST[$valor]))
                {
                        $_POST[$valor] = filter_input(INPUT_POST,$valor,FILTER_VALIDATE_INT);
                        return $_POST[$valor];
                }else
                        return 0;
        }

       
        protected function getMensaje($tipo,$cadena)
        {
            $this->_mensaje = base64_encode($tipo.':'.$cadena);            
        }
        
        protected function filtrarInt($int)// filtra los datos enteros y si no lo son intenta  convertirlos 
        {
                $int = (int) $int;
                if(is_int($int))
                {
                        return $int;
                }else
                        return 0;
        }

        static function getPostParam($valor)// retorna los valores de la super global $_POST recomendado para inserciones con el metodo prepare de pdo
        {
                if(isset($_POST[$valor]))
                {
                    if(is_numeric($_POST[$valor]))
                    {
                        if(is_int($_POST[$valor]))
                            (int)$_POST[$valor];
                        else 
                            (float)$_POST[$valor];
                    
                    }    
                    return $_POST[$valor];
                }

        }
        static function getGetParam($valor)// retorna los valores de la super global $_POST recomendado para inserciones con el metodo prepare de pdo
        {
                if(isset($_GET[$valor]))
                {
                        return $_GET[$valor];
                }

        }
        /// limpia las sentencia sql de caracteres de escape
        static function getSql($clave)
        {
                if(isset($_POST[$clave]) &&  !empty($_POST[$clave]))
                {
                        $_POST[$clave]= strip_tags($_POST[$clave]);
                        if(!get_magic_quotes_gpc())
                        {
                                $_POST[$clave]=$this->mres($_POST[$clave]);
                        }
                        return trim($_POST[$clave]);

                }

        }
        // esta funcion convierte el valor enviado en caracretes aceptando solo valor de la a a la z y del 0 al 9 
        static function getAlphaNum($clave)
        {
                if(isset($_POST[$clave]) && !empty($_POST[$clave]))
                {
                        $_POST[$clave]=(string) preg_replace('/[^A-Z0-9_]/i', '', $_POST[$clave]);
                        return trim($_POST[$clave]);
                }

        }
        // funccion que valida las direcciones de correo
        static function validarEmail($email)
        {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                return true;
            }

            return false;
        }
        protected function mres($value)
        {
                $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
                $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

                return str_replace($search, $replace, $value);
        }

        static function generaPass()
        {
                $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                $longitudCadena=strlen($cadena);
                $pass = "";
                $longitudPass=10;
                for($i=1 ; $i<=$longitudPass ; $i++){
                        $pos=rand(0,$longitudCadena-1);
                        $pass .= substr($cadena,$pos,1);
                }
                return $pass;
        }
        
 //--------------------------------------------------------------------------------------
 //method that generates array by collecting variables from the post method
///-------------------------------------------------------------------------------------        
    static function getParam_forModel(model $model)
        {
            $data = array();
            if(count($_POST)>0)
            {
                foreach ($_POST as $key => $val)
                {
                    if(($v = array_search($key,$model->getFields()))>0)
                    {
                        $index = ":".$key;
                        $data[$index]= $val;
                    }
                    
                }
                return $data;
            }
            return FALSE;
                        
        }
        
        
    //method that return the IP Client    
    static function getRealIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }     
    
    
}

                 

?>