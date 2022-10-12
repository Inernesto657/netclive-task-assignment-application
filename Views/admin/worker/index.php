<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Worker</title>
</head>
<body>
    
    <div class="tasks-list" id="tasks-list">
    <h2>List of Tasks</h2>
        <table>
            <thead>
                <tr>
                    <td>Name</td>
                    <td>Task Cartegory</td>
                    <td>Department</td>
                    <td>Description</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task) : ?>
                    <tr>
                        <td>
                            <?php echo ucfirst($task->name); ?>
                        </td>

                        <td>
                            <?php echo ucfirst($task->taskCartegory); ?>
                        </td>

                        <td>
                            <?php echo ucfirst($task->department); ?>
                        </td>

                        <td>
                            <?php echo ucfirst($task->description); ?>
                        </td>

                        <td>
                            <?php echo ucfirst($task->status); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>        
    </div>
</body>
</html>