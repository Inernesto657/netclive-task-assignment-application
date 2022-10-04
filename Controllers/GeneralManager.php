<?php
namespace Controllers;
use Controllers\Netclive;
use Models\Roles;

class GeneralManager extends Netclive {

    public function index() {
        $roles = (new Roles())->find()->fetchThisQuery();
        $tasks = $this->tasks; $users = $this->users;
        $data = [];
        return $this->view("admin.general_manager", compact("roles", "tasks", "users", $data));
    }
} 
?>