<?php
//clase base controller  
abstract class controller
{
        private   $_registry;
        protected $_view;
        protected $_request;
        protected $_acl;
        protected $_mensaje;    
        
        
                
        
        public function __construct()
        {           
                $this->_registry = registry::getInstancia();
                $this->_acl =  $this->_registry ->_acl;
                $this->_request = $this->_registry->_request;

                $this->_view = new view($this->_request,$this->_acl);
                $this->_mensaje = '';
        }

        abstract public function index();
        
        
        //-----------------------------------------------------------------------------------------------
        //26-05-2019,Rafael Perez: metodo que se ejecuta antes del metodo invocado o por defecto index
        //----------------------------------------------------------------------------------------------
        protected function initialize()
        {
        
            
        }
        
        //----------------------------------------------------------------------------------------
        //26-05-2019;Rafael Perez:metodo que se ejecuta despues del metodo invocado o por defecto
        //----------------------------------------------------------------------------------------
        protected function finalize()
        {
        
                        
        }
        
       

        //------------------------------------------
        //metodo que carga un modelo de datos a nivel de controlador 
        //-----------------------------------------
        protected function loadModel($modelo,$modulo=false)
        {
                $_nmodelo = $modelo;
            
                $modelo = $modelo.'Model';
                $ruta_modelo = APP_PATH . 'models' . DS . $modelo . '.php';// creamos la ruta del modelo

                if(!$modulo)
                {
                        $modulo = $this->_request->getModulo();
                }

                if($modulo)
                {
                        if($modulo != 'defaults')
                        {
                                $ruta_modelo = APP_PATH .'modules'. DS . $modulo . DS .'models' . DS . $modelo . '.php';
                        }
                }

                //die($ruta_modelo);
                if(is_readable($ruta_modelo))// verificamos que la ruta existe 
                {

                        require_once $ruta_modelo;// requerimos el archivo
                        //----------------------------------------------------------------------------------------------------------
                        //$modelo = new $modelo;// instanciamos la clase contenida en el archivo
                        //Autor: Rafel Perez
                        // 30/01/2019 
                        //----------------------------------------------------------------------------------------------------
                        //change that allows the name of the model to pass to the class
                          $modelo = new $modelo($_nmodelo);
                          
                        return $modelo;// retornamos el objeto intanciado

                }else 
                        throw new errorsys('Modelo no encontrado');
        }

        //---------------------------------------------------------
        // metodo que carga una libreria en el entorno de ejecucion
        //---------------------------------------------------------
        protected function getLibrary($libreria)
        {
                $ruta_libreria = APP_PATH . 'libs' . DS . $libreria . '.php';

                if(is_readable($ruta_libreria))
                {
                        require_once $ruta_libreria;

                }else
                        throw new errorsys('Libreria no encontrada');

        }
        
        //---------------------------------------------------------
        // metodo que carga una libreria en el entorno de ejecucion
        //---------------------------------------------------------
        protected function getHelper($libreria)
        {
                $ruta_libreria = APP_PATH . 'helper' . DS . $libreria . '.php';

                if(is_readable($ruta_libreria))
                {
                        require_once $ruta_libreria;

                }else
                        throw new errorsys('Libreria no encontrada');

        }
        protected function redireccionar($ruta=false)// metodo que redirecciona la vista
        {
                if($ruta)
                {
                    if(!empty($this->_mensaje))
                        header("location:".BASE_URL.$ruta.'/'.$this->_mensaje);
                    else
                        header("location:".BASE_URL.$ruta);
                    
                    exit;
                }else
                        {
                            if(!empty($this->_mensaje))
                                header("location:".BASE_URL.$ruta.'/'.$this->_mensaje);
                            else
                                header("location:".BASE_URL.$ruta);
                            exit;
                        }
        }

} 


?>
