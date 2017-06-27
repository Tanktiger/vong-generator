<?php
include_once "config.php";
include_once "vongClass.php";
/**
 * @copyright  Tom Scheduikat, 2017
 */
session_start();

$link = mysqli_connect(Config::DB_DOMAIN, Config::DB_USER, Config::DB_PASSWORD, Config::DB_DATABASE);
mysqli_set_charset($link, "utf8");

/* check connection */
if (mysqli_connect_errno()) {
//    printf("Connect failed: %s\n", mysqli_connect_error());
//    exit();
}

if (!isset($_SESSION["user"])) {
    header('Location: http://'.$_SERVER["HTTP_HOST"].'/login.php', true, 303);
    die();
}
$action = null;
$editPost = null;
if (isset($_GET["action"]) && $_GET["action"] !== '') {
    $action = $_GET["action"];
    switch ($action) {
        case "new":
            if (isset($_POST) && count($_POST) > 0 && isset($_POST["title"]) && isset($_POST["text"])) {
                $file = null;
                if (isset($_FILES) && isset($_FILES["file"]) && isset($_FILES["file"]["name"]) && $_FILES["file"]["name"] !== '') {
                    $uploaddir = realpath(__DIR__ . '/img/posts/');
                    $filename = basename($_FILES['file']['name']);
                    $uploadfile = $uploaddir . '/' . $filename;
                    $file = "http://".$_SERVER["HTTP_HOST"].'/img/posts/' . $filename;

                    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
//                        echo "Datei ist valide und wurde erfolgreich hochgeladen.\n";
                    } else {
//                        echo "Möglicherweise eine Dateiupload-Attacke!\n";
//                        var_dump($_FILES);
//                        var_dump($_POST);
//                        exit(PHP_EOL . __FILE__ . ' on Line: ' . __LINE__ . ' in Function: ' . __FUNCTION__);
                    }
                }
                $vongClass = new Vong();

                $title = $vongClass->vongarize(strip_tags($_POST["title"]));
                $text = $_POST["text"];

                $vong = $vongClass->vongarize($text);

                $insertNewPost = mysqli_query($link, "INSERT INTO posts VALUES(null, 
                    '".mysqli_real_escape_string($link, $title)."',
                    '".mysqli_real_escape_string($link, $text)."',
                    '".mysqli_real_escape_string($link, $vong)."',
                    '".$file."',
                    '".time()."'
                )");

                if ($insertNewPost === false) {
//                    var_dump($_FILES);
//                    var_dump($_POST);
//                    var_dump(mysqli_error($link));
//                    exit(PHP_EOL . __FILE__ . ' on Line: ' . __LINE__ . ' in Function: ' . __FUNCTION__);
                }
            }
            break;
        case "edit":
            $editPostQ = mysqli_query($link, "SELECT * FROM posts WHERE id=" . $_GET["id"]);
            if ($editPostQ) {
                $editPost = mysqli_fetch_assoc($editPostQ);
            }
            break;
        case "update":
            if (isset($_POST) && isset($_POST["title"]) && isset($_POST["text"]) && isset($_POST["id"])) {
                $file = null;

                if (isset($_FILES) && isset($_FILES["file"]) && isset($_FILES["file"]["name"]) && $_FILES["file"]["name"] !== '') {
                    $uploaddir = realpath(__DIR__ . '/img/posts/');
                    $filename = basename($_FILES['file']['name']);
                    $uploadfile = $uploaddir . $filename;
                    $file = "http://".$_SERVER["HTTP_HOST"].'/img/posts/' . $filename;

                    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                    } else {
//                        var_dump($_FILES);
//                        var_dump($_POST);
//                        exit(PHP_EOL . __FILE__ . ' on Line: ' . __LINE__ . ' in Function: ' . __FUNCTION__);
                    }
                }
                $vongClass = new Vong();

                $title = $vongClass->vongarize(strip_tags($_POST["title"]));
                $text = $_POST["text"];

                $vong = $vongClass->vongarize($text);

                $editPost = mysqli_query($link, "UPDATE posts SET 
                    title = '".mysqli_real_escape_string($link, $title)."',
                    text = '".mysqli_real_escape_string($link, $text)."',
                    vong = '".mysqli_real_escape_string($link, $vong)."',
                    file = '".$file."',
                    '".time()."'
                    WHERE id = ".$_POST["id"]."
                ");

                if ($editPost === false) {
//                    var_dump($_FILES);
//                    var_dump($_POST);
//                    var_dump(mysqli_error($link));
//                    exit(PHP_EOL . __FILE__ . ' on Line: ' . __LINE__ . ' in Function: ' . __FUNCTION__);
                }
            }
            break;
        case "delete":
            if (isset($_GET["id"])) {
                $deletePostQ = mysqli_query($link, "DELETE FROM posts WHERE id=" . $_GET["id"]);
                if ($deletePostQ) {
                }
            }

            break;
        case "logout":
            $_SESSION["user"] = null;
            header('Location: http://'.$_SERVER["HTTP_HOST"].'/login.php', true, 303);
            die();
            break;
    }
}

