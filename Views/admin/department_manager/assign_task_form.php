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

        <section class="task-assign-form">
            <h2>assign task</h2>

            <form action="/netclive-task-assignment-application/public/?department+manager/assign+task" method="post">
                <input type="hidden" name="taskId" required readonly value="<?php echo $task->id; ?>">
                <input type="hidden" name="assignor" required readonly value="<?php echo $auth->firstName; ?>">

                <label for="taskName">name:</label>
                <input type="text" name="taskName" required readonly value="<?php echo $task->name; ?>">

                <label for="assignee">assign task to:</label>
                <select name="assignee" id="assignee" required>
                    <optgroup label="--assign task to--">
                        <option value="">--select from list--</option>
                        <?php foreach($users as $user): ?>

                            <?php foreach($roles as $role): ?>

                                <?php if($user->hierarchicalValue == $role->hierarchicalValue): ?>                                
                        
                                    <option value="<?php echo $user->email; ?>"><?php echo $user->firstName . " @" . $user->email . " -- " . $role->name . " -- " . $user->department; ?></option>

                                    <?php break; ?>
                                    
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </optgroup>
                </select>

                <label for="assigneeHierarchicalValue">select role category:</label>
                <select name="assigneeHierarchicalValue" id="assigneeHierarchicalValue" required readonly>
                    <option value="<?php echo $task->taskCartegoryHierarchicalValue; ?>"><?php echo $task->taskCartegory?></option>
                </select>

                <label for="assigneeDepartment">select department:</label>
                <select name="assigneeDepartment" id="assigneeDepartment" required readonly>
                    <option value="<?php echo $task->department; ?>"><?php echo $task->department; ?></option>
                </select>

                <label for="taskDescription">description:</label>
                <textarea name="taskDescription" id="taskDescription" cols="30" rows="10" required readonly><?php echo str_replace("\r\n", "", $task->description); ?></textarea>

                <div class="submit-box">
                    <input type="submit" class="btn" value="assign task">
                </div>
            </form>
        </section>
    </div>
</body>

<?php  require_once("footer.php"); ?>