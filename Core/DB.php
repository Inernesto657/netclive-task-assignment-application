<?php
namespace Core;
use PDO;
use PDOException;

class DB {

    private $DBHOST        = "localhost";
    private $DBNAME        = "task_application";
    private $DBCHARSET     = "UTF8";
    private $DBUSER        = "root";
    private $DBPASSWORD    = "";
    private $DBCONNECT;
    public  $SQL           = "";
    public  $SQLPARAMETERS = [];

    /**
     * This magic custom method allows
     * decendants of this class to call
     * inaccessible methods of this class
     * @param $method (method name)
     * @param $args (arguments passed to the method, if any)
     * @return function (i.e the inaccessible method of this class)
     */
    public function __call($method, $args){
        
        return call_user_func_array([$this, $method], $args);
    }

    /**
     * This magic custom method allows
     * decendants of this class to read
     * inaccessible properties of this class
     * @param $property (property name)
     * @return mixed (the called property)
     */
    public function __get($property){

        return $this->$property;
    }

    /**
     * This magic custom method allows
     * decendants of this class to write data to
     * inaccessible properties of this class
     * @param $property (property name)
     * @param $value (data to write to the property)
     */
    public function __set($property, $value){

        $this->$property = $value;
    }

    /**
     * This method makes connection to the database
     * using PDO 
     * @return void
     */
    private function connection() { 

        $dsn = "mysql:host={$this->DBHOST};dbname={$this->DBNAME};charset={$this->DBCHARSET}";

        try {

            $pdo = new PDO($dsn, $this->DBUSER, $this->DBPASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            if($pdo) {

                $this->DBCONNECT = $pdo;
            }
        } catch (PDOException $e){

            echo $e->getMessage();
        }
    }

    /**
     * This method executes the sql statement
     * using PDO
     * @param string $sql 
     * @param array $parameters (if any) 
     * @return void
     */
    private function execute(string $sql = "", array $parameters = []) {

        $statement = $this->DBCONNECT->prepare($sql);

        $statement->execute($parameters);

        return $statement;
    }

    /**
     * This method strengthens the sql statement
     * using PDO prepared statements
     * @return object $pdo
     */
    private function prepare() { 

        $pdo = $this->DBCONNECT->prepare($this->SQL);

        $pdo->execute(array_values($this->SQLPARAMETERS));

        $this->SQLPARAMETERS = [];

        $this->SQL = "";

        return $pdo;
    }

    /**
     * This method sets the sql statement
     * to fetch data from the database
     * @return object $this
     */    
    public function find(){

        $this->SQL = "SELECT * FROM " . $this->DBTABLE;

        return $this;
    }

    /**
     * This method returns number of
     * row count for the last executed
     * query
     * @return int rowcount
     */ 
    public function rowCount(){
        
        return $this->prepare()->rowCount();
    }

    /**
     * This method is used to
     * modify the sql statement
     * by adding the "WHERE" filters
     * @return function
     */     
    public function where(array $array){
        
        return count($array) > 1 ? $this->doubleWhere($array) : $this->singleWhere($array);
    }

    /**
     * This method is called 
     * when a single "WHERE" filter is needed
     * in the sql statement
     * @return object $this
     */
    private function singleWhere(array $array){

        $this->SQL .= " WHERE ";

        foreach($array as $key => $value){

            $this->SQL .= "{$key} = ?";
        }

        $this->SQLPARAMETERS = array_merge($this->SQLPARAMETERS, $array);
        
        return $this;
    }

    /**
     * This method is called 
     * when a multiple "WHERE" filters are needed
     * in the sql statement
     * @return object $this
     */
    private function doubleWhere(array $array){

        foreach($array as $key => $value){

            $where[] = "{$key} = ?";
        }
        
        $this->SQL .= " WHERE " . implode(" AND ", $where);

        $this->SQLPARAMETERS = array_merge($this->SQLPARAMETERS, $array);

        return $this;
    }

    /**
     * This method is used to
     * modify the sql statement
     * by adding the "LIMIT" filters
     * @return function
     */     
    public function limit(){
        
        return func_num_args() > 1 ? $this->doubleLimit(func_get_args()) : $this->singleLimit(func_get_arg(0));
    }

    /**
     * This method is called 
     * when a single "LIMIT" filter is needed
     * in the sql statement
     * @return object $this
     */
    private function singleLimit($limit){

        $this->SQL .= " LIMIT {$limit} ";

        return $this;
    }

    /**
     * This method is called 
     * when a multiple "LIMIT" filters are needed
     * in the sql statement
     * @return object $this
     */
    private function doubleLimit(array $array){

        $this->SQL .= " LIMIT " . implode (" , ", $array);

        return $this;
    }

    /**
     * This method ckecks whether the sql
     * statement is set
     * @return bool
     */
    private function checkSql(){
        
        return isset($this->SQL) ? true : false;
    }

    /**
     * This method fetches the records
     * after the pdo statement has been
     * executed
     * @param object $pdo
     * @return array $pdo result
     */
    private function fetchThisRecord($pdo){
        
        return $pdo->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * This method executes, fetches,
     * as well as call the instantiation method
     * @return mixed array of object when the pdo execution 
     *               is true and bool when false
     */
    private function fetchThisQuery(){
        
        if($this->checkSql()){
            
            $sql_record = [];
            
            if($pdo = $this->prepare()){
                
                while(($row = $this->fetchThisRecord($pdo)) !== false){
                    
                    $sql_record[] = $this->instantiation($row);
                }

                return $this->recordArrayShift($sql_record);
            }

            return false;
        }
        
        return false;
    }

    /**
     * This method instantiates the results 
     * of the pdo query to the calling model
     * @param array $row
     * @return object $this
     */
    private function instantiation(array $row){
        $obj  = new $this;

        foreach($row as $key => $value){
            
            $obj->$key = $value;
        }
        
        return $obj;
    }

    /**
     * This method returns either the object
     * of the record or an array of objects for 
     * multiple records
     * @param array $sql_record
     * @return mixed array|object
     */
    private function recordArrayShift(array $sql_record){
        
        return count($sql_record) > 1 ? $sql_record : array_shift($sql_record);
    }

    /**
     * This method creates a new record in the database
     * @param array|object $data
     * @return mixed the last created id (int) when a record is successfully created
     *               and false (bool) when an error occurs
     */
    public function create(array|object $data){

        $this->SQLPARAMETERS = $this->convertObjectToArray($data);

        $placeholder = $this->preparedStatementPlaceHolder($this->SQLPARAMETERS);

        $this->SQL = "INSERT INTO " . $this->DBTABLE . " (" . implode( "," , array_keys($this->SQLPARAMETERS)) .  " ) VALUES (" . implode("," , array_values($placeholder)) . ")";

        return $this->prepare() ? $this->DBCONNECT->lastInsertId() : false;
    }

    /**
     * This method converts objects to array
     * @param array|object $data
     * @return array $data
     */
    private function convertObjectToArray(array|object $data) {

		if(is_object($data)){

			return get_object_vars($data);
		}

		return $data;
	}

    /**
     * This method creates place holders for the
     * sql prepared statement
     * @param array $parameters
     * @return array $placeholder
     */
    private function preparedStatementPlaceHolder(array $parameters) {
        $placeholder = [];
    
        foreach($parameters as $key){
            $placeholder[] = "?";
        }

        return $placeholder;
    }

    /**
     * This method updates the database with
     * a piece of data
     * @param array|object $data
     * @return mixed the updated id (int) when a record is successfully updated
     *               and false (bool) when an error occurs
     */
    public function update(array|object $data){
        $update = [];

        $this->SQLPARAMETERS = $this->convertObjectToArray($data);

        foreach($data as $key => $value){
            
            $update[] = "{$key} = ?";
        }
        
        $this->SQL  = "UPDATE " . $this->DBTABLE . " SET ";
        $this->SQL .= implode(",", $update);
        $this->SQL .= " WHERE id = " . $this->id;

        return $this->prepare() ? $this->id : false;
    }

    /**
     * This method calls either the update method or
     * the create method. If the record already exists, the
     * update method is called otherwise the create method is
     * called.
     * @param array|object $data
     * @return function
     */
    public function save(array|object $data){

        return (
            $this->id ? $this->update($data) : $this->create($data)
        );
    }

    /**
     * This method deletes a record from the database
     * @param array|object $data
     * @return mixed the deleted id (int) when a record is successfully deleted
     *               and false (bool) when an error occurs
     */
    public function delete(){

        $this->SQL = "DELETE FROM " . $this->DBTABLE . " WHERE id =" . $this->id;

        return $this->prepare() ? $this->id : false;
    }

}

?>