<?php

namespace Controllers;
use Core\Controller;
use Models\Users;
use Models\Tasks;
use Core\Authentication as Auth;

class Netclive extends Controller{
    use TaskManagement;

    /**
     * Store total number of users in the company
     * @var array $users
     */
    private array $users = [];

    /**
     * Store total numnber of tasks assigned in the company
     * @var array $taskTotal
     */
    private array $tasks = [];


    public function __construct(){
        if((new Auth())->loggedIn()){
        
            parent::__construct();

            $this->auth();

            $this->fetchAllUsers();

            $this->fetchAllTasks();
        }

        return header("Location: /netclive-task-assignment-application/public/");
    }
    
    public function index(){
        $user = (new Auth())->user();

        switch($user->hierarchicalValue) {
            case "1":
                return $this->redirectToGeneralMangerRoute();
            break;

            case "2":
                return $this->redirectToDepartmentManagerRoute();
            break;

            case "3":
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
    private function fetchAllUsers(){
        $users = (new Users())->find()->fetchThisQuery();

        foreach($users as $user){
            $this->users[] = $user;
        }
    }

    /**
     * Fetch all tasks from the database into the $tasks property
     */    
    private function fetchAllTasks(){
        $tasks = (new Tasks())->find()->fetchThisQuery();

        if(is_array($tasks)){

            foreach($tasks as $task){
                $this->tasks[] = $task;
            }
        }else{

            $this->tasks[] = $tasks;
        }
    }
}

?>