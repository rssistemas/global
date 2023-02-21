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
           
     public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC){

		//Dejar al pelo l a sentencia
		$sth = $this->prepare($sql);

		foreach ($array as $key => $value) {
			$sth->bindValue($key,$value);
		
                        echo $key;
                }
                
                //$sth->
		$res=$sth->execute();
                
                //die($sql);
                //$res = $this->query($sql);
                if(!$res)
                {
                    $error =$this->getError();
                    logger::errorLog($error['2'],'DB');
                    return array();
                }else
                {
                    //$res->setFetchMode(PDO::FETCH_ASSOC);
                    return $sth->fetchAll($fetchMode);
                    //return $res->fetchAll($fetchMode);
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



	/**
		Insert
	*	@param String $table | Tabla enn donde se insertarán los datos
	*	@param String $data | Arreglo asociativo con los datos a insertar
	*	
	*	@return Boolean $sth->exceute | Resultado de la consulta
	*/
	public function insert($table,$data){
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
                    $error =$this->getError();
                    logger::errorLog($error['2'],'DB');
                    return false;
                }else
                    return $res;
                
                
	}

	/**
		Update
	*	@param String $table | Tabla enn donde se insertarán los datos
	*	@param String $data | Arreglo asociativo con los datos a insertar
	*	@param String $where | La parte de la sentencia WHERE
	*	
	*	@return Boolean $sth->exceute | Resultado de la consulta
	*/
	public function update($table, $data, $where){

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
                    logger::errorLog($error['2'],'DB');
                }else
                    return $res;
	}

	/**
		Delete
	*	
	*	@param String $table | Tabla enn donde se insertarán los datos
	*	@param String $where | La parte de la sentencia WHERE
	*	@param int $limit | Limite
	*	
	*	@return Boolean $this->exc | Resultado de la consulta
	*/
	public function delete($table,$where,$limit = 1){
		$res = $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
                if(!$res)
                {
                    $error =$this->getError();
                     logger::errorLog($error['2'],'DB');
                }else
                    return $res;
	}

	public function getInsertedId(){
		return self::lastInsertId();
	}

	public function getError(){
		return self::errorInfo();
	} 
           
        // Method that starts transaction in MYSQL  
        public function start()
        {
            self::beginTransaction(); 
        }
        
        
        // Method that confirms transaction in MYSQL 
        public function confirm()
        {
            self::commit();
        }
       
        // Method that cancels transaction in MYSQL 
        public function cancel()
        {
            self::rollBack();
            $error =$this->getError();
            logger::errorLog($error['2'],'DB');
        }   
        //-------------------------------------------------------------
        //Metodo de query sql 
        //-------------------------------------------------------------
        public function sqlQuery($sql,$limit)
        {
            $sql = $sql." LIMIT ". $limit;
            $res = $this->query($sql);
            if($res)
            {
                $res->setFetchMode(PDO::FETCH_ASSOC);
                return $res->fetchAll();
            }else
                return array();
        }      
           
}

