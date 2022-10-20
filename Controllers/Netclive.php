<?php

namespace Controllers;
use Core\Controller;
use Models\Users;
use Models\Tasks;
use Core\Authentication as Auth;
use Models\Notifications;

/**
 * This Class handles general functionalities for the company staffs 
 * and serves as the parent class for the General Manager Class
 * Class Netclive
 * @package Controllers
 */
class Netclive extends Controller{

    /**
     * stores total number of users in the company
     * @var mixed users
     */
    protected $users = [];

    /**
     * stores total numnber of tasks assigned in the company
     * @var mixed tasks
     */
    protected $tasks = [];

    /**
     * custom action messages for each action made by the logged-in user
     * NB: this messages are edited accordingly and stored in the notifications DB
     * after the corresponding action has been made
     * @var array notificationMessage
     */
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

    /**
     * calls some methods of this class
     * when ever an object is being instantiated
     */
    public function __construct(){
        if((new Auth())->loggedIn()){

            $this->fetchAllUsers();

            $this->fetchAllTasks();
        }else{

            header("Location: /netclive-task-assignment-application/public/");
        }
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
     * checks the logged-in user's hierarchical value
     * (i.e general manager, department manager or worker) and
     * redirect's the logged-in user to the corresponding route/url
     * @return method
     */
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

    /**
     * redirect's to the general manager route/url
     * @return function (redirection to the corresponding route/url)
     */
    private function redirectToGeneralMangerRoute(){
        return header("Location: /netclive-task-assignment-application/public/?general+manager/index");
    }

    /**
     * redirect's to the department manager route/url
     * @return function (redirection to the corresponding route/url)
     */
    private function redirectToDepartmentManagerRoute(){
        return header("Location: /netclive-task-assignment-application/public/?department+manager/index");
    }

    /**
     * redirect's to the worker route/url
     * @return function (redirection to the corresponding route/url)
     */
    private function redirectToWorkerRoute(){
        return header("Location: /netclive-task-assignment-application/public/?worker/index");
    }

    /**
     * fetches all users from the database into the $users property
     * @return void
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
     * fetches all tasks from the database into the $tasks property
     * @return void
     */    
    private function fetchAllTasks() {
        $tasks = (new Tasks())->find()->fetchThisQuery();

        if(is_object($tasks)){

            $this->tasks[] = $tasks;
        }else{

            $this->tasks = $tasks;
        }
    }

    /**
     * denies access to restricted methods of this
     * class or child class, made by objects of this class,
     * or objects of child class
     * @return function (i.e redirection to the index method)
     */
    protected function permissionRestricted() {
        
        $_SESSION["error"] = "Access Denied: You cannot proceed with this action";

        return header("Location: ?netclive/index/");
    }

    /**
     * creates a new notification whenever an action is being
     * made by the logged-in user
     * @return method (this method creates the notification)
     */
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