<?php
namespace Core;
use Models\Users;

/**
 * This Class handles the retrieval of the logged-in user information
 * Class Authentication
 * @package Core
 */
class Authentication {

    /**
     * holds the id of the logged-in user
     * @var id
     */
    public $id = false; 

    /**
     * calls some methods of this class
     * when ever an object is being instantiated
     */
    public function __construct() {
        $this->setAuthId();
    }

    /**
     * This magic custom method allows
     * decendants of this class to call
     * inaccessible methods of this class
     * @param mixed method (method name)
     * @param mixed args (arguments passed to the method, if any)
     * @return function (i.e the inaccessible method of this class)
     */
    public function __call($method, $args){
        
        return call_user_func_array([$this, $method], $args);
    }

    /**
     * This magic custom method allows
     * decendants of this class to read
     * inaccessible properties of this class
     * @param mixed property (property name)
     * @return mixed (the called property)
     */
    public function __get($property){

        return $this->$property;
    }

    /**
     * This magic custom method allows
     * decendants of this class to write data to
     * inaccessible properties of this class
     * @param mixed property (property name)
     * @param mixed value (data to write to the property)
     */
    public function __set($property, $value){

        $this->$property = $value;
    }

    /**
     * sets the id property with the logged-in user id
     * @return int id
     * @return boolean
     */
    private function setAuthId() {
        if(isset($_SESSION['user_id'])){

            return $this->id = $_SESSION['user_id'];
        }

        return $this->id = false;
    }

    /**
     * checks if a user is logged-in
     * @return boolean
     */
    public function loggedIn() {
        if($this->id){
            
            return true;
        }

        return false;
    }

    /**
     * returns the logged-in user object
     * @return object user
     * @return boolean
     */
    public function user() {
        if($this->loggedIn()) {

            return (new Users())->find()->where(["id" => $this->id])->fetchThisQuery();
        }

        return false;
    }
}
?>