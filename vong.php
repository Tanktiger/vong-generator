<?php
include_once "gd-text/src/Box.php";
include_once "gd-text/src/TextWrapping.php";
include_once "gd-text/src/Color.php";
include_once "gd-text/src/HorizontalAlignment.php";
include_once "gd-text/src/VerticalAlignment.php";
use GDText\Box;
use GDText\Color;

session_start();

$allowed_hosts = array('vong-generator.de', 'localhost:9050');
if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
    exit;
}

//    $link = mysqli_connect("127.0.0.1", "vongdb", "&D2o5xd8", "vong");
$link = mysqli_connect("127.0.0.1", "root", "", "vong");
/* check connection */
if (mysqli_connect_errno()) {
    echo json_encode(array("vong" => "Sorry, da ist mir ein Fehler unterlaufen! Bitte probiere es erneut!"));
    exit();
}

if (isset($_POST["text"]) && $_POST["text"] !== '') {
    $text =  strip_tags($_POST['text']);

    $vong = vongarize($text);

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
        echo json_encode(array("vong" => "Sorry, da ist mir ein Fehler unterlaufen! Bitte probiere es erneut!"));
        exit();
    }

    if ($result instanceof mysqli) {
        mysqli_free_result($result);
    }

    $im = imagecreatefromjpeg("img/vong.jpg");

    $fs = 80;

    if (strlen($vong) > 150 && strlen($vong) < 250) {
        $fs = 60;
    } else if (strlen($vong) >= 250 && strlen($vong) < 350) {
        $fs = 40;
    } else if (strlen($vong) >= 350 && strlen($vong) < 500) {
        $fs = 20;
    }else if (strlen($vong) >= 500) {
        $fs = 10;
    }

    $box = new Box($im);
    $box->setFontFace(__DIR__.'/font/Vinegar-Regular.otf'); // http://www.dafont.com/pacifico.font
    $box->setFontSize($fs);
    $box->setFontColor(new Color(0, 0, 0));
    $box->setTextShadow(new Color(0, 0, 0, 50), 0, 0);
    $box->setLineHeight(1.2);
    $box->setBox(20, 0, 900, 900);
    $box->setTextAlign('center', 'center');
    $box->draw(html_entity_decode($vong));


//        header("Content-type: image/png");
    $filename = time() . '.png';
    $imageUrl= "http://".$_SERVER['HTTP_HOST']."/img/created/".$filename;
    imagepng($im, "img/created/" . $filename);

    $lastinsertId = mysqli_insert_id($link);

    //save image
    $result = mysqli_query($link, "UPDATE user_text SET image='".$imageUrl."' WHERE id=" . $lastinsertId);

    mysqli_close($link);

    header("Content-type: application/json; charset=utf-8");

    echo json_encode(array("vong" => $vong, "url" => $imageUrl));
    exit();

} else if (isset($_POST["like"]) && isset($_POST["id"]) && $_POST["like"] && $_POST["id"] !== '') {
    $result = mysqli_query($link, "UPDATE user_text SET likes= likes + 1 WHERE id=" . mysqli_real_escape_string($link, $_POST["id"]));

    if ( $result === false ) {
        mysqli_close($link);
        echo json_encode(array("vong" => "Sorry, da ist mir ein Fehler unterlaufen! Bitte probiere es erneut!"));
        exit();
    }

    if ($result instanceof mysqli) {
        mysqli_free_result($result);
    }

    mysqli_close($link);
    if (!isset($_SESSION["likes"])) $_SESSION["likes"] = array();

    array_push($_SESSION["likes"], $_POST["id"]);

    header("Content-type: application/json; charset=utf-8");

    echo json_encode(array("success" => true));
    exit();
}

mysqli_close($link);

header("Content-type: application/json; charset=utf-8");
echo json_encode(array("vong" =>  "Sorry, da ist mir ein Fehler unterlaufen! Bitte probiere es erneut!"));
exit();

function vongarize($text) {

    //2 arrays, im ersten sind alle Wörter - im 2 sind alle umgewandelten Wörter
    $words = explode(" ", $text);
    $newWords = array();

    foreach ($words as $key => $word) {
        //check for line breaks
        if(FALSE !== strpos($word, "#nl#")) {
            $newWordParts = array();
            $wordParts = explode("#nl#", $word);

            foreach ($wordParts as $index => $wordPart) {
                $newWordParts[$index] = vongarizeWord($wordPart);
            }

            $newWords[$key] = implode(PHP_EOL, $newWordParts);
        } else {
            $newWords[$key] = vongarizeWord($word);
        }

    }

    return implode(" ", $newWords);
}

