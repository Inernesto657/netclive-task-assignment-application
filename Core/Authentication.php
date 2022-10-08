<?php
namespace Core;
use Models\Users;

class Authentication {
    public $id = false; 

    public function __construct() {
        $this->setAuthId();
    }

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

    private function setAuthId() {
        if(isset($_SESSION['user_id'])){

            return $this->id = $_SESSION['user_id'];
        }

        return $this->id = false;
    }

    public function loggedIn() {
        if($this->id){
            
            return true;
        }

        return false;
    }

    public function user() {
        if($this->loggedIn()) {

            return (new Users())->find()->where(["id" => $this->id])->fetchThisQuery();
        }

        return false;
    }
}
?>