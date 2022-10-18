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
            <h2>list of Requests</h2>

            <table class="table">
                <thead>
                    <tr>
                        <td>task id</td>
                        <td>description</td>
                        <td>department</td>
                        <td>status</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if($taskRequests) : ?>
                        <?php foreach($taskRequests as $taskRequest) : ?>
                            <tr>
                                <td>
                                    <?php echo ucfirst($taskRequest->taskId); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($taskRequest->description); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($taskRequest->taskDepartment); ?>
                                </td>                                

                                <td>
                                    <?php echo ucfirst($taskRequest->status); ?>
                                </td>

                                <td>
                                    <a class="btn assign-btn" href="/netclive-task-assignment-application/public/?general+manager/approve+task+request/<?php echo $taskRequest->id; ?>&task_id=<?php echo $taskRequest->taskId; ?>">approve</a>
                                </td>

                                <td>
                                    <a class="btn cancel-btn" href="/netclive-task-assignment-application/public/?general+manager/unapprove+task+request/<?php echo $taskRequest->id; ?>&task_id=<?php echo $taskRequest->taskId; ?>">unapprove</a>
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