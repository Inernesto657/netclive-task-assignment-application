<?php
namespace Controllers;
use Controllers\DepartmentManager as DM;
use Models\Roles;
use Models\Users;
use Models\Tasks;
use Models\TaskRequests as TRs;
use Models\AssignedTasks as ATs;
use Models\Notifications;
use Core\Authentication as Auth;
use Core\Request;
use PDO;
use PDOException;

class Worker extends DM{
    
    private $notificationsTabs = false;

    /**
     * This magic custom method allows
     * decendants of this class to call
     * inaccessible methods of this class
     * @param $method (method name)
     * @param $args (arguments passed to the method, if any)
     * @return function (i.e the inaccessible method of this class)
     */
    public function __call($method, $args){
        
        if(method_exists(__CLASS__, $method)) {
            return call_user_func_array([$this, $method], $args);
        }

        return $this->permissionRestricted();
    }

    /**
     * This magic custom method allows
     * decendants of this class to read
     * inaccessible properties of this class
     * @param $property (property name)
     * @return mixed (the called property)
     */
    public function __get($property){

        if(property_exists(__CLASS__, $property)) {
            return $this->$property;
        }

        return $this->permissionRestricted();
    }

    /**
     * This magic custom method allows
     * decendants of this class to write data to
     * inaccessible properties of this class
     * @param $property (property name)
     * @param $value (data to write to the property)
     */
    public function __set($property, $value){

        if(property_exists(__CLASS__, $property)) {
            $this->$property = $value;
        }

        return $this->permissionRestricted();
    }

    public function index() {
        $data  = [];

        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        $roles = (new Roles())->find()->fetchThisQuery();

        $tasksObj = (new ATs())->find()->where(["assignee" => $auth->email])->fetchThisQuery();

        if(is_object($tasksObj)){

            $tasks[] = $tasksObj;
        }else{

            $tasks = $tasksObj;
        }

        return $this->view("admin.worker.index", compact("notificationsTabs", "roles", "tasks", "auth", $data));
    }

    private function showSalesUnassignedTasks() {
        $auth = (new Auth())->user();

        if(strcasecmp($auth->department, "sales") == 0){
        
            $data  = [];

            $tasksObj = (new Tasks())->find()->where([
                "status"      => "unassigned",
                "department"  => "sales"
            ])->fetchThisQuery();

            if(is_object($tasksObj)){

                $tasks[] = $tasksObj;
            }else{

                $tasks = $tasksObj;
            }
            
            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.worker.sales_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    private function showProductionUnassignedTasks() {
        $auth = (new Auth())->user();

        if(strcasecmp($auth->department, "production") == 0){

            $data  = [];

            $tasksObj = (new Tasks())->find()->where([
                "status"      => "unassigned",
                "department"  => "production"
            ])->fetchThisQuery();

            if(is_object($tasksObj)){

                $tasks[] = $tasksObj;
            }else{

                $tasks = $tasksObj;
            }
            
            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.worker.production_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    private function showAssignTaskForm(int $taskId) {
        $auth = (new Auth())->user();

        $task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery();

        if($task->taskCartegoryHierarchicalValue == $auth->hierarchicalValue) {
        
            $data = [];

            $users[] = $auth;

            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.worker.assign_task_form", compact("auth", "users", "roles", "notificationsTabs", "task", $data));
        }

        $_SESSION['error'] = "sorry, you cannot assign this task!!!";

        return header("Location: ?worker/index");
    }

    private function assignTask(Request $request) {
        $taskAssignmentObj = (new ATs())->find()->where(["assignee" => $request->assignee])->fetchThisQuery();

        if(is_array($taskAssignmentObj)){

            $taskAssignments = $taskAssignmentObj;
        }else{

            $taskAssignments[] = $taskAssignmentObj;
        }

        if(count($taskAssignments) < 3){

            if($taskAssignmentId = (new ATs())->create($request)){

                $task = (new Tasks())->find()->where(["id" => $request->taskId])->fetchThisQuery();

                $task->save(["status" => "assigned"]);

                $this->pushNotificationCreation("assignTask", $task->id, $request->assigneeDepartment, $request->assignee);

                $_SESSION['message'] = "task has been assigned!!!";

                return header("Location: ?worker/index");
            }
        }else{

            $_SESSION['error'] = "sorry, can not assign more than 3 tasks to an assignee!!!";

            return header("Location: ?worker/index");
        }
    }

    private function updateTaskStatus(int $taskId) {
        $auth = (new Auth())->user();

        if(
            $task = (new ATs())->find()->where([
                "assignee" => $auth->email,
                "taskId"   => $taskId
            ])->fetchThisQuery()
        ){
            $taskUpdate = $task->update(["status" => "completed"]);

            $this->pushNotificationCreation("completeTask", $task->id, $task->assigneeDepartment);

            $_SESSION['message'] = "this task {$task->taskName} has now been updated to completed!!!";
            
            return header("Location: ?worker/index");
        }

        $_SESSION['error'] = "sorry this task was not found!!!";
            
        return header("Location: ?worker/index");
    }
} 
?>