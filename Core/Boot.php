<?php

namespace Core;
use Core\Request;
use Core\Authentication as Auth;

/**
 * This Class initializes core and important
 * functionalities for this application.
 * Class Boot
 * @package Core
 */
class Boot{

    /**
     * calls some functionalities
     * when ever an object is being instantiated
     */
    public function __construct() {
        session_start();
        $this->integrateCoreFunctionalities();
    }

    /**
     * This method calls fundamental and integral functionalities
     * of the application
     * @return void
     */
    private function integrateCoreFunctionalities(){
        set_exception_handler([$this, "customException"]);
        (new Request())->runRouter();
        (new Auth());
    }

    /**
     * defines function for uncaught exceptions
     * @return void
     */
    public function customException ($e) { 
        if($e->getMessage()){
            echo $e->getMessage();
        }
    }
}
?>