<?php
class archivoController extends controller
{
    
    public function __construct() {
        parent::__construct();
        $this->getHelper("validate");
        $this->getHelper("logger");
        
    }
    
    public function index() {
        
    }
    
    
}
