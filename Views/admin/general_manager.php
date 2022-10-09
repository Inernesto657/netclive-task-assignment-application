<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - General Manager</title>

    <!-- font awesome cdn css link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css link -->
    <link rel="stylesheet" href="/netclive-task-assignment-application/public/css/admin.css">    
</head>
<body>
    <div class="sidebar" id="sidebar">
        <a href="#" class="logo"><span>N</span>etclive</a>

        <nav class="navbar">
            <a href="#">new task</a>
            <a href="#">all users</a>
            <a href="#">all task</a>
            <a href="#" id="sales-nav">sales department</a>
            <a href="#" class="sub-nav" id="sales-sub-nav">users</a>
            <a href="#" class="sub-nav" id="sales-sub-nav">assigned task</a>
            <a href="#" class="sub-nav" id="sales-sub-nav">unassigned task</a>
            <a href="#" id="production-nav">production department</a>
            <a href="#" class="sub-nav" id="production-sub-nav">users</a>
            <a href="#" class="sub-nav" id="production-sub-nav">assigned task</a>
            <a href="#" class="sub-nav" id="production-sub-nav">unassigned task</a>
        </nav>
    </div>

    <header>
        <h2>welcome ernest</h2>
        <div class="icons">
            <a href="#"><i class="fas fa-sign-out" id="log-out-btn"></i> log out</a>
        </div>
    </header>

    <div class="main">
        <section class="task-info">
            <h2>List of Tasks</h2>

            <table class="table">
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
        </section>
    </div>

    <script src="/netclive-task-assignment-application/public/js/admin.js"></script>
</body>
</html>