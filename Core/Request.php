<?php
namespace Core;
use Core\Router;
use Exception;

class Request {
    private $requestCheck = false;
    private $uploadsCheck = false;
    private $uploads = [];
    private $request = [];

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
    
    private function checkRequest() {
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $this->requestCheck = true;
        }

        return $this;
    }

    private function setRequest() {
        if($this->requestCheck) {
            $this->request = $_POST;
        }
    }

    public function request() {
        if(func_num_args() == 0){
            return $this->getRequest();
        }
        
        return $this->getRequestValue(func_get_arg(0));
    }

    private function getRequest() {
        return $this->instantiateRequestVariables();
    }

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

    private function getRequestValue(string $value){
        if(array_key_exists($value, $this->request)){
            return $this->request[$value];
        }

        return throw new Exception("Request value does not exist");
    }

    private function checkUploads() {
        if(!empty($_FILES)){
            $this->uploadsCheck = true;
        }

        return $this;
    }

    private function setUploads(){
        if($this->uploadsCheck){
            $this->uploads = $_FILES;
        }
    }

    public function uploads() {
        if(func_num_args() == 0){
            return $this->uploads;
        }
        
        return $this->getUploadValue(func_get_arg(0));
    }

    private function getUploadValue(string $value){
        if(array_key_exists($value, $this->uploads)){
            return $this->uploads[$value];
        }

        return throw new Exception("Upload does not exist");
    }

    public function route($url) {
        return (new Router($url));
    }

    public function runRouter() {
        if(isset($_SERVER["REQUEST_METHOD"])){
            $url = htmlspecialchars($_SERVER['QUERY_STRING']);

            $this->route($url)->processUrlCall();
        }
    }
}

    
?>