<?php
include_once "../gd-text/src/Box.php";
include_once "../gd-text/src/TextWrapping.php";
include_once "../gd-text/src/Color.php";
include_once "../gd-text/src/HorizontalAlignment.php";
include_once "../gd-text/src/VerticalAlignment.php";
include_once "../config.php";
include_once "helper.php";
include_once "../vongClass.php";
use GDText\Box;
use GDText\Color;

session_start();

//$allowed_hosts = array('vong-generator.de', 'localhost:9050', 'neu.vong-generator.de');
//if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
//    header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
//    exit;
//}
$helper = new ApiHelper();
$link = mysqli_connect(Config::DB_DOMAIN, Config::DB_USER, Config::DB_PASSWORD, Config::DB_DATABASE);
mysqli_set_charset($link, "utf8");
/* check connection */
if (mysqli_connect_errno()) {
    $helper->responseError("Could not connect to database", mysqli_error($link));
}



if (isset($_POST["text"]) && $_POST["text"] !== '') {
    $text =  strip_tags($_POST['text']);

    $vongClass = new Vong();
    $vong = $vongClass->vongarize($text);

    $now = date("Y-m-d H:i:s");
    $result = mysqli_query($link, "INSERT INTO user_text VALUES (null, null, 
    '".$now."',
    '".$_SERVER['REMOTE_ADDR']."',
    '".mysqli_real_escape_string($link, $text)."',
    '".mysqli_real_escape_string($link, $vong)."',
    0
    )");

    if ( $result === false ) {
        mysqli_close($link);
        $helper->responseError("Could not save the Text", mysqli_error($link));
    }

    if ($result instanceof mysqli) {
        mysqli_free_result($result);
    }

    $imageUrl = null;

    if (strlen($vong) < 800) {
        $im = imagecreatefromjpeg("../img/vong.jpg");

        $fs = 80;

        //Damit die schrift auch ins Bild passt, hier die schriftgröße je nach Zeichen reduzieren
        if (strlen($vong) > 100 && strlen($vong) < 200) {
            $fs = 60;
        } else if (strlen($vong) >= 200 && strlen($vong) < 300) {
            $fs = 40;
        } else if (strlen($vong) >= 300 && strlen($vong) < 500) {
            $fs = 20;
        } else if (strlen($vong) >= 500) {
            $fs = 10;
        }

        $box = new Box($im);
        $box->setFontFace(dirname(__DIR__).'/font/Vinegar-Regular.otf'); //@TODO: auf font von vong anpassen
        $box->setFontSize($fs);
        $box->setFontColor(new Color(0, 0, 0));
        $box->setTextShadow(new Color(0, 0, 0, 50), 0, 0);
        $box->setLineHeight(1.2);
        $box->setBox(20, 0, 900, 900);
        $box->setTextAlign('center', 'center');
        $box->draw(html_entity_decode($vong));


//        header("Content-type: image/png");
        $folder = "img/created/" . date("Y") . "/" . date("m") . "/" . date("d") . "/";

        if (!is_dir("../".$folder)) {
            mkdir("../".$folder, 0755, true);
        }

        $filename = md5(time() . mt_srand()) . '.png';
        $imageUrl= "http://".$_SERVER['HTTP_HOST']."/" . $folder .$filename;
        imagepng($im, "../".$folder . $filename);

        $lastinsertId = mysqli_insert_id($link);

        //save image
        $result = mysqli_query($link, "UPDATE user_text SET image='".$imageUrl."' WHERE id=" . $lastinsertId);

        mysqli_close($link);
    }

    $helper->response(array("vong" => $vong, "url" => $imageUrl));

} else {
    $helper->responseError("Not text provided");
}


