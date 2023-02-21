<?php
/*clase vista, objeto que controla las vista
 *
 *
 */

class view
{
        private $_request;// atributo que contiene el objeto peticion
        private $_js;
        private $_css;
        private $_jsPlugin;
        private $_cssPlugin;
        private $_rutas;
        protected $_controlador;
        protected $_modulo;
        protected $_argumentos;

        protected $_template;
        protected $_menu;
        protected $_submenu;
        protected $_notificacion;
        protected $_pathController;


        public function __construct( request $peticion,acl $acl)
        {
                $this->_request = $peticion;
                $this->_js = array();
				$this->_css = array();
                $this->_jsPlugin = array();
                $this->_cssPlugin = array();
                $this->_rutas = array();

                $this->_template = DEFAULT_LAYOUT;

                $modulo = $this->_request->getModulo();
                $this->_modulo = $modulo;
                $this->_argumentos = $this->_request->getArgumento();

                $this->_controlador = $this->_request ->getControlador();

                $this->_menu = new Menu();
                //$this->_notificacion = new Notificacion();

                // si existe la variable modulo, el arreglo view y js se direccionan al modulo indicado
                // si no se direcciona al la base de la aplicacion
                if($modulo)
                {
                        $this->_rutas['view'] = APP_PATH . 'modules' . DS . $modulo . DS . 'views' . DS . $this->_controlador . DS;
                        $this->_rutas['js'] = BASE_URL . 'application/modules/' . $modulo . '/views/' . $this->_controlador . '/js/';
						            $this->_rutas['css'] = BASE_URL . 'application/modules/' . $modulo . '/views/' . $this->_controlador . '/css/';							


                }else
                        {
                                $this->_rutas['view'] = APP_PATH . 'views' . DS . $this->_controlador . DS;
                                $this->_rutas['js'] = BASE_URL . 'application/views/' . $this->_controlador . '/js/';
				                        $this->_rutas['css'] = BASE_URL . 'application/views/' . $this->_controlador . '/css/';
                        }

                $this->_pathController = $this->_rutas['view'];


        }
        public function renderizar($vista,$item = false,$mod = FALSE )
        {


                //$_template = (isset($this->_template))?$this->_template:DEFAULT_LAYOUT;
                $_template = $this->_template;
                // // preparo el menu
                //--------------------------------------------------------------------------------------------
                $menu = $this->_menu;
				$lista_menu = $menu->menu();

                if(!count($lista_menu))
                {
                        $lista_menu = array(
                        array(
                                "id"=>'inicio',
                                "titulo"=>'Inicio',
                                "enlace"=>BASE_URL,
                                "imagen"=>''
                        ),
                        array(
                                "id"=>'resena',
                                "titulo"=>'Resena Historica',
                                "enlace"=>BASE_URL,
                                "imagen"=>''
                        ),
                        array(
                                "id"=>'mision',
                                "titulo"=>'Mision',
                                "enlace"=>BASE_URL,
                                "imagen"=>''

                        ),
                        array(
                                "id"=>'vision',
                                "titulo"=>'Vision',
                                "enlace"=>BASE_URL,
                                "imagen"=>''
                        ));
                }


                //$alerta = $this->_notificacion->getAlerta();
                //$msj = $this->_notificacion->getMensajeDpto();


                ////------------------------------------------------------------------------------------------------------------
                /// arreglo de archivos javascript
                //-----------------------------------------------------------------------------------------------------------
                $js = array();
                if(count($this->_js))
                {
                    $js = $this->_js;
                }

                if(count($this->_jsPlugin))
                {
                    $js = array_merge($js,  $this->_jsPlugin);
                }

                //=-----------------------------------------------------------------------------------------------------------
                //arreglo de archivo css
                //-----------------------------------------------------------------------------------------------------------
                $css = array();
                if(count($this->_css))
                {
                    $css = $this->_css;
                }
                if(count($this->_cssPlugin))
                {
                    $css = array_merge($css, $this->_cssPlugin);
                }
                //-----------------------------------------------------------------------------------------------------
                //analizo argumentos
                //-----------------------------------------------------------------------------------------------------
                if(count($this->_argumentos)>0)
                {
                    $parametro = base64_decode($this->_argumentos[0]);
                    //print_r($parametro); exit();
                    if(strpos($parametro,':')>0)
                    {
                        $mensaje = explode(':', $parametro);
                        //print_r($mensaje); exit();
                    }else
                        $mensaje = array();


                }else
                    $mensaje = array();
                //-----------------------------------------------------------------------------------------------------------
                // arreglo de parametros
                //-----------------------------------------------------------------------------------------------------------
                $_layoutParams = array(
                        "ruta_css"=>BASE_URL.'application/views/_layout/'. $this->_template . '/css/',
                        "ruta_img"=>BASE_URL.'application/views/_layout/'. $this->_template .'/img/',
                        "ruta_js"=>BASE_URL.'application/views/_layout/'. $this->_template . '/js/',
                        "js"=>$js,
                        "css"=>$css,
                        "item"=>$item,
                        "template"=>$this->_template,
                        "mensaje"=>$mensaje,
                        "lista_menu"=>$lista_menu,
                        "mod"=>$mod


                );
                //---------------------------------------------------------------------------------------------------------------

                //die($this->_rutas['view'] .  $vista . '.phtml');
                if(is_readable($this->_rutas['view'] .  $vista . '.phtml'))
                {
                    ob_start();
                    if($_template){
                        // incluyo	las cabeceras de la pagina
                        include_once APP_PATH . 'views'. DS . '_layout'. DS . $_template . DS .'header.php' ;
                    }
                        // incluyo el cuerpo de la pagina
                        include_once $this->_rutas['view'] .  $vista . '.phtml';
                    if($_template){
                        // incluyo el pie de la paogina
                        include_once APP_PATH . 'views'. DS . '_layout'. DS . $_template . DS .'footer.php';
                    }

                    $conten = ob_get_contents();
                    ob_clean();
                    echo $conten;
                }else
                        {
                                throw new errorsys('Vista no encontrada');
                        }


        }


