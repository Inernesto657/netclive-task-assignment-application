<?php
namespace Controllers;
use Core\Request;
use Models\Users;

/**
 * This Class handles login functionalities
 * Class Login
 * @package Controllers
 */
class Login {

    /**
     * handles login attempt made by the admin-login request form
     * @return function (i.e redirection to either netclive->index() method
     *                      or home->index() method, depending on the outcome
     *                      of the login attempt)
     */
    public function facilitate(Request $request) {
        $user = (new Users)->find()->where([
            "email" => $request->email,
            "password" => $request->password
        ])->fetchThisQuery();

        if($user) {
            $_SESSION["user_id"] = $user->id;

            return header("Location: /netclive-task-assignment-application/public/?netclive/index");
        }

        return header("Location: /netclive-task-assignment-application/public/");
    }

    /**
     * logs the current user out of the admin section
     * of the application
     * @return function (i.e redirection to home->index() method)
     */
    public function cancel(){
        if(isset($_SESSION['user_id'])){
            unset($_SESSION['user_id']);
        }

        header("Location: /netclive-task-assignment-application/public/");
    }
}
?>