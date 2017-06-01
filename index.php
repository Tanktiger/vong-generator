<?php
session_start();
/**
 * @copyright  Tom Scheduikat, 2017
 */
$limit = 25;
$page = 0;

if (isset($_GET) && is_array($_GET) && count($_GET) > 0) {
    if (isset($_GET['p']) && $_GET['p'] !== '') $page = (int) $_GET['p'];
}

$start = $limit * $page;

if (!$start && !is_int($start)) $start = 0;

//$link = mysqli_connect("127.0.0.1", "root", "", "vong");
$link = mysqli_connect("127.0.0.1", "vongdb", "&D2o5xd8", "vong");

/* check connection */
if (mysqli_connect_errno()) {
//    printf("Connect failed: %s\n", mysqli_connect_error());
//    exit();
}

$texts = array();

$resultCount = mysqli_query($link, "SELECT COUNT(*) as count FROM user_text LIMIT 10000");
if ( $resultCount === false ) {

}

$rowCount = mysqli_fetch_assoc($resultCount);
$overallRows = $pageCount = 0;
if (isset($rowCount["count"])) {
    $overallRows = $rowCount["count"];

    if ($overallRows > 0) {
        $pageCount = ceil($overallRows/$limit);
    }
}


$result = mysqli_query($link, "SELECT * FROM user_text ORDER BY tstamp_created DESC LIMIT " . mysqli_real_escape_string($link, $start) . ',' . mysqli_real_escape_string($link, $limit));
if ( $result === false ) {

}

while ( $row = mysqli_fetch_assoc($result) ) {
    $texts[$row['id']] = $row;
}
$posts = mysqli_query($link, "SELECT * FROM posts ORDER BY id DESC LIMIT 10");

mysqli_free_result($result);
mysqli_close($link);

function getGermanDate($date) {
    $datetime = new \DateTime($date);
    return $datetime->format("d.m.Y H:i:s");
}

$previousLikes = (isset($_SESSION["likes"]))? $_SESSION["likes"]: array();

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="Eigene Nachrichten oder Whatsapp Messages in Vong Text oder Bild umwandeln. ✓ Einfach ✓ schnell ✓ online so vong Einfachkeit her">
    <meta name="robots" content="index">
    <meta property="og:url" content="http://www.vong-generator.de/" />
    <meta property="og:title" content="Vong Text Generator - Nachrichten in Vong Text erstellen als Bild oder Text" />
    <meta property="og:locale" content="de_DE" />
    <meta property="og:description" content="Eigene Nachrichten oder Whatsapp Messages in Vong Text oder Bild umwandeln. ✓ Einfach ✓ schnell ✓ online so vong Einfachkeit her" />
    <meta property="og:site_name" content="Vong Text und Bild Generator Online" />
    <meta property="article:publisher" content="https://www.facebook.com/vong-generator" />
