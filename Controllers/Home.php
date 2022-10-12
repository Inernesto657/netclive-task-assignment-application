<?php

namespace Controllers;
use Core\Controller;
use Core\Authentication as Auth;

class Home extends Controller{

    public function index(){
        
        $data["auth"] = new Auth();

        return $this->view("home.index", $data);
    }
}

?>