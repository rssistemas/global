<?php
abstract class widget
{
    protected function loadModel($model)
    {
        $rutaModel = APP_PATH . DS . 'widgets' . DS . 'model' . DS . $model . '.php';
        if(is_readable($rutaModel))
        {
            include_once $rutaModel;
            $modelClass = $model.'Widget';
            if(class_exists($modelClass))
            {
                return new $modelClass;   
            }
                
        }
        throw new Exception('Error en modelo de Widget ......');
    }        
    
    protected function content($view,$data = array(),$ext = 'phtml')
    {
        $rutaView = APP_PATH . DS . 'widgets' . DS . 'views' . DS . $view .'.'.$ext;
        if(is_readable($rutaView))
        {
            ob_start();
            extract($data);
            include $rutaView;
            $conten = ob_get_contents();
            ob_clean();
            return $conten;
        }
        throw new Exception('Error en vista de Widget ......');
    }        
}