function vongarizeWord($word) {

    if ($word == '') return $word;

    $editComplete = false;
    $newWord = '';
    $specialChars = array("!", '"', "'", "?", ".", ",", ":", ";", "-", "´", "`", ")", "(");

    $lastChar = substr($word, -1);
    $firstChar = $word[0];

    if (in_array($firstChar, $specialChars)) {
        $word = str_replace($firstChar, "", $word);
    } else {
        $firstChar = '';
    }

    if (in_array($lastChar, $specialChars)) {
        $word = str_replace($lastChar, "", $word);
    } else {
        $lastChar = '';
    }

    $startsWithUpper = starts_with_upper($word);
    $word = strtolower($word);

    //@TODO: filter vong words

    switch ($word) {
        case "vong":
            $newWord = $word;
            $editComplete = true;
            break;
        case "eins":
        case "ein":
            $newWord = "1";
            $editComplete = true;
            break;
        case "zwei":
            $newWord = "2";
            $editComplete = true;
            break;
        case "drei":
            $newWord = "3";
            $editComplete = true;
            break;
        case "vier":
            $newWord = "4";
            $editComplete = true;
            break;
        case "fünf":
            $newWord = "5";
            $editComplete = true;
            break;
        case "sechs":
            $newWord = "6";
            $editComplete = true;
            break;
        case "sieben":
            $newWord = "7";
            $editComplete = true;
            break;
        case "acht":
            $newWord = "8";
            $editComplete = true;
            break;
        case "neun":
            $newWord = "9";
            $editComplete = true;
            break;
        case "zehn":
            $newWord = "10";
            $editComplete = true;
            break;
        case "elf":
            $newWord = "11";
            $editComplete = true;
            break;
        case "ich":
            $newWord = "i";
            $editComplete = true;
            break;
        case "vom":
        case "von":
            $newWord = "vong";
            $editComplete = true;
            break;
        case "wenn":
            $newWord = "weng";
            $editComplete = true;
            break;
        case "zu":
            $newWord = "zung";
            $editComplete = true;
            break;
        case "habe":
            $newWord = "han";
            $editComplete = true;
            break;
        case "bist":
            $newWord = "bimst";
            $editComplete = true;
            break;
        case "hallo":
            $newWord = "halo";
            $editComplete = true;
            break;
        case "sind":
            $newWord = "sims";
            $editComplete = true;
            break;
        case "frei":
            $newWord = "fly";
            $editComplete = true;
            break;
        case "kuh":
            $newWord = "Q";
            $editComplete = true;
            break;
        case "baby":
            $newWord = "BB";
            $editComplete = true;
            break;
        case "pfau":
            $newWord = "V";
            $editComplete = true;
            break;
        case "zeh":
            $newWord = "C";
            $editComplete = true;
            break;
        default:
            break;
    }

    if (false === $editComplete && stripos($word, "tz") !== FALSE) {
        $newWord = str_replace("tz", "z", $word);
    }

    if (false === $editComplete && strripos($word,'ß') !== FALSE) {
        $newWord = str_replace("ß", "s", $word);
    }

    //4. ersetze n durch m (@TODO: doppel n wird zu einmal m) - wenn mehrere n im Wort nur das letzt n ersetzen
    if (false === $editComplete && strripos($word,'n') !== FALSE && strripos($word,'n') > 0) {
        $newWord = substr_replace($word, "m", strripos($word,'n'), 1);
        $editComplete = true;
    }

    //5. letzter Buchstabe = t , durch d ersetzen
    //6. letzter Buchstabe = e , weglassen
    if (false === $editComplete && substr($word, -1) === "t") {
        $newWord = substr_replace($word, "d", strlen($word) - 1, 1);
        $editComplete = true;
    } else if (false === $editComplete && substr($word, -1) === "e" && strlen($word) > 5) {
        $newWord = substr_replace($word, "", strlen($word) - 1, 1);
        $editComplete = true;
    }

    //10. ä wird zu e
//        if (stripos($word,'ä') !== FALSE) {
//            $newWord = str_replace("ä", "e", $word);
//            $editComplete = true;
//        }


    if ($newWord == '') $newWord = $word;

    if ($startsWithUpper) $newWord = ucfirst($newWord);

    return $firstChar . $newWord . $lastChar;
}

function starts_with_upper($str) {
    $chr = mb_substr ($str, 0, 1, "UTF-8");
    return mb_strtolower($chr, "UTF-8") != $chr;
}

//echo $text;
//echo PHP_EOL;
//echo vongarize($text);
//echo PHP_EOL;
//echo $text2;
//echo PHP_EOL;
//echo vongarize($text2);
//echo PHP_EOL;
//echo $text3;
//echo PHP_EOL;
//echo vongarize($text3);