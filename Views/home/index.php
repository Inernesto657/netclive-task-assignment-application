<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netclive - Home</title>

    <!-- font awesome cdn css link-->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
    <link rel="stylesheet" href="/netclive-task-assignment-application/public/font-awesome/css/all.min.css">

    <!-- custom css link -->
    <link rel="stylesheet" href="/netclive-task-assignment-application/public/css/home.css">

</head>
<body>
    
    <!-- header section -->
    <header>
        <div id="menu-bar" class="fas fa-bars"></div>

        <a href="/netclive-task-assignment-application/public/" class="logo"><span>N</span>etclive</a>

        <nav class="navbar">
            <a href="/netclive-task-assignment-application/public/?netclive/index" 
                class=<?php echo $auth->loggedIn() ? "active" : ""; ?>
            >admin</a>
        </nav>

        <div class="icons" id="login-btn">
            login <i class="fas fa-user"></i>
        </div>
    </header>
    <!-- header section ends -->

    <!-- login form container -->
    <div class="login-form-container">
        <i class="fas fa-times" id="form-close"></i>

        <form action="/netclive-task-assignment-application/public/?login/facilitate" method="post">
            <h3>login</h3>
            <input type="email" name="email" class="box" placeholder="enter your company email">
            <input type="password" name="password" class="box" placeholder="enter your password">
            <input type="submit" value="click to enter your dashboard" class="btn">
        </form>
    </div>

    <section class="home" id="home">
        <div class="img-container">
            <img src="/netclive-task-assignment-application/public/images/home.jpg" alt="">
        </div>
    </section>


    <script src="/netclive-task-assignment-application/public/js/home.js"></script>
</body>
</html>