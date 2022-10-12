<?php
namespace Controllers;
use Controllers\GeneralManager as GM;
use Models\Roles;
use Core\Authentication as Auth;

class DepartmentManager extends  GM{

    public function index() {
        $data  = [];
        $users = [];
        $tasks = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();
        
        foreach($this->users as $user){
            if($user->department == $auth->department){
                $users[] = $user;
            }
        }

        foreach($this->tasks as $task){
            if($task->department == $auth->department){
                $tasks[] = $task;
            }
        }

        return $this->view("admin.department_manager", compact("roles", "tasks", "users", $data));
    }

    public function showCreateTaskForm() {
        return $this->view("admin.show_create_task_form");
    }

    public function createTask(Request $request) {
        
        $user = (new Auth())->user();

        switch($user->hierarchicalValue){
            case 2:
                return $this->createDepartmentManagerTask($request, $user);
            break;

            default:
                $_SESSION['error'] = "Access Denied: You cannot create this task";
                return header("Location: ?netclive/index/");
        }

        $_SESSION['error'] = "Access Denied: You cannot create this task";
        return header("Location: ?netclive/index/"); 
    }

    private function createDepartmentManagerTask(array|object $tasksData, object $user){

        if($this->taskCreationPermissionForDepartmentManager($tasksData, $user)){
            
            if($taskId = (new Tasks())->create($tasksData)){

                $_SESSION['message'] = "Task has been successfully created!!!";
                return header("Location: ?netclive/index/");
            }
        }

        $_SESSION['error'] = "Access Denied: You cannot create this task";
        return header("Location: ?netclive/index/");
    }

    private function taskCreationPermissionForDepartmentManager(array|object $tasksData, object $user) {
        $role = (new Roles())->find()->where(["name" => $tasksData->taskCartegory])->fetchThisQuery();

        if($user->department != $tasksData->department || $user->hierarchicalValue >= $role->hierarchicalValue){
            return false;
        }

        return true;
    }
} 
?>