        public function autorenderizar($vista,$item = false,$mod = FALSE )
        {


                //$_template = (isset($this->_template))?$this->_template:DEFAULT_LAYOUT;
                $_template = $this->_template;
                // // preparo el menu
                //--------------------------------------------------------------------------------------------
                $menu = $this->_menu;
		$lista_menu = $menu->menu();

                if(!count($lista_menu))
                {
                        $lista_menu = array(
                        array(
                                "id"=>'inicio',
                                "titulo"=>'Inicio',
                                "enlace"=>BASE_URL,
                                "imagen"=>''
                        ),
                        array(
                                "id"=>'resena',
                                "titulo"=>'Resena Historica',
                                "enlace"=>BASE_URL,
                                "imagen"=>''
                        ),
                        array(
                                "id"=>'mision',
                                "titulo"=>'Mision',
                                "enlace"=>BASE_URL,
                                "imagen"=>''

                        ),
                        array(
                                "id"=>'vision',
                                "titulo"=>'Vision',
                                "enlace"=>BASE_URL,
                                "imagen"=>''
                        ));
                }


                $alerta = $this->_notificacion->getAlerta();
                $msj = $this->_notificacion->getMensajeDpto();
                //------------------------------------------------------------------------------------------------------------
                /// arreglo de archivos javascript
                //-----------------------------------------------------------------------------------------------------------
                $js = array();
                if(count($this->_js))
                {
                        $js = $this->_js;
                }

                if(count($this->_jsPlugin))
                {
                    $js = array_merge($js,  $this->_jsPlugin);
                }

                //=-----------------------------------------------------------------------------------------------------------
                //arreglo de archivo css
                //-----------------------------------------------------------------------------------------------------------
                $css = array();
				if(count($this->_css))
				{
					$css = $this->_css;
				}
                if(count($this->_cssPlugin))
				{
					$css = array_merge($css, $this->_cssPlugin);
				}
                //-----------------------------------------------------------------------------------------------------
                //analizo argumentos
                //-----------------------------------------------------------------------------------------------------
                if(count($this->_argumentos)>0)
                {
                    $parametro = base64_decode($this->_argumentos[0]);
                    //print_r($parametro); exit();
                    if(strpos($parametro,':')>0)
                    {
                        $mensaje = explode(':', $parametro);
                        //print_r($mensaje); exit();
                    }else
                        $mensaje = array();


                }else
                    $mensaje = array();
                //-----------------------------------------------------------------------------------------------------------
                // arreglo de parametros
                //-----------------------------------------------------------------------------------------------------------
                $_layoutParams = array(
                        "ruta_css"=>BASE_URL.'application/views/_layout/'. $this->_template . '/css/',
                        "ruta_img"=>BASE_URL.'application/views/_layout/'. $this->_template .'/img/',
                        "ruta_js"=>BASE_URL.'application/views/_layout/'. $this->_template . '/js/',
                        "js"=>$js,
                        "css"=>$css,
                        "item"=>$item,
                        "template"=>$this->_template,
                        "mensaje"=>$mensaje,
                        "lista_menu"=>$lista_menu,
                        "mod"=>$mod


                );
                //---------------------------------------------------------------------------------------------------------------

                //die($this->_rutas['view'] .  $vista . '.phtml');
                if(is_readable($this->_rutas['view'] .  $vista . '.phtml'))
                {
                    ob_start();
                    if($_template){
                        // incluyo	las cabeceras de la pagina
                        include_once APP_PATH . 'views'. DS . '_layout'. DS . $_template . DS .'header.php' ;
                    }
                        // incluyo el cuerpo de la pagina
                        include_once $this->_rutas['view'] .  $vista . '.phtml';
                    if($_template){
                        // incluyo el pie de la paogina
                        include_once APP_PATH . 'views'. DS . '_layout'. DS . $_template . DS .'footer.php';
                    }

                    $conten = ob_get_contents();
                    ob_clean();
                    echo $conten;
                }else
                        {
                            if(is_readable(APP_PATH . 'views'. DS . '_magicfile'. DS . 'index.phtml'))
                            {
                                ob_start();
                                if($_template){
                                    // incluyo	las cabeceras de la pagina
                                    include_once APP_PATH . 'views'. DS . '_layout'. DS . $_template . DS .'header.php' ;
                                }
                                    // incluyo el cuerpo de la pagina
                                    include_once APP_PATH . 'views'. DS . '_magicfile'. DS . $vista . '.phtml';
                                if($_template){
                                    // incluyo el pie de la paogina
                                    include_once APP_PATH . 'views'. DS . '_layout'. DS . $_template . DS .'footer.php';
                                }

                                $conten = ob_get_contents();
                                ob_clean();
                                echo $conten;
                            }else
                                throw new errorsys('Vista no encontrada');

                        }


        }




