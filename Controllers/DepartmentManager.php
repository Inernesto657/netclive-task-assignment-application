<?php
namespace Controllers;
use Controllers\GeneralManager as GM;
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

class DepartmentManager extends  GM{

    private $notificationsTabs = [];

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

    public function __construct() {
        parent::__construct();

        $this->fetchNotificationsTabs();
    }

    private function fetchNotificationsTabs() {
        $user = (new Auth())->user();

        $notificationsTabs = [];

        $notifications = (new Notifications());

        $sql = "SELECT * FROM " . $notifications->DBTABLE . " WHERE time >= ? AND department = ?";

        $notifications = $notifications->execute($sql, [$user->updatedAt, $user->department])->fetchAll(PDO::FETCH_ASSOC);

        foreach($notifications as $notification){

            $notificationsTabs[] = (object) $notification;
        }

        $this->notificationsTabs = $notificationsTabs;
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

        return $this->view("admin.department_manager.index", compact("notificationsTabs", "roles", "tasks", "auth", $data));
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
            
            return header("Location: ?department+manager/index");
        }

        $_SESSION['error'] = "sorry this task was not found!!!";
            
        return header("Location: ?department+manager/index");
    }    

    private function showNotificationsTabs() {
        $data  = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.department_manager.notifications", compact("auth", "notificationsTabs", "roles", $data));
    }

    private function notificationViewUpdate() {
        date_default_timezone_set("America/New_York");

        if($userId = (new Auth())->user()->update(["updatedAt" => date("Y-m-d H:i:s")])){

            $_SESSION['message'] = "notifications marked as viewed";

            return header("Location: ?department+manager/show+notifications+tabs");
        }
    }    

    private function showCreateTaskForm() {
        $data = [];

        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.department_manager.create_task_form", compact("auth", "notificationsTabs", $data));
    }

    private function createTask(Request $request) {
        
        if($taskId = (new Tasks())->create($request)){

            $this->pushNotificationCreation("createTask", $taskId, $request->department);

            $_SESSION['message'] = "task has been successfully created!!!";

            return header("Location: ?department+manager/show+create+task+form");
        }
    }

    private function salesUsers() {
        $auth = (new Auth())->user();
        
        if(strcasecmp($auth->department, "sales") == 0){

            $data  = [];

            $usersObj = (new Users())->find()->where(["department" => "sales"])->fetchThisQuery();

            if(is_object($usersObj)){

                $users[] = $usersObj;
            }else{

                $users = $usersObj;
            }
            
            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.department_manager.sales_users", compact("roles", "users", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    private function productionUsers() {
        $auth = (new Auth())->user();
        
        if(strcasecmp($auth->department, "production") == 0){

            $data  = [];

            $usersObj = (new Users())->find()->where(["department" => "production"])->fetchThisQuery();

            if(is_object($usersObj)){

                $users[] = $usersObj;
            }else{

                $users = $usersObj;
            }
            
            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.department_manager.production_users", compact("roles", "users", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    private function showSalesAssignedTasks() {
        $auth = (new Auth())->user();

        if(strcasecmp($auth->department, "sales") == 0){

            $data  = [];

            $tasksObj = (new ATs())->find()->where(["assigneeDepartment"  => "sales"])->fetchThisQuery();

            if(is_object($tasksObj)){

                $tasks[] = $tasksObj;
            }else{

                $tasks = $tasksObj;
            }
            
            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.department_manager.sales_assigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    private function showProductionAssignedTasks() {
        $auth = (new Auth())->user();

        if(strcasecmp($auth->department, "production") == 0){

            $data  = [];

            $tasksObj = (new ATs())->find()->where(["assigneeDepartment"  => "production"])->fetchThisQuery();

            if(is_object($tasksObj)){

                $tasks[] = $tasksObj;
            }else{

                $tasks = $tasksObj;
            }
            
            $roles = (new Roles())->find()->fetchThisQuery();

            $notificationsTabs = $this->notificationsTabs;

            return $this->view("admin.department_manager.production_assigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
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

            return $this->view("admin.department_manager.sales_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
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

            return $this->view("admin.department_manager.production_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    private function cancelTask(int $taskId) {
        $auth = (new Auth())->user();

        $task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery();

        $taskRequest = new TRs();

        $sql = "SELECT * FROM " . $taskRequest->DBTABLE . " WHERE taskId = ? ORDER BY id DESC LIMIT 1";

        $taskRequestObj = $taskRequest->execute($sql, [$taskId])->fetchObject($taskRequest::class);

        if($taskRequestObj){

            if($taskRequestObj->status == "approved"){

                if($taskAssignment = (new ATs())->find()->where(["taskId" => $taskId])->fetchThisQuery()){
                
                    $taskAssignment->delete();

                    $task->save(["status" => "unassigned"]);

                    $this->pushNotificationCreation("cancelTask", $task->id, $task->department);

                    $_SESSION['message'] = "task assignment has been cancelled!!!";

                    return header("Location: ?department+manager/index");
                }

                $_SESSION['error'] = "sorry this task has not been assigned yet!!!";

                return header("Location: ?department+manager/index");
            }

            $_SESSION['error'] = "sorry your request has not been approved yet!!!";

            return header("Location: ?department+manager/index");
        }

        $taskRequestId = $taskRequest->create(
            [
                "taskId"          => $taskId,
                "requester"       => $auth->email,
                "description"     => "{$auth->email} has requested to cancel {$task->name} task assignment",
                "taskDepartment"  => $task->department,
            ]
        );

        $this->pushNotificationCreation("taskRequest", $task->id, $task->department);

        $_SESSION['message'] = "your request has been made and needs to be approved, before you can proceed!!!";

        return header("Location: ?department+manager/show+sales+assigned+tasks");
    }

    private function showAssignTaskForm(int $taskId) {
        $data = [];

        $auth = (new Auth())->user();

        $task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery();

        $users = [];

        foreach($this->users as $user){

            if($user->department == $task->department) {

                $users[] = $user;
            }
        }

        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.department_manager.assign_task_form", compact("auth", "users", "roles", "notificationsTabs", "task", $data));
    }

    private function assignTask(Request $request) {
        $taskAssignmentObj = (new ATs())->find()->where(["assignee" => $request->assignee])->fetchThisQuery();

        if(is_array($taskAssignmentObj)){

            $taskAssignments = $taskAssignmentObj;
        }else{

            $taskAssignments[] = $taskAssignmentObj;
        }

        if(count($taskAssignments) < 3){

            if($this->assignTaskLogic()){

                if($taskAssignmentId = (new ATs())->create($request)){

                    $task = (new Tasks())->find()->where(["id" => $request->taskId])->fetchThisQuery();

                    $task->save(["status" => "assigned"]);

                    $this->pushNotificationCreation("assignTask", $task->id, $request->assigneeDepartment, $request->assignee);

                    $_SESSION['message'] = "task has been assigned!!!";

                    return header("Location: ?department+manager/index");
                }
            }

            return $this->permissionRestricted();
        }else{

            $_SESSION['error'] = "sorry, can not assign more than 3 tasks to an assignee!!!";

            return header("Location: ?department+manager/index");
        }
    }

    private function assignTaskLogic(object $request, object $auth) {

        if($request->assigneeHierarchicalValue > $auth->hierarchicalValue) {

            return false;
        }

        if(($request->assigneeHierarchicalValue == $auth->hierarchicalValue) &&
            ($request->assignee != $auth->email)
        ){
            
            return false;
        }

        return true;
    }
} 
?>