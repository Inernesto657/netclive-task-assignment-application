<?php

namespace Controllers;
use Core\Controller;
use Core\Authentication as Auth;

/**
 * This Class handles functionalities for the welcome page
 * Class Home
 * @package Controllers
 */
class Home extends Controller{

    /**
     * displays the welcome veiw for the whole application
     * @return method (i.e the corresponding view)
     */
    public function index(){
        
        $data["auth"] = new Auth();

        return $this->view("home.index", $data);
    }
}

?>