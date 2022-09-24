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
    /************************************* Connection Code ********************************/

    public function __construct() {
        
        $this->connection();
    }

    /**
     * This function makes connection to the database
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
     * This function executes the sql statement
     * using PDO
     * @param string $sql 
     * @param array $parameters (if any) 
     * @return void
     */
    public function execute(string $sql = "", array $parameters = []) {

        $statement = $this->DBCONNECT->prepare($sql);

        $statement->execute($parameters);

        return $statement;
    }

    /**
     * This function strengthens the sql statement
     * using PDO prepared statements
     * @return object $pdo
     */
    public function prepare() {

        if(empty($this->SQLPARAMETERS)){

            $pdo = $this->DBCONNECT->prepare($this->SQL);

            $this->SQLPARAMETERS = [];
            
            $this->SQL = "";
        }else{   

            $pdo = $this->DBCONNECT->prepare($this->SQL);
    
            $pdo->execute(array_values($this->SQLPARAMETERS));

            $this->SQLPARAMETERS = [];

            $this->SQL = "";
        }

        return $pdo;
    }

    /**
     * This function sets the sql statement
     * to fetch data from the database
     * @return object $this
     */    
    public function find(){

        $this->SQL = "SELECT * FROM " . $this->db_table;

        return $this;
    }

    /**
     * This function returns number of
     * row count for the last executed
     * query
     * @return int rowcount
     */ 
    public function rowCount(){
        
        return $this->prepare()->rowCount();
    }

    /**
     * This function is used to
     * modify the sql statement
     * by adding the "WHERE" filters
     * @return function
     */     
    public function where(array $array){
        
        return count($array) > 1 ? $this->doubleWhere($array) : $this->singleWhere($array);
    }

    /**
     * This function is called 
     * when a single "WHERE" filter is needed
     * in the sql statement
     * @return object $this
     */
    public function singleWhere(array $array){

        $this->SQL .= " WHERE ";

        foreach($array as $key => $value){

            $this->SQL .= "{$key} = ?";
        }

        $this->SQLPARAMETERS = array_merge($this->SQLPARAMETERS, $array);
        
        return $this;
    }

    /**
     * This function is called 
     * when a multiple "WHERE" filters are needed
     * in the sql statement
     * @return object $this
     */
    public function doubleWhere(array $array){

        foreach($array as $key => $value){

            $where[] = "{$key} = ?";
        }
        
        $this->SQL .= " WHERE " . implode(" AND ", $where);

        $this->SQLPARAMETERS = array_merge($this->SQLPARAMETERS, $array);

        return $this;
    }

    /**
     * This function is used to
     * modify the sql statement
     * by adding the "LIMIT" filters
     * @return function
     */     
    public function limit(){
        
        return func_num_args() > 1 ? $this->doubleLimit(func_get_args()) : $this->singleLimit(func_get_arg(0));
    }

    /**
     * This function is called 
     * when a single "LIMIT" filter is needed
     * in the sql statement
     * @return object $this
     */
    public function singleLimit($limit){

        $this->SQL .= " LIMIT {$limit} ";

        return $this;
    }

    /**
     * This function is called 
     * when a multiple "LIMIT" filters are needed
     * in the sql statement
     * @return object $this
     */
    public function doubleLimit(array $array){

        $this->SQL .= " LIMIT " . implode (" , ", $array);

        return $this;
    }

    /**
     * This function ckecks whether the sql
     * statement is set
     * @return bool
     */
    public function checkSql(){
        
        return isset($this->SQL) ? true : false;
    }

    /**
     * This function fetches the records
     * after the pdo statement has been
     * executed
     * @param object $pdo
     * @return array $pdo result
     */
    public function fetchThisRecord($pdo){
        
        return $pdo->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * This function executes, fetches,
     * as well as call the instantiation method
     * @return mixed array of object when the pdo execution 
     *               is true and bool when false
     */
    public function fetchThisQuery(){
        
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
     * This function instantiates the results 
     * of the pdo query to the calling model
     * @param array $row
     * @return object $this
     */
    public function instantiation(array $row){
        
        foreach($array as $key => $value){
            
            $this->$key = $value;
        }
        
        return $this;
    }

    /**
     * This function returns either the object
     * of the record or an array of objects for 
     * multiple records
     * @param array $sql_record
     * @return mixed array|object
     */
    public function recordArrayShift(array $sql_record){
        
        return count($array) > 1 ? $array : array_shift($array);
    }

    /**
     * This function creates a new record in the database
     * @param array|object $data
     * @return mixed the last created id (int) when a record is successfully created
     *               and false (bool) when an error occurs
     */
    public function create(array|object $data){

        $this->SQLPARAMETERS = $this->convertObjectToArray($data);

        $placeholder = $this->preparedStatementPlaceHolder($this->SQLPARAMETERS);

        $this->SQL = "INSERT INTO " . $this->db_table . " (" . implode( "," , array_keys($this->SQLPARAMETERS)) .  " ) VALUES (" . implode("," , array_values($placeholder)) . ")";

        return $this->prepare() ? $this->DBCONNECT->lastInsertId() : false;
    }

    /**
     * This function converts objects to array
     * @param array|object $data
     * @return array $data
     */
    public function convertObjectToArray(array|object $data) {

		if(is_object($data)){

			return get_object_vars($data);
		}

		return $data;
	}

    /**
     * This function creates place holders for the
     * sql prepared statement
     * @param array $parameters
     * @return array $placeholder
     */
    public function preparedStatementPlaceHolder(array $parameters) {
        $placeholder = [];
    
        foreach($parameters as $key){
            $placeholder[] = "?";
        }

        return $placeholder;
    }

    /**
     * This function updates the database with
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
        
        $this->SQL  = "UPDATE " . $this->db_table . " SET ";
        $this->SQL .= implode(",", $update);
        $this->SQL .= " WHERE id = " . $this->id;

        return $this->prepare() ? $this->id : false;
    }

    /**
     * This function calls either the update method or
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
     * This function deletes a record from the database
     * @param array|object $data
     * @return mixed the deleted id (int) when a record is successfully deleted
     *               and false (bool) when an error occurs
     */
    public function delete(){

        $this->SQL = "DELETE FROM " . $this->db_table . " WHERE id =" . $this->id;

        return $this->prepare() ? $this->id : false;
    }

}

?>