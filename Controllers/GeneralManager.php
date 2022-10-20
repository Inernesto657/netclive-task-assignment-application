<?php
namespace Controllers;
use Controllers\Netclive;
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
 * This Class handles functionalities for the general manager
 * and serves as the parent class for the Department Manager Class
 * Class GeneralManager
 * @package Controllers
 */
class GeneralManager extends Netclive {

    /**
     * stores the notifications yet to be seen
     * by the general manager
     * NB: this includes all notifications in the notifications DB
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
     * fetches all notifications that have not been seen by the
     * general manager from the notification DB
     * @return void
     */
    private function fetchNotificationsTabs() {
        $user = (new Auth())->user();

        $notificationsTabs = [];

        $notifications = (new Notifications());

        $sql = "SELECT * FROM " . $notifications->DBTABLE . " WHERE time >= ?";

        $notifications = $notifications->execute($sql, [$user->updatedAt])->fetchAll(PDO::FETCH_ASSOC);

        foreach($notifications as $notification){

            $notificationsTabs[] = (object) $notification;
        }

        $this->notificationsTabs = $notificationsTabs;
    }

    /**
     * displays general information about tasks assigned 
     * to logged-in user (i.e general manager)
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

        return $this->view("admin.general_manager.index", compact("notificationsTabs", "roles", "tasks", "auth", $data));
    }

    /**
     * updates the notifications once they have been marked as seen
     * by the logged-in user (general-manager)
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
            
            return header("Location: ?general+manager/index");
        }

        $_SESSION['error'] = "sorry this task was not found!!!";
            
        return header("Location: ?general+manager/index");
    }

    /**
     * displays all the users in the company
     * @return method (i.e the corresponding view)
     */
    private function allUsers() {
        $data  = [];

        $users = $this->users;

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.all_users", compact("roles", "users", "notificationsTabs", "auth", $data));
    }

    /**
     * displays the users (staffs) in the sales department
     * @return method (i.e the corressponding view)
     */
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

    /**
     * displays the users (staffs) in the production department
     * @return method (i.e the corressponding view)
     */
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

    /**
     * displays all the tasks created from the tasks DB
     * @return method (i.e the corressponding view)
     */
    private function allTasks() {
        $data  = [];

        $tasks = $this->tasks;

        $auth = (new Auth())->user();
        
        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.all_tasks", compact("roles", "tasks", "notificationsTabs", "auth", $data));
    }

    /**
     * displays all the assigned tasks from the assigned_tasks DB
     * @return method (i.e the corressponding view)
     */
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

    /**
     * displays the assigned tasks in the sales department
     * @return method (the corresponding view)
     */
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

    /**
     * displays the assigned tasks in the production department
     * @return method (the corresponding view)
     */
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

    /**
     * displays all the unassigned tasks from the assigned_tasks DB
     * @return method (i.e the corressponding view)
     */
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

    /**
     * displays the unassigned tasks in the sales department
     * @return method (the corresponding view)
     */
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

    /**
     * displays the unassigned tasks in the production department
     * @return method (the corresponding view)
     */
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

