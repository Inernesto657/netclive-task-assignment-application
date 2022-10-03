<?php

namespace Core;
use Exception;

Class View {

    public function __construct($view, $data = []){

        if(isset($_SESSION["message"])) {
            $data["message"] = $_SESSION["message"];
            unset($_SESSION["message"]);
        }
    
        if(isset($_SESSION["error"])){
            $data["error"] = $_SESSION["error"];
            unset($_SESSION["error"]);
        }
        
        $this->view($view, $data);
    }

    public function view($view, $data) {

        $view = str_replace(".", "/", $view);

        $view = static::removeTrailingDash($view);

        extract($data, EXTR_SKIP);

        $file = "../Views/" . $view . ".php";

        if(is_readable($file)){

            require_once($file);
        }else{

            throw new Exception("<h1>File Not Found!!!</h1>");
        }
    }

    public static function removeTrailingDash($view) {
        
        return preg_replace("/[\/\\\]+$/", "", $view);
    }

}
?>