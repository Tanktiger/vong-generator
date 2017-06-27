<?php
include_once "config.php";
/**
 * @copyright  Tom Scheduikat, 2017
 */
session_start();
$username = "vong-admin";
$password = "8fa8c531630b11b2aec279b5935bd094";

$link = mysqli_connect(Config::DB_DOMAIN, Config::DB_USER, Config::DB_PASSWORD, Config::DB_DATABASE);
mysqli_set_charset($link, "utf8");

/* check connection */
if (mysqli_connect_errno()) {
//    printf("Connect failed: %s\n", mysqli_connect_error());
//    exit();
}
$enteredUsername = $enteredPW = null;
if (isset($_POST) && count($_POST) > 0) {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $enteredUsername = strip_tags($_POST["username"]);
        $enteredPW = strip_tags($_POST["password"]);

        if ($enteredUsername === $username && md5($enteredPW) === $password) {
            $_SESSION["user"] = array("username" => $username);
            header('Location: http://'.$_SERVER["HTTP_HOST"].'/backend.php', true, 303);
            die();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Vong Generator Admin - Login</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->

    <!-- Custom styles for this template -->
    <link href="/css/style.css" rel="stylesheet">

</head>

<body>

<div class="container theme-showcase" role="main">


    <div class="page-header">
        <h1>Vong-generator Admin</h1>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6 col-xs-12 center-row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    <?php if (null !== $enteredUsername && null !== $enteredPW) { ?>
                    <div class="alert alert-danger">
                        <b>Fehler:</b> Benutzername oder Passwort falsch!
                    </div>
                    <?php } ?>
                    <form action="login" method="POST">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Benutzername</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Benutzername">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Passwort</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
            </div>
        </div><!-- /.col-sm-4 -->
    </div>


</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/js/jquery-2.2.3.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
</body>
</html>

