<?php
Class Vong {

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
                    $newWordParts[$index] = $this->vongarizeWord($wordPart);
                }

                $newWords[$key] = implode(PHP_EOL, $newWordParts);
            } else {
                $newWords[$key] = $this->vongarizeWord($word);
            }

        }

        return implode(" ", $newWords);
    }

    function vongarizeWord($word) {

        if ($word == '' || $word == 'ä' || $word == 'ü' || $word == 'ö' || $word == "ß") return $word;

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

        $startsWithUpper = $this->starts_with_upper($word);
        $word = strtolower($word);

        $changedWord = $this->wordSwitch($word);
        if ($changedWord !== $word) {
            $editComplete = true;
            $newWord = $changedWord;
        }

        $wordPartsNumbers = array("eins", "zwei", "drei", "vier", "fünf", "sechs", "sieben", "acht", "neun", "zehn", "elf", "zwölf");
        foreach ($wordPartsNumbers as $wordPartsNumber) {
            if (false === $editComplete && FALSE !== stripos($word, $wordPartsNumber)) {
                $newWord = str_replace($wordPartsNumber, $this->wordSwitch($wordPartsNumber), $word);
                $editComplete = true;
                break;
            }
        }


        if (false === $editComplete && stripos($word, "tz") !== FALSE) {
            $newWord = str_replace("tz", "z", $word);
        }

        if (false === $editComplete && strripos($word,'ß') !== FALSE) {
            $newWord = str_replace("ß", "s", $word);
        }

        //ersetze n durch m (@TODO: doppel n wird zu einmal m) - wenn mehrere n im Wort nur das letzt n ersetzen
        if (false === $editComplete && strripos($word,'n') !== FALSE && strripos($word,'n') > 0) {
            $newWord = substr_replace($word, "m", strripos($word,'n'), 1);
            $editComplete = true;
        }

        //letzter Buchstabe = t , durch d ersetzen
        //letzter Buchstabe = e , weglassen
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

    function wordSwitch($word) {
        $newWord = $word;
        switch ($word) {
            case "vong":
                $newWord = $word;
                break;
            case "eins":
            case "ein":
                $newWord = "1";
                break;
            case "zwei":
                $newWord = "2";
                break;
            case "drei":
                $newWord = "3";
                break;
            case "vier":
                $newWord = "4";
                break;
            case "fünf":
                $newWord = "5";
                break;
            case "sechs":
                $newWord = "6";
                break;
            case "sieben":
                $newWord = "7";
                break;
            case "acht":
                $newWord = "8";
                break;
            case "neun":
                $newWord = "9";
                break;
            case "zehn":
                $newWord = "10";
                break;
            case "elf":
                $newWord = "11";
                break;
            case "ich":
                $newWord = "i";
                break;
            case "vom":
            case "von":
                $newWord = "vong";
                break;
            case "wenn":
                $newWord = "weng";
                break;
            case "zu":
                $newWord = "zung";
                break;
            case "habe":
                $newWord = "han";
                break;
            case "bist":
                $newWord = "bimst";
                break;
            case "hallo":
                $newWord = "halo";
                break;
            case "sind":
                $newWord = "sims";
                break;
            case "frei":
                $newWord = "fly";
                break;
            case "kuh":
                $newWord = "Q";
                break;
            case "baby":
                $newWord = "BB";
                break;
            case "pfau":
                $newWord = "V";
                break;
            case "zeh":
                $newWord = "C";
                break;
            default:
                break;
        }

        return $newWord;
    }
}