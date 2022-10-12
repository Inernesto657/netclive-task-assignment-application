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
        
        <a href="/netclive-task-assignment-application/public/" class="logo"><span>N</span>etclive</a>

        <div class="dashboard-info">
            <p>general manager</p>
        </div>

        <nav class="navbar">
            <div class="notification">
                <a href="#" >notifications</a>
                <div class="icon"></div>
            </div>
            <a href="/netclive-task-assignment-application/public/?netclive/showCreateTaskForm">create task</a>
            <a href="#">Assign Task</a>
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
            <a href="/netclive-task-assignment-application/public/?login/cancel"><i class="fas fa-sign-out" id="log-out-btn"></i> log out</a>
        </div>
    </header>

    <div class="main">
        <section class="task-create-form">
            <h2>add new task</h2>

            <form action="#" method="post">
                <label for="name">name:</label>
                <input type="text" name="name" required>

                <label for="taskCartegory">select role category:</label>
                <select name="taskCartegory" id="" required>
                    <option value="department manager">department manager</option>
                    <option value="worker">worker</option>
                </select>

                <label for="department">select department:</label>
                <select name="department" id="" required>
                    <option value="sales">sales</option>
                    <option value="production">production</option>
                </select>

                <label for="description">description:</label>
                <textarea name="" id="" cols="30" rows="10"></textarea>

                <div class="submit-box">
                    <input type="submit" class="btn" value="create task">
                </div>
            </form>
        </section>
    </div>

    <script src="/netclive-task-assignment-application/public/js/admin.js"></script>
</body>
</html>