<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 26/02/2019
 * @time 11:34:50 PM
 */

class organizacionModel extends model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function listar()
    {
        $data = $this->sQuery();
        if(count($data)>0)
            return $data;
        else
            return array();
        
    }
    
    
}