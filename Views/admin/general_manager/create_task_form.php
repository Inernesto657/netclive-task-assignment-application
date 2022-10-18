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

        <section class="task-create-form">
            <h2>create new task</h2>

            <form action="/netclive-task-assignment-application/public/?general+manager/create+task" method="post">
                <label for="name">name:</label>
                <input type="text" name="name" required>

                <label for="taskCartegory">select role category:</label>
                <select name="taskCartegory" id="" required>
                    <optgroup label="--select a role for this task--">
                        <option value="">--select from list--</option>
                        <option value="general manager">general manager</option>
                        <option value="department manager">department manager</option>
                        <option value="worker">worker</option>
                    </optgroup>
                </select>

                <label for="taskCartegoryHierarchicalValue">select hierarchical value:</label>
                <select name="taskCartegoryHierarchicalValue" id="" required>
                    <optgroup label="--select a role for this task--">
                        <option value="">--select from list--</option>
                        <option value="1">select this option for the general manager</option>
                        <option value="2">select this option for department managers</option>
                        <option value="3">select this option for workers</option>
                    </optgroup>
                </select>

                <label for="department">select department:</label>
                <select name="department" id="" required>
                    <optgroup label="--select task department--">
                        <option value="">--select from list--</option>
                        <option value="nil">none</option>
                        <option value="sales">sales</option>
                        <option value="production">production</option>
                    </optgroup>
                </select>

                <label for="description">description:</label>
                <textarea name="description" id="" cols="30" rows="10"></textarea>

                <div class="submit-box">
                    <input type="submit" class="btn" value="create task">
                </div>
            </form>
        </section>
    </div>
</body>

<?php  require_once("footer.php"); ?>