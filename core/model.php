<?php
// Autor: Rafael Perez
// comment: class data model that provides functionality for operations with Mysql databases type ORM
// Update 26/02/2019

class model
{
        private $_registry;// class 	
        protected $_db;
        private $_table;
        private $_table_max_limit;
        private $_nrow;
        private $_model;
        private $_table_info;
        private $_seek;
        private $_index;    
        private $_foreing_model;
        

        public function __construct()
        {
            $this->_registry = registry::getInstancia();
            $this->_db = $this->_registry->_db;

            $this->_model = get_class();
            
            $this->_table = array(); 
            $this->_nrow = array();
            $this->_table_max_limit = 100;	
            $this->_seek =false;
            $this->_index=array("key_primary"=>"","foreign_key"=>"");
            $this->_table_info = $this->get_info_table();
                       
            
        
        }
       //12-02-23 
       //------------------------------------------------------------          
       //Se carga informacion de la tabla relacionada al modelo
       //------------------------------------------------------------ 
       private function get_info_table()
       {
           $res = $this->_db->sqlQuey('SHOW COLUMNS FROM '.$this->_model);
           return $res;
       }    
       //------------------------------------------------------------------
       //12-02-23
       //crea estructura de datos para modelo
       //------------------------------------------------------------------
       private function create_table_model()
       {
           if(is_array($this->_table_info)&& count($this->_table_info))
           {
               foreach ($this->_table_info as $value)
               {
                   $datos = array();
                   $key = $value['Field'];
                   $type = substr($value['Type'],0, strpos($value['Type'], '('));
                   switch ($type)
                   {
                       case'int' :$datos[$key]=0;
                           break;
                       case'varchar':$datos[$key]="";
                           break;
                       case'datetime':$datos[$key]="";
                           break;
                       default:$datos[$key]="";
                   }
                    
               }
               return $datos;
           }
       }
       
       //-------------------------------------------------------------------
       //Metodo que retorna regitros de una tabla esta limitado por la variable
       //$this->_table_max_limit por defecto 100
       //-------------------------------------------------------------------
       public function get_all()
       {
           $res = $this->_db->sqlQuey('select * from '.$this->_model,$this->_table_max_limit);
           if(count($res))
           {
               $datos = array();
               foreach ($res as $key =>$value)
               {
                   $datos[$key] = $value;
               }
               
               $this->_table['datos'] = $datos;
               
               return $this->_table['datos'];
           }else
           {
               return array();
           }
           
       }
       
      //-----------------------------------------------------
      //Metodo de busqueda
      //----------------------------------------------------- 
       public function search($field,$value)
       {           
            $res = $this->_db->sqlQuey('select * from '.$this->_model .' where '. $field.'='.$value );
                if(count($res))
                {
                    $this->_table['datos'] = $res;
                    return true;
                }else
                    return false;
  
       }
       //----------------------------------------------------------
       //busqueda por indice, se requiere que este establecido 
       //el indice $_index[primary_key,foreing_key]. 
       //----------------------------------------------------------
       public function seek($value)
       {
            $index = $this->_index['key_primary'];
            if($value)
            {
                if($res=$this->_db->select_one([$index,$value],$this->_model))
                {
                    $this->_nrow = $res;
                    return true;
                }else
                    {
                        return false; 
                    }
            }

       }

       //---------------------------------------------------------
       //Retorna el resultado de la busqueda con seek
       //---------------------------------------------------------
       public function found()
       {
            if(count($this->_nrow)>0)
                return $this->_nrow;
            else
                return array();

       }       
       //----------------------------------------------------
       //Metodo que crea un nuevo registro o fila
       //----------------------------------------------------
       public function new()
       {
            $this->_nrow = $this->create_table_model();
       }
       
       //-----------------------------------------------------
       //Metodo que graba un registo
       //-----------------------------------------------------
       public function save($data=false)
       {
            if($data)
            {
                if(is_array($data) && count($data)>0)
                {

                    if($this->_db->insert($this->_model,$data))
                     {
                        return true;
                     }else
                        return false;
                }      
            }else
                {
                    if(isset($this->_nrow['id'])&& $this->_nrow['id']>0)
                    {
                        if($this->_db->update($this->_model,$this->_nrow))
                            return true;
                        else
                            return false;
                    }else
                        {
                            if($this->_db->insert($this->_model,$this->_nrow))
                                return true;
                            else
                                return false;

                        }
                    
                }
    
       }
       ////----------------------------------------------------------------
       //
       //------------------------------------------------------------------

       public function get($field)
       {
           $datos = $this->_table['datos'];
           return $datos[$field];
       }
       public function set($field,$valor)
       {
           $this->_nrow[$field][$valor];
       }


       public function set_max_limit($value)
       {
            if(is_int($value))
            {
                $this->_table_max_limit = $value;
            }
       }

       //------------------------------------------------------------------
       //18-12-23
       //establece indeces de modelo actual
       //------------------------------------------------------------------
       // clave primaria
       public function set_primary_key($value)
       {
            $this->_index['key_primary'][$value];
       }
       // clave foranea
       public function set_foreing_key($value)
       {
            $this->_index['key_foreing'][$value];
       } 
       //-----------------------------------------------------------------
}      
        
?>