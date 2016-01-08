<?php
include 'src/gallery.php';
gallery::init();

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


 ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo end((explode('/',gallery::$name))); ?></title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <?php if(isMobile()){?>
    <link rel="stylesheet" href="css/blueimp-gallery_mobile.css">
    <?php }else{ ?>
    <link rel="stylesheet" href="css/blueimp-gallery.css">
    <?php };?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>



    <style>
        img{
            padding: 3px 1px 3px 1px;
            max-height: 170px;
        }

    </style>
</head>
<body>
    <div class='jumbotron'>
        <div class='container'>
            <div class='row'>
                <div class='col-sm-5'>
                    <?php if(gallery::$name!=''): ?><a href='?dir=<?php echo gallery::$parent;?>'><img src='/img/back.png'></a><?php endif;?>
                </div>
                <div class='col-sm-7'>
                    <h2><?php echo gallery::$name; ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class='container' id='container'>
        <!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
        <div id="links">
            <?php  foreach(gallery::listDirs() as $value){
                echo "<a href=\"?dir=".htmlspecialchars(urlencode($value))."\">
                    <img  class='align' src=\"?folder=".urlencode(end((explode('/',$value))))."\" data-width='433' data-height='312'>
                </a>\n";
            } ?>

            <?php $pocet=0;
                foreach(gallery::listImages() as $value){
                $size = getimagesize('data'.$value);
                echo "<a href=\"index.php?image=".htmlspecialchars(urlencode($value))."\" title='".end((explode('/',$value)))."' data-gallery>";
                    echo "<img id='thumb".$pocet."' class='align lazy' src=\"index.php?thumb=".htmlspecialchars(urlencode($value))."\" data-width=\"".$size[0]."\" data-height=\"".$size[1]."\">";

                echo "</a><a href='#' onClick=\"$('#thumb".$pocet."').attr('src', 'index.php?rotate=".htmlspecialchars(urlencode($value))."'+'&date=' + new Date().getTime()); return false;\">Rotate</a><br/>\n";
                $pocet++;
            } ?>
        </div>

    </div>

    <script>
        function rotate(id){
            $("#myimg").attr("src", $("#myimg").attr("src")+"?timestamp=" + new Date().getTime());

        }

    </script>



</body>
</html>
