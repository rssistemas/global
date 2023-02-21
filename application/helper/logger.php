<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 02/06/2019
 * @time 11:29:43 PM
 */


Class Logger{

	static public function debug($name,$var,$method = false){
		$bt = debug_backtrace();
		$caller = array_shift($bt);
		$m = '';

		if($method) $m = "function: ".$method."()";

		print('</br><b>Logger::Debug</b> at File: <b>'.
				end(explode('\\',$caller['file'])).'</b> at line <b>'.
				$caller['line'].'</b> '.$m.'</br></br> <span style="margin-left:15px;">$'.$name.':</span>');
		print_r($var);
		print('</br></br><b>End of debug</b></br></br>');	
	}

	static public function msg($msg,$method = false){
		$bt = debug_backtrace();
		$caller = array_shift($bt);
		$m = '';

		if($method) $m = "function: ".$method."()";

		print('</br><b>Logger::msg</b> at File: <b>'.
			end(explode('\\',$caller['file'])).'</b> at line <b>'.
			$caller['line'].'</b> '.$m.': "'.$msg.'"</br></br>');
	}

	static public function alert($msg,$var,$method = false){
		$bt = debug_backtrace();
		$caller = array_shift($bt);
		$m = '';

		if($method) $m = "function: ".$method."()";

		print('</br><b>Logger::Alert</b> at File: <b>'.
				end(explode('\\',$caller['file'])).'</b> at line <b>'.
				$caller['line'].'</b> '.$m.'</br></br> <span style="margin-left:15px; color:rgb(217,30,24);">'.$msg.': </span>');
		print_r($var);
		print('</br></br><b>End of debug</b></br></br>');	
	}
        
    //-------------------------------------------------------------------------
    //method that registers error in log
    //-------------------------------------------------------------------------
    static function errorLog($mensaje,$type="ERROR")
    {
        
        switch($type)
        {
            case $type=='DB':
                if($log = fopen(LOG_PATH."logDB.txt","a+"))
                {
                    if(!empty($mensaje))
                    {
                       fwrite($log, date("F j, Y, g:i a").'  '.$mensaje. chr(13));
                    }    
                    fclose($log);
                    return TRUE;
                }
            break;    
            case $type=='SYSTEM':            
                if($log = fopen(LOG_PATH."logSYS.txt","a+"))
                {
                    if(!empty($mensaje))
                    {
                       fwrite($log, date("F j, Y, g:i a").'  '.$mensaje. chr(13));
                    }    
                    fclose($log);
                    return TRUE;
                }                
            break;
            case $type=='ACCES':
                if($log = fopen(LOG_PATH."security.txt","a+"))
                {
                    if(!empty($mensaje))
                    {
                       fwrite($log, date("F j, Y, g:i a").'  '.$mensaje. chr(13));
                    }    
                    fclose($log);
                    return TRUE;
                }
            break;
            case $type='ERROR':
                
                if($log = fopen(LOG_PATH."logError.txt","a+"))
                {
                    if(!empty($mensaje))
                    {
                       fwrite($log, date("F j, Y, g:i a").'  '.$mensaje. chr(13));
                    }    
                    fclose($log);
                    return TRUE;
                }                
            break;
        }
           
    }

}
