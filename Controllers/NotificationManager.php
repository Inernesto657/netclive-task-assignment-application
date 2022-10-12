<?php
namespace Controllers;
use Models\Notifications;

trait NotificationManager {

    public function __construct(){

    }

    /**
     * This magic custom method allows
     * decendants of this class to call
     * inaccessible methods of this class
     * @param $method (method name)
     * @param $args (arguments passed to the method, if any)
     * @return function (i.e the inaccessible method of this class)
     */
    public function __call($method, $args) {
        return call_user_func_array([$this, $method], $args);
    }

    public function getNotificationsTab() {
        if($user = (new Auth())->user()){

            switch($user->hierarchicalValue){
                case 1 || 2:
                    return $this->returnNotificationsTab($user);
                break;

                default:
                    $_SESSION['error'] = "Access Denied: You cannot process this action";
                    return header("Location: ?netclive/index/");
            }
        }

        $_SESSION['error'] = "Access Denied: You cannot process this action";
        return header("Location: ?netclive/index/");
    }

    private function returnNotificationsTab(object $user) {
        $notifications = (new Notifications())->find()->where(["user_id" => $user->id])->fetchThisQuery();
        $notificationsTab = [];

        if(is_object($notifications)){
            $notifications[] = $notifications;
        }

        foreach($notifications as $notification) {
            if(
                strtotime($notification->time) >= strtotime($user->updatedAt) &&
                $notification->userHierarchicalValue < $user->hierarchicalValue    
            ){
                $notificationsTab[] = $notification;
            }
        }

        return $this->view();
    }
}
?>