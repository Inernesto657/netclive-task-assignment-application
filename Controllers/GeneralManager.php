<?php
namespace Controllers;
use Controllers\Netclive;
use Models\Roles;

class GeneralManager extends Netclive {

    public function index() {
        $tasks = $this->tasks;

        $users = $this->users;

        $data  = [];
        
        $roles = (new Roles())->find()->fetchThisQuery();

        return $this->view("admin.general_manager", compact("roles", "tasks", "users", $data));
    }
} 
?>