        //----------------------------------------------------------------------
        //metodo que carga archivo .js  en la vistas
        //----------------------------------------------------------------------
        public  function setJs(array $js)
        {
                if(is_array($js) && count($js))
                {

                        for($i=0; $i < count($js);$i++)
                        {
                                $this->_js[]= $this->_rutas['js'] .$js[$i].'.js';
                        }

                }else
                        {
                                throw new errorsys('Archivo Js no encontrado');
                        }
        }

		//----------------------------------------------------------------------
        //metodo que carga archivo .css  en la vistas
        //----------------------------------------------------------------------
        public  function setCss(array $css)
        {
                if(is_array($css) && count($css))
                {

                        for($i=0; $i < count($css);$i++)
                        {
                                $this->_css[]= $this->_rutas['css'] .$css[$i].'.css';
                        }

                }else
                        {
                                throw new errorsys('Archivo css no encontrado');
                        }
        }


        //----------------------------------------------------------------------
        // metodo que carga archivos .js desde la carpeta publica
        //----------------------------------------------------------------------
        public function setJsPlugin(array $js)
        {
                if(is_array($js) && count($js))
                {
                        for($i=0; $i < count($js);$i++)
                        {
                                $this->_jsPlugin[]= BASE_URL.'application/public/js/'. $js[$i].'.js';
                        }
                }else
                        {
                                throw new errorsys("Error de js plugin");
                        }

        }
        //----------------------------------------------------------------------
        // metodo que carga archivos .js desde la carpeta publica
        //----------------------------------------------------------------------
        public function setCssPlugin(array $css)
        {
                if(is_array($css) && count($css))
                {
                        for($i=0; $i < count($css);$i++)
                        {
                                $this->_cssPlugin[]= BASE_URL.'application/public/css/'. $css[$i].'.css';
                        }
                }else
                        {
                                throw new errorsys("Error cargando CSS desde carpeta publica");
                        }

        }
        //----------------------------------------------------------------------
        // SETEA EL TEMPLATE POR DEFECTO
        //----------------------------------------------------------------------
        public function setTemplate($template)
        {
                $this->_template = $template;
        }
        //----------------------------------------------------------------------
        // metodo que permite setear la propiedad que controla el print del menu
        //----------------------------------------------------------------------
        public function cnfMenu($valor)
        {
            $this->_getMenu = $valor;
        }
        //----------------------------------------------------------------------
        //metodo que permite cargar un archivo parcial desde las vistas
        //----------------------------------------------------------------------
        public static function partial($archivo,array $_layoutParams)
        {
            $template = (isset($_template))?$_template:DEFAULT_LAYOUT;
            if($archivo)
            {
                $documento = APP_PATH . 'views'. DS . '_layout'. DS . $template . DS . '_partial'. DS . $archivo .'.phtml';
                if(is_readable($documento))// verificamos que la ruta existe
                {
                    include_once $documento;
                }
            }
        }
        //----------------------------------------------------------------------
        //metodo que permite cargar un Js desde las vistas
        //----------------------------------------------------------------------
        public static function tagJs($archivo,$_template = FALSE)
        {
            $template = ($_template)?$_template:DEFAULT_LAYOUT;
            $documento = BASE_URL . 'application/views/_layout/' . $template . '/js/' . $archivo .'.js';
            echo "<script src='".$documento."' type='text/javascript'></script>";
        }

