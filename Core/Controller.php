<?php

namespace Core;
use Core\View;

abstract class Controller {

    public function __construct(){
    
    }

    public function view($view, $data = []) {
        return new View($view, $data);
    }

}
?>