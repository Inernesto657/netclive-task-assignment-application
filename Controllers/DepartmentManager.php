<?php
namespace Controllers;
use Controllers\GeneralManager as GM;
use Models\Roles;
use Core\Authentication as Auth;

class DepartmentManager extends  GM{

    public function index() {
        $users = [];
        $tasks = [];
        $data = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();
        
        foreach($this->$users as $user){
            if($user->department == $auth->department){
                $users[] = $user;
            }
        }

        foreach($this->$tasks as $task){
            if($task->department == $auth->department){
                $tasks[] = $task;
            }
        }

        return $this->view("admin.department_manager", compact("roles", "tasks", "users", $data));
    }
} 
?>