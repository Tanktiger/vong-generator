<?php

//$text = "Wie heißt eigentlich der Gott der Vegetarier? Hallo, ich bins ein Kräuterbuddha, lol.";
//$text2 = "Ich bin ab jetzt ein Smartphone, ich verlasse das Haus erst wenn ich 100% voll bin. Hallo ich bins ein Smartphone.";
//$text3 = "Nein du Spast. Du bist ein Roboter und kein Mensch. lol";

if (isset($_POST["text"]) && $_POST["text"] !== '') {
    $text =  htmlspecialchars(strip_tags($_POST['text']));
    $link = mysqli_connect("127.0.0.1", "vongdb", "&D2o5xd8", "vong");
//    $link = mysqli_connect("127.0.0.1", "root", "", "vong");

    /* check connection */
    if (mysqli_connect_errno()) {
        echo json_encode(array("vong" => "Sorry, da ist mir ein Fehler unterlaufen! Bitte probiere es erneut!"));
        exit();
    }

    $vong = vongarize($text);

    $now = date("Y-m-d H:i:s");
    $result = mysqli_query($link, "INSERT INTO user_text VALUES (null, 
    '".$now."',
    '".$_SERVER['REMOTE_ADDR']."',
    '".mysqli_real_escape_string($link, $text)."',
    '".mysqli_real_escape_string($link, $vong)."'
    )");

    if ( $result === false ) {
        mysqli_close($link);
        echo json_encode(array("vong" => "Sorry, da ist mir ein Fehler unterlaufen! Bitte probiere es erneut!"));
        exit();
    }

    if ($result instanceof mysqli) {
        mysqli_free_result($result);
    }

    mysqli_close($link);

    header("Content-type: application/json; charset=utf-8");

    echo json_encode(array("vong" => $vong));
    exit();
}
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

    $editComplete = false;
    $newWord = '';

    $lastChar = substr($word, -1);

    if ($lastChar === '!' || $lastChar === '?' || $lastChar === '.' || $lastChar === ',' || $lastChar === ';' || $lastChar === '-' || $lastChar === ':') {
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

    return $newWord . $lastChar;
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