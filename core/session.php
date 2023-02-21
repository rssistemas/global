<?php

class session
{
        /*
         *metodo que inicia una session
         * method that init session
         */
        public static function init()
        {
                session_start();
        }
        /*-------------------------------------------------------------------------------------
         * metodo de destruye la session, una variable de session o varias variables de session
           method of destroying the session, a session variable or several session variables
         * -------------------------------------------------------------------------------------
        */
         public static function destroy($clave=false)

        {
                if($clave)
                {
                    if(is_array($clave))
                    {
                        for($i=0;$i<count($clave);$i++)
                        {
                            if(isset($_SESSION[$clave[$i]]))
                            {
                                    unset($_SESSION[$clave[$i]]);
                            }
                        }
                    }else
                        {
                            if(isset($_SESSION[$clave]))
                            {
                                unset($_SESSION[$clave]);
                            }
                        }
                }else
                        {
                            session_destroy();
                        }
        }
        //-----------------------------------------------
        // metodo que carga variables en session
        // Method that loads variables in session
        //-----------------------------------------------
        public static function set($clave,$valor)
        {
            if(!empty($clave))
                $_SESSION[$clave]=$valor;
        }

        //-----------------------------------------------------
        // metodo que muestra una variable guardada en sission
        // method that shows a variable saved in sission
        //-----------------------------------------------------
        public static function get($clave)
        {
            if(isset($_SESSION[$clave]))
                return $_SESSION[$clave];
            else
		return false;
        }
        //-----------------------------------------------------
        //metodo que verifica si una variable existe en session
        //method that verifies if a variable exists in session
        //-----------------------------------------------------
        public static function has($clave)
        {
            if(isset($_SESSION[$clave]))
                    return true;
            else
                return FALSE;
        }
        //-----------------------------------------
        //controla el acceso a nivel de usuario
        //controls access at the user level
        //------------------------------------------
        public static function acceso($level = FALSE)
        {
            if(!session::get('autenticado'))
            {
		session::destroy();
                header("location:".BASE_URL.'index/login/');
                exit();
            }

            session::tiempo();

			//if(session::getLevel($level) > session::getLevel(session::get('level')))
			//{
			//	header("location:".BASE_URL.'error/access/5050');
			//	exit();
			//}
        }

        //-----------------------------------------------------------
        // metodo que maneja los accesos a nivel de vista
        // method that handles accesses at the level of view
        //-----------------------------------------------------------
        public static function accesoView($level)
        {
                if(!session::get('autenticado'))
                {
                        return false;
                }
                session::tiempo();

                if(session::getLevel($level) > session::getLevel(session::get('level')))
                {
                        return FALSE;
                }

                return TRUE;
        }

        //------------------------------------------------------------
        //metodo que retorna el nivel de acceso
        //method that returns the level of access
        //------------------------------------------------------------
        public static function getLevel($level)
        {
                $role = array();
                $role['admin']=1;
                $role['especial']=2;
                $role['usuario']=3;

                if(!array_key_exists($level, $role))
                {
                        throw new error("Error de Acceso... Nivel de acceso no definido");
                }else
                        return $role[$level];



        }

        //----------------------------------------------------------------
        // metodo que maneja el acceso a nivel de grupo de usuario+
        // method that handles access at the user group level
        //----------------------------------------------------------------
        public static function accesoEstricto(array $level, $noAdmin=FALSE)
        {
                if(!session::get('autenticado'))
                {
                        header("location:".BASE_URL.'error/access/5050');
                        exit();
                }
                session::tiempo();
                if($noAdmin==false)
                {
                        if(session::get('level')=='admin')
                                return;
                }

                if(count($level))
                {
                        if(in_array(session::get('level'), $level))
                        {
                                return;
                        }
                }

                header("location:".BASE_URL.'error/access/5050');
                exit();

        }

        public static function accesoViewEstricto(array $level, $noAdmin=FALSE)
        {
                if(!session::get('autenticado'))
                {
                    return FALSE;
                }

                session::tiempo();

                if($noAdmin==false)
                {
                    if(session::get('level')=='admin')
                       return TRUE;
                }

                if(count($level))
                {
                    if(in_array(session::get('level'), $level))
                    {
                        return TRUE;
                    }
                }

                return FALSE;

        }
        //-------------------------------------------------------------------------------------
        //metodo que verifica variable que controla el tiempo de sesion
        //method that verifies variable that controls the session time
        //-------------------------------------------------------------------------------------
        public static function tiempo()
        {
                if(!session::get('tiempo') || !defined('SESSION_TIME'))
                {
                        throw new error("No se ha definido el tiempo de la session ....");
                }
                if(SESSION_TIME == 0)
                        return;

                if( (time()- session::get('tiempo')) > (SESSION_TIME * 60))
                {
                        session::destroy();
                        header("location:".BASE_URL.'index/logout/');
                        exit();
                }else
                        {
                                session::set('tiempo', time());
                                session::set('autenticado', true);
                        }
        }


        public static function validate()
        {
            if(!session::get('tiempo') || !session::get('autenticado'))
            {
              return false;
            }
            return true;

        }

}

?>
