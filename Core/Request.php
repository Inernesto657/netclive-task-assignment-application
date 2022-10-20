<?php
namespace Core;
use Core\Router;
use Exception;

/**
 * This Class handles all form data sent to the application
 * Class Request
 * @package Core
 */
class Request {

    /**
     * stores a value depending on whether a form data is sent or not
     * @var mixed requestCheck
     */
    private $requestCheck = false;

    /**
     * stores a value depending on whether a file is uploaded or not
     * @var mixed uploadsCheck
     */    
    private $uploadsCheck = false;

    /**
     * stores the uploaded file data
     * @var mixed uploads
     */    
    private $uploads = [];

    /**
     * stores the form data
     * @var mixed request
     */
    private $request = [];

    /**
     * calls some methods of this class when an object
     * is instantiated
     */
    public function __construct(){
        $this->checkRequest();
        $this->setRequest();
        $this->checkUploads();
        $this->setUploads();
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
     * checks if a POST request was made to the server
     * @return boolean
     */
    private function checkRequest() {
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $this->requestCheck = true;
        }

        return $this;
    }

    /**
     * sets the request array with the POST data 
     * if a POST request was made to the server
     * @return void
     */
    private function setRequest() {
        if($this->requestCheck) {
            $this->request = $_POST;
        }
    }

    /**
     * returns the request
     * @return method (i.e returns all the request data as an object if no particular request key was indicated)
     * @return method (i.e returns the particular request data that was indicated)
     */
    public function request() {
        if(func_num_args() == 0){
            return $this->getRequest();
        }
        
        return $this->getRequestValue(func_get_arg(0));
    }

    /**
     * returns the request data object
     * @return method (i.e the object of the Request class with the form data fields as properties)
     */
    private function getRequest() {
        return $this->instantiateRequestVariables();
    }

    /**
     * instanciates the Request class and sets the form data fields
     * as properties of the Request class
     * @return object obj
     */
    private function instantiateRequestVariables(){
        $obj = new $this;

        foreach(get_object_vars($obj) as $key => $value){
            unset($obj->$key);
        }

        if(empty($this->request)){
            return throw new Exception("Request does not exist");
        }

        foreach($this->request as $key => $value){
            $obj->$key = $value;
        }
        
        return $obj;
    }

    /**
     * returns the particular request data indicated
     * @return mixed (i.e the indicated request data)
     * @return object (i.e throws an exception if the data field indicated does not exist in the request property)
     */
    private function getRequestValue(string $value){
        if(array_key_exists($value, $this->request)){
            return $this->request[$value];
        }

        return throw new Exception("Request value does not exist");
    }

    /**
     * checks if a file(s) has been uploaded and sets the uploadsCheck property
     * @return object
     */
    private function checkUploads() {
        if(!empty($_FILES)){
            $this->uploadsCheck = true;
        }

        return $this;
    }

    /**
     * retrieves the uploaded files and sets them into the uploads property
     * @return void
     */
    private function setUploads(){
        if($this->uploadsCheck){
            $this->uploads = $_FILES;
        }
    }

    /**
     * returns the uploaded files
     * @return method (i.e returns all the uploaded files if no particular file was indicated)
     * @return method (i.e returns the particular file that was indicated)
     */
    public function uploads() {
        if(func_num_args() == 0){
            return $this->uploads;
        }
        
        return $this->getUploadValue(func_get_arg(0));
    }

    /**
     * returns the particular file indicated
     * @return mixed (i.e the indicated file)
     * @return object (i.e throws an exception if the file indicated does not exist in the uploads property)
     */
    private function getUploadValue(string $value){
        if(array_key_exists($value, $this->uploads)){
            return $this->uploads[$value];
        }

        return throw new Exception("Upload does not exist");
    }

    /**
     * returns an object instance of the Router Class
     * @return object
     */
    public function route($url) {
        return (new Router($url));
    }

    /**
     * calls the router class to process a url request made by the browsing user
     * @return void
     */
    public function runRouter() {
        if(isset($_SERVER["REQUEST_METHOD"])){
            $url = htmlspecialchars($_SERVER['QUERY_STRING']);

            $this->route($url)->processUrlCall();
        }
    }
}

    
?>