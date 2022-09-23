<?php

namespace Core;

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
    private $regexPattern = "/^(?<controller>[\w\-\/]+)\/(?<action>[\w\-]*)\/*(?<argument>[\d\w\s\-]*)\/*$/i";

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


    public function __construct(public $url) {
        $matches = [];

        preg_match($this->regexPattern, $this->url, $matches);

        $this->setUrlVariables($matches);

        $this->setRouterParameters();
    }

    /**
     * This function sets the default controller(class) name
     * and action(method) if it is not present in the url
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
     * from the url
     * @return string
     */
    private function getController() : string{

        return $this->urlVariables['controller'];
    }

    /**
     * This function returns the action(method) name
     * from the url
     * @return string
     */
    private function getAction() : string{

        return $this->urlVariables['action'];
    }

    /**
     * This function returns the argument(variable) from the url if any,
     * to be passed when the method is called
     * @return string
     */
    private function getArgument() : string{

        return $this->urlVariables['argument'];
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
     * This function processes the desired url call; 
     * @return;
     */
    public function processUrlCall(){
        if(class_exists($this->parameters['controller'])){

            $controller = $this->parameters['controller'];
            $method    =  $this->parameters['action'];

            return (new $controller())->$method($this->parameters['argument']);
        }

        header("Location: ../Views/page_not_found.php");
    }

    /**
     * convert Controller(class) name to studly caps
     * @return string
     */
    private function convertToStudlyCaps($string){
        
        $string = str_replace("-", " ", $string);

        $string = str_replace("/", " ", $string);

        return str_replace(" ", "", ucwords($string));
    }

    /**
     * convert Action(method) name to camel case
     * @return string
     */
    private function convertToCamelCase($string){

        return lcfirst(self::convertToStudlyCaps($string));
    }

}
?>