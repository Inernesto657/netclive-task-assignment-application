<?php

namespace Core;
use Core\Request;

/**
 * This Class initializes core and important
 * functionalities for this application.
 * Class Boot
 * @package Core
 */
class Boot{

    public function __construct() {
        session_start();
        $this->integrateCoreFunctionalities();
    }

    /**
     * This method imports our important functionalities
     * @return void
     */
    private function integrateCoreFunctionalities(){
        set_exception_handler([$this, "customException"]);
        (new Request())->runRouter();
        include_once("functions.php");
    }

    public function customException ($e) { 
        if($e->getMessage()){
            echo $e->getMessage();
        }
    }
}
?>