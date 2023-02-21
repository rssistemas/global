<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Your Name <RSSistemas>
 * @date 09/07/2019
 * @time 11:30:38 PM
 */

class magicfileController extends controller
{
    
    protected $_model;
    private $_objModel;
    
    public function __construct() {
        parent::__construct();
            
    }
    
    public function initialize() {
        $this->getLibrary('paginador');
        $this->_objModel = $this->loadModel($this->_model);       
    }

    public function index($pagina = 1)
    {
        $this->_view->data = $paginador->paginar($this->_objModel->getAll(),$pagina);
        $this->_view->paginacion = $paginador->getView('paginacion',$this->_view->pathController);
        $this->_view->title= ucwords($this->_view->_controlador);
        $this->_view->renderizar('index',$this->_view-_modulo,ucwords($this->_view->_controlador));
        exit();
    }
    
    public  function create()
    {
        
        
    }
    
    public function update()
    {
        
        
        
    }
            
    public function delete()
    {
        
        
    }
            
    
    
}