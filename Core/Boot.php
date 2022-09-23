<?php

namespace Core;

/**
 * This Class initializes core and important
 * functionalities for this application.
 * Class Boot
 * @package Core
 */
class Boot{

    public function __construct() {
        $this->includeCorePages();
    }

    /**
     * This method imports our important functionalities
     * @return void
     */
    private function includeCorePages(){
        include_once("functions.php");
    }
}
?>