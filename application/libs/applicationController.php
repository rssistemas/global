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
 * @time 08:41:07 PM
 */


class applicationController extends controller
{
    
    
    
    public function __construct() {
        parent::__construct();
        $this->_view->setTemplate('sbadmin');
    }
        
    public function index();
    
    
    
}