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

/**
 * This Class handles functionalities for the department manager
 * and serves as the parent class for the Worker Class
 * Class DepartmentManager
 * @package Controllers
 */
class DepartmentManager extends  GM{

    /**
     * stores the notifications yet to be seen
     * by the department manager
     * NB: this consists of only notifications perculiar
     * to the same department as the depeartment manager
     * @var mixed notificationsTabs
     */
    private $notificationsTabs = [];

    /**
     * calls parent constructors (if any)
     * and also some methods of this class
     * when ever an object is being instantiated
     */
    public function __construct() {
        parent::__construct();

        $this->fetchNotificationsTabs();
    }

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
     * fetches and sets notifications belonging to this department
     * and has not been seen by the department manager
     * @return void
     */
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

    /**
     * displays general information about task assignment to the logged-in user 
     * (i.e department manager)
     * @return method (i.e the corresponding view)
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

        return $this->view("admin.department_manager.index", compact("notificationsTabs", "roles", "tasks", "auth", $data));
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
            
            return header("Location: ?department+manager/index");
        }

        $_SESSION['error'] = "sorry this task was not found!!!";
            
        return header("Location: ?department+manager/index");
    }    

    /**
     * displays notifications belonging to this department
     * and has not been seen by the department manager
     * @return method (i.e the corresponding view)
     */
    private function showNotificationsTabs() {
        $data  = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.department_manager.notifications", compact("auth", "notificationsTabs", "roles", $data));
    }

    /**
     * updates the notifications once they have been marked
     * as seen by the logged-in user (i.e department manager)
     * @return fucntion (i.e redirection back to showNotificationsTabs method)
     */
    private function notificationViewUpdate() {
        date_default_timezone_set("America/New_York");

        if($userId = (new Auth())->user()->update(["updatedAt" => date("Y-m-d H:i:s")])){

            $_SESSION['message'] = "notifications marked as viewed";

            return header("Location: ?department+manager/show+notifications+tabs");
        }
    }    

    /**
     * displays the form to create tasks
     * @return method (i.e the corresponding view)
     */
    private function showCreateTaskForm() {
        $data = [];

        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.department_manager.create_task_form", compact("auth", "notificationsTabs", $data));
    }

    /**
     * creates the task into the Tasks DB once the 
     * form request has been made
     * @param object request
     * @return function (i.e redirection back to showCreateTaskForm method)
     */
    private function createTask(Request $request) {
        
        if($taskId = (new Tasks())->create($request)){

            $this->pushNotificationCreation("createTask", $taskId, $request->department);

            $_SESSION['message'] = "task has been successfully created!!!";

            return header("Location: ?department+manager/show+create+task+form");
        }
    }

    /**
     * displays the users (staffs) in the sales department
     * NB: when the logged-in user (department manager) does
     * not belong to the sales department, 
     * a permissionRestricted method is called
     * @return method (i.e the corressponding view)
     */
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

    /**
     * displays the users (staffs) in the production department
     * NB: when the logged-in user (department manager) does
     * not belong to the production department, 
     * a permissionRestricted method is called
     * @return method (i.e the corresponding view)
     */
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

    /**
     * displays the assigned tasks in the sales department
     * NB: when the logged-in user (department manager) does
     * not belong to the sales department, 
     * a permissionRestricted method is called
     * @return method (the corresponding view)
     */
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

    /**
     * displays the assigned tasks in the production department
     * NB: when the logged-in user (department manager) does
     * not belong to the production department, 
     * a permissionRestricted method is called
     * @return method (the corresponding view)
     */
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

    /**
     * displays the unassigned tasks in the sales department
     * NB: when the logged-in user (department manager) does
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

            return $this->view("admin.department_manager.sales_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    /**
     * displays the assigned tasks in the production department
     * NB: when the logged-in user (department manager) does
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

            return $this->view("admin.department_manager.production_unassigned_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
        }

        return $this->permissionRestricted();
    }

    /**
     * cancels a task assignment already made.
     * NB: initialing creates a request in the task_requests DB
     * and if request for the said already exists, it has to be
     * approved before proceeding to cancel task assignment
     * @param int taskId
     * @return function (redirection back to index method)
     */
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

        return header("Location: ?department+manager/index");
    }

    /**
     * displays the task assignment form
     * @param int taskId
     * @return method (the corresponding view)
     */
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

            if($this->assignTaskLogic($request)){

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

    /**
     * makes the logic for task assignment. 
     * NB: if logged-in user (department manager) assigns task to a superior or peer,
     * the logic returns false, else, it returns true
     * @param object request (request from the form)
     * @return boolean
     */
    private function assignTaskLogic(object $request) {
        $auth = (new Auth())->user();

        if($request->assigneeHierarchicalValue < $auth->hierarchicalValue) {

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