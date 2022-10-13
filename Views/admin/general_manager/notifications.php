<?php require_once("header.php"); ?>

<body>
    <?php  require_once("side_nav.php"); ?>

    <div class="main">
        <div class="message <?php echo isset($data["message"]) ? "active" : ""; ?>">
            <p><?php echo isset($data["message"]) ? $data["message"] : ""; ?></p>
        </div>

        <div class="error <?php echo isset($data["error"]) ? "active" : ""; ?>">
            <p><?php echo isset($data["error"]) ? $data["error"] : ""; ?></p>
        </div>

        <section class="notifications-tab">
            <h2>notifications</h2>

            <table class="table">
                <thead>
                    <tr>
                        <td>action</td>
                        <td>actor details</td>
                        <td>actor role</td>
                        <td>time</td>
                        <td><a href="/netclive-task-assignment-application/public/?general+manager/notification+view+update" class="btn btn-notification-tab <?php echo ($notificationsTabs) ? "active" : ""; ?>">mark as seen</a></td>
                        <td><a href="/netclive-task-assignment-application/public/?general+manager/delete+notifications" class="btn btn-delete-notification-tab <?php echo ($notificationsTabs) ? "active" : ""; ?>">delete all</a></td>
                    </tr>
                </thead>
                <tbody>
                    <?php if($notificationsTabs): ?>
                    
                        <?php foreach($notificationsTabs as $notificationsTab) : ?>
                            <tr>
                                <td>
                                    <?php echo ucfirst($notificationsTab->action); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($notificationsTab->actor); ?>
                                </td>

                                <td>
                                    <?php foreach($roles as $role): ?>

                                        <?php if($role->hierarchicalValue == $notificationsTab->actorHierarchicalValue): ?>

                                            <?php 
                                                echo ucfirst($role->name);
                                                break; 
                                            ?>

                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                </td>

                                <td>
                                    <?php echo $notificationsTab->time; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>        
        </section>
    </div>
</body>

<?php require_once("footer.php"); ?>