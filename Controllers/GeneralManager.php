<?php
namespace Controllers;
use Controllers\Netclive;
use Models\Roles;
use Models\Users;
use Models\Tasks;
use Models\AssignedTasks as ATs;
use Models\Notifications;
use Core\Authentication as Auth;
use Core\Request;

class GeneralManager extends Netclive {

    /**
     * This magic custom method allows
     * decendants of this class to call
     * inaccessible methods of this class
     * @param $method (method name)
     * @param $args (arguments passed to the method, if any)
     * @return function (i.e the inaccessible method of this class)
     */
    public function __call($method, $args){
        
        if(method_exists($this, $method)) {
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

        if(property_exists($this, $property)) {
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

        if(property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this->permissionRestricted();
    }

    public function index() {
        $data  = [];

        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.index", compact("notificationsTabs", "auth", $data));
    }

    private function allUsers() {
        $data  = [];

        $users = $this->users;

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.all_users", compact("roles", "users", "notificationsTabs", "auth", $data));
    }

    private function salesUsers() {
        $data  = [];

        $usersObj = (new Users())->find()->where(["department" => "sales"])->fetchThisQuery();

        if(is_object($usersObj)){

            $users[] = $usersObj;
        }else{

            $users = $usersObj;
        }

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.sales_users", compact("roles", "users", "notificationsTabs", "auth", $data));
    }

    private function productionUsers() {
        $data  = [];

        $usersObj = (new Users())->find()->where(["department" => "production"])->fetchThisQuery();

        if(is_object($usersObj)){

            $users[] = $usersObj;
        }else{

            $users = $usersObj;
        }

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.production_users", compact("roles", "users", "notificationsTabs", "auth", $data));
    }

    private function allTasks() {
        $data  = [];

        $tasks = $this->tasks;

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.all_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showAssignedTasks() {
        $data  = [];

        $tasksObj = (new ATs())->find()->fetchThisQuery();

        if(is_object($tasksObj)){

            $tasks[] = $tasksObj;
        }else{

            $tasks = $tasksObj;
        }

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.assigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showSalesAssignedTasks() {
        $data  = [];

        $tasksObj = (new ATs())->find()->where(["assigneeDepartment"  => "sales"])->fetchThisQuery();

        if(is_object($tasksObj)){

            $tasks[] = $tasksObj;
        }else{

            $tasks = $tasksObj;
        }

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.sales_assigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showProductionAssignedTasks() {
        $data  = [];

        $tasksObj = (new ATs())->find()->where(["assigneeDepartment"  => "production"])->fetchThisQuery();

        if(is_object($tasksObj)){

            $tasks[] = $tasksObj;
        }else{

            $tasks = $tasksObj;
        }

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.production_assigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showUnassignedTasks() {
        $data  = [];

        $tasksObj = (new Tasks())->find()->where(["status" => "unassigned"])->fetchThisQuery();

        if(is_object($tasksObj)){

            $tasks[] = $tasksObj;
        }else{

            $tasks = $tasksObj;
        }

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showSalesUnassignedTasks() {
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

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.sales_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showProductionUnassignedTasks() {
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

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.production_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    private function showCreateTaskForm() {
        $data = [];

        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.create_task_form", compact("auth", "notificationsTabs", $data));
    }

    private function createTask(Request $request) {
        
        if($taskId = (new Tasks())->create($request)){

            $_SESSION['message'] = "task has been successfully created!!!";
            return header("Location: ?general+manager/show+create+task+form");
        }
    }

    private function showAssignTaskForm(int $taskId) {
        $data = [];

        $auth = (new Auth())->user();

        $users = $this->users;

        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        $task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery();

        return $this->view("admin.general_manager.assign_task_form", compact("auth", "users", "roles", "notificationsTabs", "task", $data));
    }

    private function assignTask(Request $request) {
        if($taskAssignmentId = (new ATs())->create($request)){

            $task = (new Tasks())->find()->where(["id" => $request->taskId])->fetchThisQuery();

            $task->save(["status" => "assigned"]);

            $_SESSION['message'] = "task has been assigned!!!";

            return header("Location: ?general+manager/all+tasks");
        }
    }

    private function cancelTask(int $taskId) {
        if($taskAssignment = (new ATs())->find()->where(["taskId" => $taskId])->fetchThisQuery()){
        
            $taskAssignment->delete();

            $task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery();

            $task->save(["status" => "unassigned"]);

            $_SESSION['message'] = "task assignment has been cancelled!!!";

            return header("Location: ?netclive/index/");
        }

        $_SESSION['error'] = "sorry this task has not been assigned yet!!!";

        return header("Location: ?netclive/index/");
    }

    private function deleteTask(int $taskId) {
        if($task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery()){
        
            if($task->delete()){

                if($taskAssignment = (new ATs())->find()->where(["taskId" => $taskId])->fetchThisQuery()){

                    if($taskAssignment->delete()){

                        $_SESSION['message'] = "task has been deleted!!!";

                        return header("Location: ?netclive/index/");
                    }
                }

                $_SESSION['message'] = "task has been deleted!!!";

                return header("Location: ?netclive/index/");
            }
        }

        $_SESSION['error'] = "sorry this task does not exist!!!";

        return header("Location: ?netclive/index/");
    }

    private function showNotificationsTabs() {
        $data  = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.notifications", compact("auth", "notificationsTabs", "roles", $data));
    }

    private function notificationViewUpdate() {
        if($userId = (new Auth())->user()->update(["updatedAt" => date("y-m-d h:i:s", strtotime("now"))])){

            $_SESSION['message'] = "notifications marked as viewed";

            return header("Location: ?general+manager/show+notifications+tabs");
        }
    }

    private function deleteNotifications() {
        $notifications = (new Notifications());

        $sql = "DELETE FROM " . $notifications->DBTABLE;

        if($notificationsDeleted = $notifications->execute($sql)){

            $_SESSION['message'] = "notifications have been deleted!!!";

            return header("Location: ?netclive/index/show+notifications+tabs");
        }
    }
} 
?>