    /**
     * displays the form to create tasks
     * @return method (i.e the corresponding view)
     */
    private function showCreateTaskForm() {
        $data = [];

        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.create_task_form", compact("auth", "notificationsTabs", $data));
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

            return header("Location: ?general+manager/show+create+task+form");
        }
    }

    /**
     * displays the form to assign tasks
     * @return method (i.e the corresponding view)
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

        return $this->view("admin.general_manager.assign_task_form", compact("auth", "users", "roles", "notificationsTabs", "task", $data));
    }

    /**
     * creates the task assigment into the assigned_tasks DB once the 
     * form request has been made
     * @param object request (request from the form) 
     * @return function (i.e redirection back to allTasks method)
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

                return header("Location: ?general+manager/all+tasks");
            }
        }else{

            $_SESSION['error'] = "sorry, can not assign more than 3 tasks to an assignee!!!";

            return header("Location: ?general+manager/all+tasks");
        }
    }

    /**
     * cancels a task assignment already made
     * @param int taskId
     * @return function (redirection back to allTasks method)
     */
    private function cancelTask(int $taskId) {
        if($taskAssignment = (new ATs())->find()->where(["taskId" => $taskId])->fetchThisQuery()){
        
            $taskAssignment->delete();

            $task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery();

            $task->save(["status" => "unassigned"]);

            $this->pushNotificationCreation("cancelTask", $task->id, $task->department);

            $_SESSION['message'] = "task assignment has been cancelled!!!";

            return header("Location: ?general+manager/all+tasks");
        }

        $_SESSION['error'] = "sorry this task has not been assigned yet!!!";

        return header("Location: ?general+manager/all+tasks");
    }

    /**
     * deletes a task record that has already been created
     * @param int taskId
     * @return function (redirection back to allTasks method)
     */
    private function deleteTask(int $taskId) {
        if($task = (new Tasks())->find()->where(["id" => $taskId])->fetchThisQuery()){
        
            if($task->delete()){

                if($taskAssignment = (new ATs())->find()->where(["taskId" => $taskId])->fetchThisQuery()){

                    if($taskAssignment->delete()){

                        $_SESSION['message'] = "task has been deleted!!!";

                        return header("Location: ?general+manager/all+tasks");
                    }
                }

                $this->pushNotificationCreation("deleteTask", $task->id, $task->department);

                $_SESSION['message'] = "task has been deleted!!!";

                return header("Location: ?general+manager/all+tasks");
            }
        }

        $_SESSION['error'] = "sorry this task does not exist!!!";

        return header("Location: ?general+manager/all+tasks");
    }

    /**
     * displays all notifications that have 
     * not been seen by the general manager
     * @return method (i.e the corresponding view)
     */
    private function showNotificationsTabs() {
        $data  = [];
        
        $auth = (new Auth())->user();

        $roles = (new Roles())->find()->fetchThisQuery();

        $notificationsTabs = $this->notificationsTabs;

        return $this->view("admin.general_manager.notifications", compact("auth", "notificationsTabs", "roles", $data));
    }

    /**
     * updates the notifications once they have been marked
     * as seen by the logged-in user (i.e general manager)
     * @return fucntion (i.e redirection back to showNotificationsTabs method)
     */
    private function notificationViewUpdate() {
        date_default_timezone_set("America/New_York");

        if($userId = (new Auth())->user()->update(["updatedAt" => date("Y-m-d H:i:s")])){

            $_SESSION['message'] = "notifications marked as viewed";

            return header("Location: ?general+manager/show+notifications+tabs");
        }
    }

    /**
     * deletes all the notifications from the notifications DB
     * @return fucntion (i.e redirection back to showNotificationsTabs method)
     */
    private function deleteNotifications() {
        date_default_timezone_set("America/New_York");

        $notifications = (new Notifications());

        $sql = "DELETE FROM " . $notifications->DBTABLE;

        if($notificationsDeleted = $notifications->execute($sql)){

            (new Auth())->user()->update(["updatedAt" => date("Y-m-d H:i:s")]);

            $_SESSION['message'] = "notifications have been deleted!!!";

            return header("Location: ?general+manager/show+notifications+tabs");
        }
    }

    /**
     * displays all resquest made from the task_requests DB
     * @return method (i.e the corresponding view)
     */
    private function showRequests() {
        $data  = [];
        
        $auth = (new Auth())->user();

        $notificationsTabs = $this->notificationsTabs;

        $taskRequestsObj = (new TRs())->find()->fetchThisQuery();

        if(is_object($taskRequestsObj)){

            $taskRequests[] = $taskRequestsObj;
        }else{

            $taskRequests = $taskRequestsObj;
        }

        return $this->view("admin.general_manager.task_requests", compact("auth", "notificationsTabs", "taskRequests", $data));
    }

    /**
     * approves a task request that has been made
     * @param int taskRequestId
     * @return function (i.e redirection back to showRequests method)
     */
    private function approveTaskRequest(int $taskRequestId) {
        $taskId = $_GET['task_id'];


        if($taskRequest = (new TRs())->find()->where(
            [
                "id"     => $taskRequestId,
                "taskId" => $taskId
            ]
        )->fetchThisQuery()){

            $taskRequest->update(["status" => "approved"]);

            $this->pushNotificationCreation("approveRequest", $taskRequest->taskId, $taskRequest->taskDepartment);

            $_SESSION['message'] = "task request has been approved!!!";

            return header("Location: ?general+manager/show+requests");
        }
    }

    /**
     * unapproves a task request that has been made
     * @param int taskRequestId
     * @return function (i.e redirection back to showRequests method)
     */
    private function unapproveTaskRequest(int $taskRequestId) {
        $taskId = $_GET['task_id'];

        if($taskRequest = (new TRs())->find()->where(
            [
                "id"     => $taskRequestId,
                "taskId" => $taskId
            ]
        )->fetchThisQuery()){

            $taskRequest->update(["status" => "unapproved"]);

            $this->pushNotificationCreation("unapproveRequest", $taskRequest->taskId, $taskRequest->taskDepartment);

            $_SESSION['message'] = "task request has been unapproved!!!";

            return header("Location: ?general+manager/show+requests");
        }
    }
} 
?>