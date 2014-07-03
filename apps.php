<?php
session_start();
$timeout = 10 * 60; // Set timeout minutes
$logout_redirect_url = "login.php"; // Set logout URL
if (isset($_SESSION['start_time'])) {
    $elapsed_time = time() - $_SESSION['start_time'];
    if ($elapsed_time >= $timeout) {
        $_SESSION = array();
        session_destroy();
        header("Location: $logout_redirect_url");
    }
}
$_SESSION['start_time'] = time();

if (isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: $logout_redirect_url");
}
if (!isset($_SESSION['username'])) {
    header("Location: $logout_redirect_url");
} else {
    ?>
    <!DOCTYPE html>
    <!--
    To change this license header, choose License Headers in Project Properties.
    To change this template file, choose Tools | Templates
    and open the template in the editor.
    -->
    <html>
        <head>
            <title>TODO supply a title</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="jquery-1.11.0.min.js"></script>
        </head>
        <body>
            <div>TODO write content</div>
            <h1>Hello <?php
                echo $_SESSION['username']
                ?></h1>
            <p>Your id is <?php echo $_SESSION['id']; ?></p>
            <form action="apps.php" method="POST">
                <input type="submit" value="logout" name="logout" />
            </form>
        </body>
    </html>
    <?php
}