if ($action !== "edit") {
    $posts = mysqli_query($link, "SELECT * FROM posts LIMIT 1000");
    if ( $posts === false ) {
        var_dump($_FILES);
        var_dump($_POST);
        var_dump(mysqli_error($link));
        exit(PHP_EOL . __FILE__ . ' on Line: ' . __LINE__ . ' in Function: ' . __FUNCTION__);
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
    <link rel="icon" href="/favicon.ico">

    <title>Vong Generator Admin - Backend</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->

    <!-- Custom styles for this template -->
    <link href="/css/style.css" rel="stylesheet">

</head>

<body>

<div class="container theme-showcase" role="main">

    <div class="row">
        <div class="col-sm-10 col-md-10 col-xs-12">
            <div class="page-header">
                <h1>Vong-generator Admin</h1>
            </div>
        </div>
        <div class="col-sm-2 col-md-2 col-xs-12 text-right">
            <br>
            <a href="backend.php?action=logout" class="btn btn-danger ">Logout</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-xs-12">
            <?php if ($action !== "edit") { ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Neuen Beitrag erstellen</h3>
                    </div>
                    <div class="panel-body">
                        <form action="backend.php?action=new" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Titel des Beitrags</label>
                                <input type="text" id="title" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="text">Beitrag</label>
                                <textarea id="text" name="text" class="form-control" rows="12" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="file">Beitragsbild</label>
                                <input type="file" id="file" name="file">
                                <p class="help-block">Hilfetext hier</p>
                            </div>
                            <button type="submit" class="btn btn-default">Speichern</button>
                        </form>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Beiträge</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Titel</th>
                                <th>Erstelldatum</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($post = mysqli_fetch_assoc($posts)) { ?>
                                <tr>
                                    <th scope="row"><?php echo $post["id"]; ?></th>
                                    <td><?php echo $post["title"]; ?></td>
                                    <td><?php echo date("d.m.Y H:i:s", $post["tstamp"]); ?></td>
                                    <td>
                                        <a href="/backend.php?action=edit&id=<?php echo $post["id"]; ?>" class="btn btn-info">Editieren</a>
                                        <a href="/backend.php?action=delete&id=<?php echo $post["id"]; ?>" class="btn btn-danger">Löschen</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php } else if (null !== $editPost) { ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Beitrag editieren</h3>
                    </div>
                    <div class="panel-body">
                        <form action="backend.php?action=update" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $editPost["id"]; ?>">
                            <div class="form-group">
                                <label for="title">Titel des Beitrags</label>
                                <input type="text" id="title" class="form-control" name="title" required value="<?php echo $editPost["title"]; ?>">
                            </div>
                            <div class="form-group">
                                <label for="text">Beitrag</label>
                                <textarea id="text" name="text" class="form-control" rows="12" required>
                                    <?php echo $editPost["text"]; ?>
                                </textarea>
                            </div>
                            <div class="form-group">
                                <label for="file">Beitragsbild</label>
                                <input type="file" id="file" name="file">
                                <p class="help-block">aktuelles Bild: <?php echo isset($editPost["file"]) ? $editPost["file"]: "Keines vorhanden"; ?></p>
                            </div>
                            <button type="submit" class="btn btn-default">Speichern</button>
                        </form>
                    </div>
                </div>
            <?php } ?>

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