<!--    <meta property="og:image" content="https://nextlevelseo.de/wp-content/uploads/2014/10/title-tag.jpg" />-->
<!--    <meta name="twitter:card" content="summary_large_image" />-->
<!--    <meta name="twitter:image:src" content="https://nextlevelseo.de/wp-content/uploads/2014/10/title-tag.jpg" />-->
<!--    <meta name="twitter:site" content="@vongGenerator" />-->
    <title>Vong Text und Bild Generator</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="stylesheet" href="css/shariff.min.css">
    <!-- Your custom styles (optional) -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W8XX6HP');</script>
    <!-- End Google Tag Manager -->

    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W8XX6HP"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<nav class="navbar navbar-default  main-nav navbar-light bg-faded">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Vong Generator</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="/">Generator <span class="sr-only">(current)</span></a></li>
                <?php if (mysqli_num_rows ($posts) > 0) { ?>
                <li><a href="/posts">Beiträge</a></li>
                <?php } ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
    <div class="container">
        <?php if ($page === 0) { ?>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <h1>Vong Text und Bild Generator Online</h1>
                <p class="fs-13">
                    Deine Nachricht oder Whatsapp Message schnell und einfach in einen Vong Text umwandeln. Als Bild oder Text kann dann euer Vong Text verteilt werden.
                    Dazu dient diese Seite. Ihr verleiht euren Nachrichten dadurch  eine neue Art von Witz und eure Freunde fallen so noch mehr vom Stuhl, lol.
                    <br>Gebt einfach hier euren Text ein und drückt dann auf "Text umwandeln" um euren Text in Vong Deutsch zu erhalten so vong Einfachkeit her.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12" >
                <p>&nbsp;</p>
            </div>
        </div>
        <form>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group">
                        <label for="normalText" class="fs-15">Dein Text:</label>
                        <textarea class="form-control" id="normalText" rows="10"></textarea>
                    </div>
                    <button id="generateTextButton" type="button" class="btn btn-block btn-lg btn-primary">Text umwandeln <i class="fa fa-arrow-right"></i></button>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group">
                        <label for="vongText" class="fs-15">Dein Vong Text:</label>
                        <textarea class="form-control" id="vongText" rows="10"></textarea>
                    </div>
                    <button id="copyVongText" type="button" class="btn btn-block btn-lg btn-info">Vong Text kopieren <i class="fa fa-files-o"></i></button>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 text-center" style="display: none;margin-top: 20px;">
                    <img src="" id="previewImage" style="width: 50%">
                    <button id="copyVongPicture" type="button" class="btn btn-block btn-lg btn-success">Bild Url kopieren <i class="fa fa-image"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 center-block">
                    <p>&nbsp;</p>
                    <p class="mb-1" style="font-size: 1.3em;">Hinweis: Eure Texte werden gespeichert. Danke das Ihr den Generator benutzt. Teilt Ihn mit euren Freunden wenn er euch gefällt!</p>
                    <div class="shariff " data-lang="de" data-services="[&quot;facebook&quot;,&quot;twitter&quot;,&quot;whatsapp&quot;,&quot;googleplus&quot;,&quot;reddit&quot;]"></div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12" >
                <p>&nbsp;</p>
            </div>
        </div>
        <?php } //endif ?>

        <?php if (mysqli_num_rows ($posts) > 0) { ?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6" >
                <h2>Aktuele Informazion vong Wichtigkeid her</h2>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12" >
                <div class="list-group">
                    <?php while ($post = mysqli_fetch_assoc($posts)) { ?>
                    <a href="/posts/<?php echo $post["id"]; ?>" class="list-group-item list-group-item-action flex-column align-items-start ">
                        <div class="row">
                            <div class="col-xs-2 col-md-2 col-sm-2 ">
                                <?php if (!isset($post["file"])) { ?>
                                    <img src="img/news.jpg" class="img" style="width: 100%;" />
                                <?php } else { ?>
                                    <img src="<?php echo $post["file"]; ?>" class="img" style="width: 100%;" />
                                <?php } ?>
                                <small><?php echo date("d.m.Y H:i:s", $post['tstamp']); ?></small>
                            </div>
                            <div class="col-xs-10 col-md-10 col-sm-10">
                                <p class="lead"><?php echo $post["title"]; ?></p>
                            </div>
                        </div>
                    </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6" >
                <?php if ($page === 0) { ?>
                    <h2>Bisher erstellte Vong Texte vong H1s</h2>
                <?php } else { ?>
                    <h1>Bisher erstellte Vong Texte mit dem Online Generator</h1>
                    <p class="fs-13">
                        Deine Nachricht oder Whatsapp Message schnell und einfach in einen Vong Text umwandeln. Als Bild oder Text kann dann euer Vong Text verteilt werden.
                        Dazu dient diese Seite. Ihr verleiht euren Nachrichten dadurch eine neue Art von Witz und eure Freunde fallen so noch mehr vom Stuhl, lol.
                        <br>Gebt einfach hier euren Text ein und drückt dann auf "Text umwandeln" um euren Text in Vong Deutsch zu erhalten so vong Einfachkeit her.
                        <a href="/" class="btn btn-warning">Jetzt Text selbst generieren</a>
                    </p>
                    <div class="shariff " data-lang="de" data-services="[&quot;facebook&quot;,&quot;twitter&quot;,&quot;whatsapp&quot;,&quot;googleplus&quot;,&quot;reddit&quot;]"></div>
                <?php } ?>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12" >
                <div class="list-group">
                    <?php foreach ($texts as $text) { ?>
                    <div class="list-group-item list-group-item-action flex-column align-items-start ">
                        <div class="row">
                            <div class="col-xs-1 col-md-1 col-sm-1 ">
                                <div class="vcenter text-center ">
                                    <?php if (!in_array($text['id'], $previousLikes)) {?>
                                    <i class="fa fa-3x fa-thumbs-o-up"
                                       id="likeIcon<?php echo $text['id']; ?>"
                                       onclick="likeText(<?php echo $text['id']; ?>)"
                                    ></i>
                                    <?php } else { ?>
                                        <i class="fa fa-3x fa-thumbs-o-up font-yellow"
                                           id="likeIcon<?php echo $text['id']; ?>"
                                        ></i>
                                    <?php } ?>
                                    <br>
                                    <span class="like-count fs-15" id="likeCount<?php echo $text['id']; ?>"><?php echo (isset($text['likes']))? $text['likes']: 0; ?></span>
                                </div>
                            </div>
                            <div class="col-xs-11 col-sm-11 col-md-11">
                                <p class="lead"><?php echo substr(nl2br($text['vong']), 0, 500); ?></p>
                                <small><?php echo getGermanDate($text['tstamp_created']); ?></small>
                                <button class="btn btn-sm btn-info copy-vong-text-button" data-clipboard-text="<?php echo $text['vong']; ?>">Text kopieren</button>
                                <?php if (isset($text['image']) && $text['image'] !== '') { ?>
                                    <button class="btn btn-sm btn-success copy-vong-picture-button" data-clipboard-text="<?php echo $text['image']; ?>">Bild kopieren</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <p>&nbsp;</p>
                <?php if (null !== $texts && count($texts) > 0) { ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page === 0) ? 'disabled':''; ?> ">
                            <a class="page-link" href="<?php echo "/?p=" . ($page - 1) ?>" aria-label="Zurück">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Zurück</span>
                            </a>
                        </li>
                        <?php for($i = 0; $i < $pageCount; $i++) { ?>
                            <li class="page-item  <?php echo ($page === $i) ? 'active':''; ?>">
                                <a class="page-link" href="<?php echo "/?p=" . $i ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="page-item <?php echo ($page == ($pageCount-1)) ? 'disabled':''; ?>">
                            <a class="page-link" href="<?php echo "/?p=" . ($page + 1) ?>" aria-label="Vor">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Vor</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php } ?>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12" >
                <h3>Herkunft vong Sprache her</h3>
                <blockquote class="fs-13">
                    <p>
                    absichtliche Falschschreibung von von bzw. von dem oder vom<br><br>
                    Die genaue Herkunft ist ungeklärt. Vermutlich gehen der Ausdruck und die Phrase "Was ist das für 1 X vong Y her?" auf mehrere Accounts in sozialen Medien zurück. Der österreichische Rapper Moneyboy gilt dabei im Hinblick auf die Phrase und der Facebook-Account „Nachdenkliche Sprüche mit Bilder“ im Hinblick auf die absichtliche Falschschreibung von „von“ zu „vong“ am einflussreichsten.
                    </p>
                    <footer><a href="https://de.wiktionary.org/wiki/vong" title="Wiktionary"><cite title="wikipedia.de">wiktionary</cite></a></footer>
                </blockquote>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <a href="/impressum" class="text-muted">Impressum</a>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="js/tether.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script type="text/javascript" src="js/shariff.min.js"></script>

    <script type="text/javascript" src="js/clipboard.min.js"></script>

    <script type="text/javascript" src="js/scripts.js"></script>
</body>

</html>
