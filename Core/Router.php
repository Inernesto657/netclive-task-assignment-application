<?php

namespace Core;
use Core\Request;

/**
 * This class is responsible for mapping
 * the Url with the various Controllers (classes)
 * and Actions (methods)
 * Class Router
 * @package Core
 */
class Router {

    /**
     * Pattern for the regular expression
     * @var string $regexPattern
     */
    private $regexPattern = "/^(?<controller>[\w\-\+]+)\/(?<action>[\w\-\+]*)\/*(?<argument>[\d\w\s\-]*)\/*$/i";

    /**
     * Processed url varibles
     * default controller(class) => home
     * default action(method) => index
     * default argument => '' 
     * @var array $urlVariables
     */    
    private $urlVariables = [
        "controller" => "home",
        "action"     => "index",
        "argument"   => "",
    ];

    /**
     * Router Parameters
     * @var array $parameters
     */    
    private $parameters = [];

    /**
     * calls some methods of this class any time this
     * class is been called
     */
    public function __construct(public $url) {
        $this->url = $this->removeQueryStringsVariables($this->url);

        $matches = [];

        preg_match($this->regexPattern, $this->url, $matches);

        $this->setUrlVariables($matches);

        $this->setRouterParameters();
    }

    /**
     * This function sets the default controller(class) name
     * and action(method) if it is not present in the route/url
     * @return object
     */
    private function setUrlVariables(array $url_variables = []) : object {
        
        foreach($this->urlVariables as $key => $value){

            array_key_exists($key, $url_variables) ? $this->urlVariables[$key] = $url_variables[$key] : "";
        }

        return $this;
    }

    /**
     * This function returns the controller(class) name
     * from the route/url
     * @return string
     */
    private function getController() : string{

        return $this->urlVariables['controller'];
    }

    /**
     * This function returns the action(method) name
     * from the route/url
     * @return string
     */
    private function getAction() : string{

        return $this->urlVariables['action'];
    }

    /**
     * This function returns the argument(variable) from the route/url if any,
     * to be passed when the method is called
     * @return string
     */
    private function getArgument() : array{

        return [$this->urlVariables['argument']];
    }

    /**
     * This function returns the parameters for processing
     * the desired url
     * @return object;
     */
    private function setRouterParameters() : object{

        $this->parameters["controller"] = "Controllers\\" . $this->convertToStudlyCaps($this->getController());
        $this->parameters["action"]     = $this->convertToCamelCase($this->getAction());
        $this->parameters["argument"]   = $this->getArgument();

        return $this;
    }

    /**
     * instanciates the Request Class and gets the requests (POST data) if any was made
     * @return void
     */
    private function handleRequest() {
        $request = new Request();

        if($request->requestCheck){ 
            array_unshift($this->parameters["argument"], $request->request());
        }
    }

    /**
     * This function processes the desired route/url call; 
     * @return;
     */
    public function processUrlCall(){
        if(class_exists($this->parameters['controller'])){
            $this->handleRequest();

            $controller = $this->parameters['controller'];
            $method    =  $this->parameters['action'];

            return (new $controller())->$method(...$this->parameters['argument']);
        }

        header("Location: ../Views/page_not_found.php");
    }

    /**
     * convert Controller(class) name to studly caps
     * @return string
     */
    private function convertToStudlyCaps($string){
        
        $string = str_replace("-", " ", $string);

        $string = str_replace("+", " ", $string);

        return str_replace(" ", "", ucwords($string));
    }

    /**
     * convert Action(method) name to camel case
     * @return string
     */
    private function convertToCamelCase($string){

        return lcfirst(self::convertToStudlyCaps($string));
    }

    /**
     * removes the query strings from the route/url (if any)
     * and returns only the route/url part containing the controllers
     * and actions
     * @return mixed url
     */
    private function removeQueryStringsVariables($url) : string {

        if(!empty($url)){

            $queryString = explode("&", $url, 2);

            if(strpos($queryString[0], "&") == false){

                $url = $queryString[0];
            }else{

                $url = "";
            }
        }

        return $url;
    }
}
?>