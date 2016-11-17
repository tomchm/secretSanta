<?php
session_start();
if($_SESSION['admin'] != TRUE){
    require_once "config.php";
    if(!is_null($_POST['passwordInput']) && $_POST['passwordInput'] == LOGIN){
        $_SESSION['admin'] = TRUE;
    }
}

if($_SESSION['admin'] == TRUE){
    header('Location: home.php');
}

?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link rel="stylesheet" href="css/skeleton.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <div class="container">
            <div class="row heading centered">
                <div class="offset-by-three six columns">
                    <h3>CRT Secret Santa</h3>
                    <h4>Admin Login</h4>
                </div>
            </div>
            <form action="index.php" method="post">
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="passwordInput">Password</label>
                        <input class="u-full-width" type="password" id="passwordInput" name="passwordInput">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <input class="button-primary" type="submit" value="Submit">
                    </div>
                </div>

            </form>


        </div>

        <script
                src="https://code.jquery.com/jquery-3.1.1.min.js"
                integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
                crossorigin="anonymous"></script>
        <script src="js/main.js"></script>
    </body>
</html>
