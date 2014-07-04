<?php
session_start();
$timeout = 10 * 60; // Set timeout minutes
$logout_redirect_url = "login.php"; // Set logout URL
if (isset($_POST['sessionVar'])) {
    if (isset($_SESSION['sessionVar'])) {
        $_SESSION = array();
        session_destroy();
        header("Location: $logout_redirect_url");
    } else {
        $_SESSION['sessionVar'] = $_POST['sessionVar'];
    }
}
/*
  if (isset($_SESSION['start_time'])) {
  $elapsed_time = time() - $_SESSION['start_time'];
  if ($elapsed_time >= $timeout) {
  $_SESSION = array();
  session_destroy();
  header("Location: $logout_redirect_url");
  }
  }
  $_SESSION['start_time'] = time();
 */
if (isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
}
if (!isset($_SESSION['username'])) {
    header("Location: $logout_redirect_url");
} else {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <title>My Apps</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
            <h1 style="
                text-align: center;
                font-family: sans-serif;
                text-transform: capitalize;
                background: aliceblue;
                padding: 10px;
                margin: 0 0 10px;
                ">Hello, <?php
                    echo $_SESSION['username']
                    ?></h1>
            <a href="notes" style="
               display: block;
               padding: 10px 0;
               font-size: 1.5em;
               font-family: sans-serif;
               font-weight: bold;
               background: #444;
               color: #fff;
               text-align: center;
               ">Use Notes App</a>
            <button onclick="logout()" style="
                    display: block;
                    text-align: center;
                    margin: 0 auto;
                    margin: 10px auto;
                    padding: 10px;
                    width: 200px;
                    font-size: 1.5em;
                    cursor: pointer;
                    ">Logout</button>
            <script>
                function logout() {
                    sessionStorage['sessionVar'] = '';
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function()
                    {
                        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                        {
                            window.location = 'login.php';
                        }
                    }
                    xmlhttp.open("POST", "apps.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send("logout=true");
                }
                function getSessionId() {
                    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                        return v.toString(16);
                    });
                }
                if (typeof sessionStorage['sessionVar'] === 'undefined' ||
                        sessionStorage['sessionVar'] === '') {
                    sessionStorage['sessionVar'] = getSessionId();
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                        }
                    }
                    xmlhttp.open("POST", "apps.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send("sessionVar=" + sessionStorage['sessionVar']);
                }
            </script>
        </body>
    </html>
    <?php
}