<?php
namespace Controllers;
use Core\Request;
use Models\Users;

class Login {

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

    public function cancel(){
        if(isset($_SESSION['user_id'])){
            unset($_SESSION['user_id']);
        }

        header("Location: /netclive-task-assignment-application/public/");
    }
}
?>