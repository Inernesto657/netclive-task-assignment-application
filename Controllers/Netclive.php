<?php

namespace Controllers;
use Core\Controller;
use Models\Users;
use Models\Tasks;
use Core\Authentication as Auth;
use Models\Notifications;

class Netclive extends Controller{
    use TaskManager, NotificationManager;

    /**
     * Store total number of users in the company
     * @var $users
     */
    protected $users = [];

    /**
     * Store total numnber of tasks assigned in the company
     * @var $tasks
     */
    protected $tasks = [];

    protected $notificationMessage = [
        "createTask"        =>  "a task with id :id was created by :by",
        "assignTask"        =>  "a task with id :id was assigned by :by and has been assigned to :to",
        "taskRequest"       =>  "a request to cancel a task assignment with task id :id was made by :by",
        "cancelTask"        =>  "a task assignment with task id :id was cancelled by :by",
        "deleteTask"        =>  "a task with id :id was deleted by :by",
        "completeTask"      =>  "a task with id :id was completed by :by",
        "approveRequest"    =>  "a task request with task id :id was approved by :by",
        "unapproveRequest"  =>  "a task request with task id :id was unapproved by :by",
    ];

    public function __construct(){
        if((new Auth())->loggedIn()){

            $this->fetchAllUsers();

            $this->fetchAllTasks();
        }else{

            header("Location: /netclive-task-assignment-application/public/");
        }
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
    
    protected function index(){
        $user = (new Auth())->user();

        switch($user->hierarchicalValue) {
            case 1:
                return $this->redirectToGeneralMangerRoute();
            break;

            case 2:
                return $this->redirectToDepartmentManagerRoute();
            break;

            case 3:
                return $this->redirectToWorkerRoute();
            break;
        }
    }

    private function redirectToGeneralMangerRoute(){
        return header("Location: /netclive-task-assignment-application/public/?general+manager/index");
    }

    private function redirectToDepartmentManagerRoute(){
        return header("Location: /netclive-task-assignment-application/public/?department+manager/index");
    }

    private function redirectToWorkerRoute(){
        return header("Location: /netclive-task-assignment-application/public/?worker/index");
    }

    /**
     * Fetch all users from the database into the $users property
     */    
    private function fetchAllUsers() {
        $users = (new Users())->find()->fetchThisQuery();

        if(is_object($users)){

            $this->users[] = $users;
        }else{

            $this->users = $users;
        }
    }

    /**
     * Fetch all tasks from the database into the $tasks property
     */    
    private function fetchAllTasks() {
        $tasks = (new Tasks())->find()->fetchThisQuery();

        if(is_object($tasks)){

            $this->tasks[] = $tasks;
        }else{

            $this->tasks = $tasks;
        }
    }

    protected function permissionRestricted() {
        
        $_SESSION["error"] = "Access Denied: You cannot proceed with this action";

        return header("Location: ?netclive/index/");
    }

    protected function pushNotificationCreation(string $action, string $taskId, string $taskDepartment) {
        $actionMessage = $this->notificationMessage[$action] ?? "";

        $user = (new Auth())->user();

        $action = str_ireplace(":id", $taskId, $actionMessage);

        $action = str_ireplace(":by", $user->email, $action);

        if(func_num_args() > 3) {

            $action = str_ireplace(":to", func_get_arg(3), $action);
        }

        return (new Notifications())->save(
            [
                "action"      => $action,
                "department"  => $taskDepartment
            ]
        );
    }
}

?>