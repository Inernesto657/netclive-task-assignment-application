<?php
namespace Controllers;
use Controllers\DepartmentManager as DM;
use Models\Roles;
use Core\Authentication as Auth;

class Worker extends DM{

    public function index() {
        $tasks = [];
        $data  = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();

        foreach($this->tasks as $task){
            if(
                $task->department == $auth->department 
                && $task->status == "unassigned" 
                && $task->taskCartegory == "workers"
            ){
                $tasks[] = $task;
            }
        }

        return $this->view("admin.worker", compact("roles", "tasks", $data));
    }
} 
?>