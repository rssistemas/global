<?php
// clase padre para conexion a base de datos Mysql extendiendo de pdo 
class database extends PDO
{
              
    
    public function __construct() {

                  parent::__construct(
                    'mysql:host=' . DB_HOST .
                  ';dbname=' . DB_NAME,
                  DB_USER, 
                  DB_PASS, 
                  array(
                      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHAR
                      ));

     
                  
    }

    
    //-----------------------------------------------------------
    //Metodo que da soporte para reistro de log sobre operaciones 
    //de base de datos.
    //-----------------------------------------------------------
    private function logReport($mensaje = false)
    {
        if(!$mensaje)
        {
            $error = $this->getError();
            $mensaje = $error[2];
                
        }

        if($log = fopen(LOG_PATH."logDB.txt","a+"))
        {
            if(!empty($mensaje))
            {
                fwrite($log, date("F j, Y, g:i a").'  '.$mensaje. chr(13));
            }    
            fclose($log);
            return TRUE;
        }
    }
    //--------------------------------------------------------------
    //
    //--------------------------------------------------------------
    public function select($field = false, $condition = array(),$table=false)
    {
        $fetchMode = PDO::FETCH_ASSOC;

        if(!$field)
            $field = " * ";

        if($condition)
        {
            $where="";
            $order="";
            $group="";
            if(is_array($condition))
            {
                 $where =    $condition['where'];
                 $order =    $condition['order'];
                 $group =    $condition['group'];
            }
        }
                

        $sql = "select (". $field .") from ". $table;

        if(!empty($where))
            $sql = $sql . " where " . $where; 

        if(!empty($order))
            $sql = $sql . " order by " . $order;

        if(!empty($group))
            $sql = $sql . " group by " . $group;

            $res = $this->query($sql);
            if($res)
            {
                $res->setFetchMode(PDO::FETCH_ASSOC);
                $datos = $res->fetchAll();
                
            }else
                {
                    $error = $this->getError();
                    $this->logReport($error[2]);
                    return array();
                }            
                    
	}



    public function select_one($parameters,$table)
    {
        if(is_array($parameters))
        {
            $indice = $parameters[0];
            $value = $parameters[1];
        }else
            $value = $parameters;

        if(!empty($indice))
            $sql = "select * from ". $table . " where ".$indice ." = ". $value;
        else
            $sql = "select * from ". $table . " where  id = ". $value;
        $res = $this->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetch();
        }else
            return array();         


    }



	public function insert($table,$data)
    {
		ksort($data);

		$fieldNames = implode('`,`',array_keys($data));
		$fieldValues = ':'.implode(', :', array_keys($data));
                
		$sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key",$value);
		}

		$res = $sth->execute();
                if(!$res)
                {
                    $error = $this->getError();
                    $this->logReport($error);
                    return false;
                }else
                    return $res;
                        
	}




	public function update($table, $data, $where)
    {

		ksort($data);

		$fieldDetails = null;
		foreach ($data as $key => $value) {
			$fieldDetails .= "$key=:$key,";
		}

		$fieldDetails = rtrim($fieldDetails,',');
		

		$sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key",$value);
		}
		
		$res = $sth->execute();
                if(!$res)
                {
                    $error =$this->getError();
                    $this->logReport($error);
                }else
                    return $res;
	}



    public function delete($table,$where,$limit = 1)
    {
		$res = $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
        if(!$res)
        {
            $error =$this->getError();
            $this->logReport($error);
        }else
            return $res;
	}

	public function getInsertedId(){
		return self::lastInsertId();
	}

	public function getError(){
		return self::errorInfo();
	} 
    //----------------------------------------------------   
    // Method that starts transaction in MYSQL  
    //----------------------------------------------------
    public function start()
    {
        self::beginTransaction(); 
    }
    
    //----------------------------------------------------
    // Method that confirms transaction in MYSQL 
    //----------------------------------------------------
    public function confirm()
    {
        self::commit();
    }
    //----------------------------------------------------
    // Method that cancels transaction in MYSQL 
    //----------------------------------------------------
    public function cancel()
    {
        self::rollBack();
        $error =$this->getError();
        $this->logReport($error);
    }   
    //-------------------------------------------------------------
    //Metodo de query sql 
    //-------------------------------------------------------------
    public function sqlQuery($sql,$limit=false)
    {
        if($limit)
            $sql = $sql." LIMIT ". $limit;
        
       // die($sql);
        $res = $this->query($sql);
        if($res)
        {
            $res->setFetchMode(PDO::FETCH_ASSOC);
            return $res->fetchAll();
        }else
            return array();
    }
        
    
    
           
}

