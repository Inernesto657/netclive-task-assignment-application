<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php 

        if(isset($error)){
            echo "<h1> {$error} </h1>";
        }

        if(isset($message)){
            echo "<h1> {$message} </h1>";
        }        
    ?>
    <form action="?netclive/create+task/&id=3" method="post" enctype="multipart/form-data">
        <label for="name">Task Name: </label>
        <br>
        <input type="text" name="name" id="name" required>

        <br>
        <br>

        <label for="taskCartegory">Select Task Cartegory:</label>
        <br>
        <select name="taskCartegory" id="taskCartegory" required>
            <option value="department manager">Task For Department Manager</option>
            <option value="workers">Task For workers</option>
        </select>

        <br>
        <br>

        <label for="department">Task Department:</label>
        <br>
        <select name="department" id="department" required>
            <option value="sales">Sales</option>
            <option value="production">Production</option>
        </select>        
        
        <br>
        <br>

        <label for="description">Task Description:</label>
        <br>
        <textarea name="description" id="description" cols="30" rows="10" required></textarea>

        <br>
        <br>

        <input type="submit" value="Create Task">
    </form>
</body>
</html>