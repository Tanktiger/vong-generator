<?php
include_once "../config.php";
include_once "helper.php";

session_start();

//$allowed_hosts = array('vong-generator.de', 'localhost:9050', 'neu.vong-generator.de');
//if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
//    header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
//    exit;
//}

$helper = new ApiHelper();
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $link = mysqli_connect(Config::DB_DOMAIN, Config::DB_USER, Config::DB_PASSWORD, Config::DB_DATABASE);
    mysqli_set_charset($link, "utf8");
    /* check connection */
    if (mysqli_connect_errno()) {
        $helper->responseError("Could not connect to database", mysqli_error($link));
    }

    $result = mysqli_query($link, "SELECT * FROM user_text WHERE id=" . $_GET["id"]);

    if ($result === false) {
        mysqli_close($link);
        $helper->responseError("Could not save the Text", mysqli_error($link));
    }

    $response = $result->fetch_assoc();

    if ($result instanceof mysqli) {
        mysqli_free_result($result);
    }

    mysqli_close($link);

    $helper->response($response);

} else {
    $link = mysqli_connect(Config::DB_DOMAIN, Config::DB_USER, Config::DB_PASSWORD, Config::DB_DATABASE);
    mysqli_set_charset($link, "utf8");
    /* check connection */
    if (mysqli_connect_errno()) {
        $helper->responseError("Could not connect to database", mysqli_error($link));
    }

    $offest = (isset($_GET["offest"]) && is_numeric($_GET["offest"])) ? $_GET["offest"]: 0;
    $result = mysqli_query($link, "SELECT * FROM user_text ORDER BY tstamp_created DESC LIMIT $offest, 50");

    if ($result === false) {
        mysqli_close($link);
        $helper->responseError("Could not load texts", mysqli_error($link));
    }

    $response = array();
    while($row = $result->fetch_assoc()) {
        $response[] = $row;
    }

    if ($result instanceof mysqli) {
        mysqli_free_result($result);
    }

    mysqli_close($link);

    $helper->response($response);

}

