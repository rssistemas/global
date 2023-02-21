<?php
/*----------------------------------------------------------------
 * clase request; recive y procesa una peticion  
 * framework basiquitoPHP
 * ----------------------------------------------------------------
 */

class request
{

        private $_controlador;
        private $_metodo;
        private $_argumentos;
        private $_modules;
        private $_modulo;

        public function __construct()
        {
			
            if(isset($_GET['url']))
            {

                //aplicamos filtro al valor de entrada get
                $url=filter_input(INPUT_GET,'url',FILTER_SANITIZE_URL);
                // creamos un arreglo  que contiene los valores de entrada   
                $url=explode('/',$url); 
                // eliminamos los valores no validos para un arreglo
                $url= array_filter($url);
                //--------------------------------------------------------------------------------------    
                // se busca tener dos tipos de url: 
                //  1 modulo/controlador/metodo/argumentos
                //  2 controlador/metodo/argumentos
                //--------------------------------------------------------------------------------------
                if(defined(APP_MODULE)) 
                {
                        $this->_modules = explode(',',APP_MODULE); 
                }else	
                        $this->_modules = array('seguridad','almacen','compra','ayuda','reporte','entrada','configuracion','archivo','desarrollo','venta','crm','rrhh','administracion',);//contiene los modulos de la aplicacion

                $this->_modulo = strtolower(array_shift($url));

                if(!$this->_modulo)
                {
                        $this->_modulo = FALSE;
                }else
                    {
                        if(count($this->_modules))
                        {
                                if(!in_array($this->_modulo, $this->_modules))
                                {
                                    $this->_controlador = $this->_modulo;
                                    $this->_modulo = FALSE;

                                }else
                                    {
                                        $this->_controlador = strtolower(array_shift($url));
                                        if(!$this->_controlador)
                                        {
                                                $this->_controlador = 'index';
                                        }
                                    }
                        }else
                            {
                                $this->_controlador = $this->_modulo;
                                $this->_modulo = FALSE;
                            }

                    }
                $this->_metodo= strtolower(array_shift($url));
                $this->_argumentos=$url;


            }
                //comprobamos que la variables no esten vacias sino le asignamos un valor por defecto 

                if(!$this->_controlador)
                {
                    $this->_controlador = DEFAULT_CONTROLLER;
                }
                if(!$this->_metodo)
                {
                    $this->_metodo='index';
                }
                if(!isset($this->_argumentos))
                {
                    $this->_argumentos=array();
                }
                
                

        }

        // metodo que retorna el modulo
        public function getModulo()
        {
            return $this->_modulo;
        }

        // metodo que retorna el controlador 
        public function getControlador()
        {
            return $this->_controlador;			
        }
        // metodo que retorna el metodo
        public function getMetodo()
        {
            return $this->_metodo;
        }
        // metodo que retorna los argumentos
        public function getArgumento()
        {
            return $this->_argumentos;
        }


}
