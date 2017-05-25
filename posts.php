<?php
session_start();

$link = mysqli_connect("127.0.0.1", "root", "", "vong");
//$link = mysqli_connect("127.0.0.1", "vongdb", "&D2o5xd8", "vong");

/* check connection */
if (mysqli_connect_errno()) {
//    printf("Connect failed: %s\n", mysqli_connect_error());
//    exit();
}
$post = null;
$texts = $lastPosts = $posts = array();

if (isset($_GET["id"])) {
    $result = mysqli_query($link, "SELECT * FROM posts WHERE id=" . (int) $_GET["id"]);
    if ( $result === false ) {

    }

    $post = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    $lastPosts = mysqli_query($link, "SELECT * FROM posts ORDER BY id DESC LIMIT 20");

    $result = mysqli_query($link, "SELECT * FROM user_text ORDER BY tstamp_created DESC LIMIT 10");
    if ( $result === false ) {

    }

    while ( $row = mysqli_fetch_assoc($result) ) {
        $texts[$row['id']] = $row;
    }
    mysqli_free_result($result);
} else {
    $posts = mysqli_query($link, "SELECT * FROM posts ORDER BY id DESC LIMIT 50");
    if ( $posts === false ) {

    }
}


mysqli_close($link);
function getGermanDate($date) {
    $datetime = new \DateTime($date);
    return $datetime->format("d.m.Y H:i:s");
}

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
    <meta property="og:title" content="<?php echo $post["title"]; ?>" />
    <meta property="og:locale" content="de_DE" />
    <meta property="og:description" content="<?php echo substr($post["vong"], 0, 150); ?>" />
    <meta property="og:site_name" content="Vong Text und Bild Generator Online" />
<!--    <meta property="article:publisher" content="https://www.facebook.com/vong-generator" />-->
    <meta name="twitter:card" content="summary_large_image" />
    <?php if (isset($post["file"])) { ?>
        <meta property="og:image" content="<?php echo $post["file"]; ?>" />
        <meta name="twitter:image:src" content="<?php echo $post["file"]; ?>" />
    <?php } ?>
    <title>Vong Text und Bild Generator</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="stylesheet" href="/css/shariff.min.css">
    <!-- Your custom styles (optional) -->
    <link href="/css/style.css" rel="stylesheet">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W8XX6HP');</script>
    <!-- End Google Tag Manager -->

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
                <li><a href="/">Generator</a></li>
                <li class="active"><a href="/posts">Beiträge <span class="sr-only">(current)</span></a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

    <div class="container">

        <?php if ($post) { ?>
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-8">
                    <h1><?php echo $post["title"]; ?></h1>
                    <small>Erstellt am: <?php echo date("d.m.Y H:i:s", $post["tstamp"]); ?></small>
                    <hr>
                    <?php if (isset($post["file"]) && $post["file"] !== '') { ?>
                        <div class="post-image">
                            <img src="<?php echo $post["file"]; ?>" class="img" style="width: 100%; max-height: 300px;" />
                        </div>
                        <hr>
                    <?php } ?>
                    <p class="fs-13">
                        <?php echo $post["vong"]; ?>
                    </p>
                    <div class="shariff " data-lang="de" data-services="[&quot;facebook&quot;,&quot;twitter&quot;,&quot;whatsapp&quot;,&quot;googleplus&quot;,&quot;reddit&quot;]"></div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <h3>Letzte Texte vong Spasds</h3>
                    <div class="list-group">
                        <?php foreach ($texts as $text) { ?>
                            <div class="list-group-item list-group-item-action flex-column align-items-start ">
                                <p class="small"><?php echo substr(nl2br($text['vong']), 0, 150); ?></p>
                                <small class="xs"><?php echo getGermanDate($text['tstamp_created']); ?></small>
                                <button class="btn btn-xs btn-info copy-vong-text-button" data-clipboard-text="<?php echo $text['vong']; ?>">Text kopieren</button>
                                <?php if (isset($text['image']) && $text['image'] !== '') { ?>
                                    <button class="btn btn-xs btn-success copy-vong-picture-button" data-clipboard-text="<?php echo $text['image']; ?>">Bild kopieren</button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12" >
                    <h2>Mehr News vong Wichtigkeid her</h2>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12" >
                    <div class="list-group">
                        <?php while ($lastPost = mysqli_fetch_assoc($lastPosts)) { ?>
                            <a href="/posts/<?php echo $lastPost["id"]; ?>" class="list-group-item list-group-item-action flex-column align-items-start ">
                                <div class="row">
                                    <div class="col-xs-1 col-md-1 col-sm-1 ">
                                        <?php if (!isset($lastPost["file"])) { ?>
                                            <img src="img/news.jpg" class="img" style="width: 100%;" />
                                        <?php } else { ?>
                                            <img src="<?php echo $lastPost["file"]; ?>" class="img" style="width: 100%;" />
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-11 col-md-11 col-sm-11">
                                        <p class="lead"><?php echo $lastPost["title"]; ?></p>
                                        <p><?php echo substr($lastPost["vong"], 0, 150); ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12" >
                    <h1>Hier fimdet ihr aktuell Imfos umd Somstiges Zeug vong H1</h1>
                    <p></p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12" >
                    <div class="list-group">
                        <?php while ($post = mysqli_fetch_assoc($posts)) { ?>
                            <a href="/posts/<?php echo $post["id"]; ?>" class="list-group-item list-group-item-action flex-column align-items-start ">
                                <div class="row">
                                    <div class="col-xs-1 col-md-1 col-sm-1 ">
                                        <?php if (!isset($post["file"])) { ?>
                                            <img src="img/news.jpg" class="img" style="width: 100%;" />
                                        <?php } else { ?>
                                            <img src="<?php echo $post["file"]; ?>" class="img" style="width: 100%;" />
                                        <?php } ?>
                                    </div>
                                    <div class="col-xs-11 col-md-11 col-sm-11">
                                        <p class="lead"><?php echo $post["title"]; ?></p>
                                        <p><?php echo substr($post["vong"], 0, 200); ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>


    </div>

<footer class="footer">
    <div class="container">
        <a href="/impressum" class="text-muted">Impressum</a>
    </div>
</footer>

    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="/js/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="/js/tether.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script type="text/javascript" src="/js/shariff.min.js"></script>

    <script type="text/javascript" src="/js/clipboard.min.js"></script>

    <script type="text/javascript" src="/js/scripts.js"></script>
</body>

</html>
