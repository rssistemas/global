<?php
class uploadFile
{
        private $_file;
        private $_size;
        private $_maxsize;
        private $_dirUpload;
        private $_tipeFile = array();
        private $_type;
        private $_name;
        private $_tmp;
        private $_reName;
        private $_mensaje;

        public function __construct()
        {
                $this->_maxsize=1000000;
                $this->_typeFile=array("image/jpg", "image/jpeg", "image/gif", "image/png"); 
        }

        public function setFile($valor)
        {
                $this->_file = $valor;
        }
        public function setDirUpload($valor)
        {
                $this->_dirUpload = $valor;
        }
        public function setRename($valor)
        {
                $this->_reName = $valor;
        }
        //----------------------------------------------------------------------------------
        //
        //---------------------------------------------------------------------------------
        public function uploadFile()
        {

                if(is_array($this->_file))
                {	
                    $this->_size = $this->_file['size'][0];
                    $this->_type = $this->_file['type'][0];
                    $this->_name = $this->_file['name'][0];
                    $this->_tmp  = $this->_file['tmp_name'][0];


                    if($this->_size <= $this->_maxsize && $this->_size > 0)
                    {

                            if(in_array($this->_type,$this->_typeFile))
                            {

                                    if(file_exists($this->_dirUpload))
                                    {                                            
                                            // Sacamos la dimensiones del archivo
                                            list($ancho, $alto) = getimagesize($this->_tmp);
                                            $nuevo_ancho = 300;
                                            $nuevo_alto = 300;
                                            //die($this->_type);
                                            if ($this->_type == "image/jpeg") $this->_type = "image/jpg";

                                            // Dependiendo de la extensión llamamos a distintas funciones
                                            switch ($this->_type) {
                                                    case "image/jpg":
                                                            $img = imagecreatefromjpeg($this->_tmp);
                                                    break;
                                                    case "image/png":
                                                            $img = imagecreatefrompng($this->_tmp);
                                                    break;
                                                    case "image/gif":
                                                            $img = imagecreatefromgif($this->_tmp);
                                                    break;
                                            }
                                            
                                            
                                            
                                            // Creamos la miniatura
                                            $thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
                                            // La redimensionamos
                                            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
                                            imagedestroy($img);
                                            $destino = $this->_dirUpload.$this->_reName;
                                            
                                            switch ($this->_type) {
                                                    case "image/jpg":
                                                            imagejpeg($thumb,$destino.'.jpg');
                                                    break;
                                                    case "image/png":
                                                            imagepng($thumb,$destino.'.jpg');
                                                    break;
                                                    case "image/gif":
                                                            imagegif($thumb,$destino.'.gif');
                                                    break;
                                            }
                                            
                                            imagedestroy($thumb);
                                            
                                            $this->_mensaje ="Archivo Subido";
                                            return true;
                                    }else
                                            {
                                                    $this->_mensaje ="El Destino no Existe";	
                                                    return false;
                                            }
                            }else
                                    {
                                            $this->_mensaje ="Tipo de archivo no Permitido";
                                            return false;
                                    }
                        }else
                                {
                                        $this->_mensaje ="Tamaño Exedido";
                                        return false;
                                }	

                }	
        }
        //------------------------------------------------------------------------------------------
        //METODO QUE PERMITE SUBIR IMAGEN PARA GUARDAR EN BASE DE DATOS
        //------------------------------------------------------------------------------------------
        public function uploadImgDb()
        {
            
            if(is_array($this->_file))
            {
                $this->_size = $this->_file['size'][0];
                $this->_type = $this->_file['type'][0];
                $this->_name = $this->_file['name'][0];
                $this->_tmp  = $this->_file['tmp_name'][0];
            
                
                if($this->_size <= $this->_maxsize && $this->_size > 0)
                {
                    if(in_array($this->_type,$this->_typeFile))
                    { 
                        $fp = fopen($this->_tmp, 'r+b');
                        $data = fread($fp, filesize($this->_tmp));
                        fclose($fp);
                    
                        $data = mysql_real_escape_string($data);
                        
                        $val = array("data"=>$data,"tipo"=> $this->_type,"nombre"=>$this->_name );
                        
                        $this->_mensaje ="Archivo Subido";
                        return $val;
                    } else {
                        $this->_mensaje ="Tipo de archivo no Permitido";
                        return false;
                    }
                }else
                {
                    $this->_mensaje ="Tamaño Exedido";
                    return false;
                }
            }
            
        }        

}


?>