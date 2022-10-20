<?php
namespace Controllers;
use Controllers\DepartmentManager as DM;
use Models\Roles;
use Models\Users;
use Models\Tasks;
use Models\AssignedTasks as ATs;
use Core\Authentication as Auth;
use Core\Request;
use PDO;
use PDOException;

/**
 * This Class handles functionalities for the worker
 * Class Worker
 * @package Controllers
 */
class Worker extends DM{
    
    /**
     * sets notifications to be seen by the logged-in user (i.e worker)
     */
    private $notificationsTabs = false;

    /**
     * this magic custom method allows
     * objects of this class, as well as 
     * decendants of this class to call
     * inaccessible methods of this class outside 
     * this class domain.
     * @param mixed method (method name)
     * @param mixed args (arguments passed to the method, if any)
     * @return mixed
     */
    public function __call($method, $args){
        
        if(method_exists(__CLASS__, $method)) {
            return call_user_func_array([$this, $method], $args);
        }

        return $this->permissionRestricted();
    }

    /**
     * this magic custom method allows
     * objects of this class, as well as 
     * decendants of this class to call
     * inaccessible properties of this class outside 
     * this class domain.
     * @param mixed property (property name)
     * @return mixed
     */
    public function __get($property){

        if(property_exists(__CLASS__, $property)) {
            return $this->$property;
        }

        return $this->permissionRestricted();
    }

    /**
     * this magic custom method allows
     * objects of this class, as well as 
     * decendants of this class to set
     * inaccessible properties of this class outside 
     * this class domain.
     * @param mixed property (property name)
     * @param mixed value (data to write to the property)
     * @return mixed
     */
    public function __set($property, $value){

        if(property_exists(__CLASS__, $property)) {
            $this->$property = $value;
        }

        return $this->permissionRestricted();
    }

    /**
     * displays general information about tasks assigned 
     * to logged-in user (i.e worker)
     * @return method (i.e the corresponding veiw)
     */    
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

    /**
     * displays the unassigned tasks in the sales department
     * NB: when the logged-in user (worker) does
     * not belong to the sales department, 
     * a permissionRestricted method is called
     * @return method (the corresponding view)
     */
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

    /**
     * displays the unassigned tasks in the production department
     * NB: when the logged-in user (worker) does
     * not belong to the production department, 
     * a permissionRestricted method is called
     * @return method (the corresponding view)
     */
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

    /**
     * displays the task assignment form
     * NB: allows the logged-in user (worker) to 
     * assign task to self and nobody else
     * @param int taskId
     * @return method (the corresponding view)
     */
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

    /**
     * creates the task assigment into the assigned_tasks DB once the 
     * form request has been made
     * @param object request (request from the form) 
     * @return function (i.e redirection back to index method)
     */ 
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

    /**
     * updates the status of tasks assigned to the logged-in user
     * to completed
     * @return function (i.e redirection back to the index method)
     */
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