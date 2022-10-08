<?php
namespace Controllers;
use Models\Tasks;
use Core\Request;
use Models\Users;
use Models\Roles;

trait TaskManagement {

    public function __construct(){

    }

    /**
     * This magic custom method allows
     * decendants of this class to call
     * inaccessible methods of this class
     * @param $method (method name)
     * @param $args (arguments passed to the method, if any)
     * @return function (i.e the inaccessible method of this class)
     */
    public function __call($method, $args){
        return call_user_func_array([$this, $method], $args);
    }

    public function createTask(Request $request) {
        
        if($user = (new Users())->find()->where(["id" => $_GET['id']])->fetchThisQuery()){

            switch($user->hierarchicalValue){
                case 1:
                    return $this->createGeneralManagerTask($request);
                break;

                case 2:
                    return $this->createDepartmentManagerTask($request, $user);
                break;

                default:
                    $_SESSION['error'] = "Access Denied: You cannot create this task";
                    return header("Location: ?netclive/index/");
            }
        }

        $_SESSION['error'] = "Access Denied: You cannot create this task";
        return header("Location: ?netclive/index/"); 
    }

    private function createGeneralManagerTask(array|object $tasksData) {

        if($taskId = (new Tasks())->create($tasksData)){

            $_SESSION['message'] = "Task has been successfully created!!!";
            return header("Location: ?netclive/index/");
        }
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

    private function assignTask() {
        
    }
}
?>