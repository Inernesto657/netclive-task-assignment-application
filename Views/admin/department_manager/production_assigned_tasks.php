<?php  require_once("header.php"); ?>

<body>
    <?php  require_once("side_nav.php"); ?>

    <div class="main">
        <div class="message <?php echo isset($data["message"]) ? "active" : ""; ?>">
            <p><?php echo isset($data["message"]) ? $data["message"] : ""; ?></p>
        </div>

        <div class="error <?php echo isset($data["error"]) ? "active" : ""; ?>">
            <p><?php echo isset($data["error"]) ? $data["error"] : ""; ?></p>
        </div>

        <section class="task-info">
            <h2>list of assigned tasks for production department </h2>

            <table class="table">
                <thead>
                    <tr>
                        <td>task id</td>
                        <td>task name</td>
                        <td>assignor</td>
                        <td>assignee</td>
                        <td>assignee role</td>
                        <td>assignee department</td>
                        <td>task description</td>
                        <td>Status</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if($tasks) : ?>
                        <?php foreach($tasks as $task) : ?>
                            <tr>
                                <td>
                                    <?php echo ucfirst($task->taskId); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($task->taskName); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($task->assignor); ?>
                                </td>
                                
                                <td>
                                    <?php echo ucfirst($task->assignee); ?>
                                </td>

                                <td>
                                    <?php foreach ($roles as $role): ?>

                                        <?php if($role->hierarchicalValue == $task->assigneeHierarchicalValue): ?>

                                            <?php echo ucfirst($role->name); ?>

                                            <?php break; ?>

                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($task->assigneeDepartment); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($task->taskDescription); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($task->status); ?>
                                </td>

                                <td>
                                    <a class="btn cancel-btn" href="/netclive-task-assignment-application/public/?department+manager/cancel+task/<?php echo $task->taskId; ?>">cancel</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>        
        </section>
    </div>
</body>

<?php  require_once("footer.php"); ?>