		//----------------------------------------------------------------------
        //metodo que permite cargar un Js desde las vistas en public
        //----------------------------------------------------------------------
        public static function tagJsPublic($archivo)
        {
            $documento = BASE_URL.'application/public/js/' . $archivo .'.js';
            echo "<script src='".$documento."' type='text/javascript'></script>";
        }

        //----------------------------------------------------------------------
        //metodo que permite cargar un Css  desde las vistas
        //----------------------------------------------------------------------
        public static function tagCss($archivo,$_template = FALSE)
        {
            $template = ($_template)?$_template:DEFAULT_LAYOUT;
            //$template = $this->_template;
            $documento = BASE_URL . 'application/views/_layout/'. $template .'/css/'. $archivo .'.css';

            echo "<link href='".$documento."' rel='stylesheet' type='text/css'>";

        }
        
        
        //----------------------------------------------------------------------
        //metodo que permite cargar un archivos de pluging desde las vistas
        //----------------------------------------------------------------------
        public static function tagPluging($archivo,$_template = FALSE)
        {
            $template = ($_template)?$_template:DEFAULT_LAYOUT;
            //$template = $this->_template;
            $documento = BASE_URL . 'application/views/_layout/'. $template .'/'. $archivo .'.css';

            echo "<link href='".$documento."' rel='stylesheet' type='text/css'>";

        }
        
        
         //----------------------------------------------------------------------
        //metodo que permite cargar un archivos de pluging desde las vistas
        //----------------------------------------------------------------------
        public static function tagImg($archivo,Array $parameters,$_template = FALSE)
        {
            $template = ($_template)?$_template:DEFAULT_LAYOUT;
            //$template = $this->_template;
            $documento = BASE_URL . 'application/views/_layout/'. $template .'/css/dist/img/'. $archivo;
			
			$type_img = array("jpg","png","gif","jpeg");
			$arr_ext = explode('.', $archivo);
			$ext = end($arr_ext);
			
			
			if(is_array($parameters))
			{
				$clase_css = (array_key_exists('clase',$parameters))?$parameters['clase']:"";
				$style_css = (array_key_exists('style',$parameters))?$parameters['style']:"";
				
			}	
			if(in_array($ext,$type_img))
			{
					echo "<img src='".$documento."' alt='AdminLTE Logo' class='".$clase_css."' style='".$style_css."'>";
			}	
		   
        }
        
        
		//----------------------------------------------------------------------
        //metodo que permite cargar un Css  desde las vistas en public
        //----------------------------------------------------------------------
		public static function tagCssPublic($archivo)
        {

            $documento = BASE_URL.'application/public/css/'. $archivo .'.css';

            echo "<link href='".$documento."' rel='stylesheet' type='text/css'>";

        }
}


?>
