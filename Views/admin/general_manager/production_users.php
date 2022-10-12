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

        <section class="task-info">
            <h2>list of users</h2>

            <table class="table">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>First Name</td>
                        <td>Last Name</td>
                        <td>Role</td>
                        <td>Department</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if($users) : ?>
                        <?php foreach($users as $user) : ?>
                            <tr>
                                <td>
                                    <?php echo ucfirst($user->id); ?>
                                </td>
                        
                                <td>
                                    <?php echo ucfirst($user->firstName); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($user->lastName); ?>
                                </td>

                                <td>
                                    <?php foreach ($roles as $role): ?>

                                        <?php if($user->hierarchicalValue == $role->hierarchicalValue): ?>

                                            <?php echo ucfirst($role->name); ?>

                                            <?php break; ?>

                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($user->department); ?>
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