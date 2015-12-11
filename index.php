<?php
include 'src/gallery.php';
gallery::init();
 ?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo end((explode('/',gallery::$name))); ?></title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/blueimp-gallery.css">
    <link rel="stylesheet" href="css/bootstrap-image-gallery.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/align.js"></script>
    <script src="js/lazyload.js"></script>


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
        <div id="blueimp-gallery" class="blueimp-gallery">
            <!-- The container for the modal slides -->
            <div class="slides"></div>
            <!-- Controls for the borderless lightbox -->
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
            <!-- The modal dialog, which will be used to wrap the lightbox content -->
            <div class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body next"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left prev">
                                <i class="glyphicon glyphicon-chevron-left"></i>
                                Previous
                            </button>
                            <button type="button" class="btn btn-primary next">
                                Next
                                <i class="glyphicon glyphicon-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="links">
            <?php  foreach(gallery::listDirs() as $value){
                echo "<a href=\"?dir=".$value."\">
                    <img  class='align' src=\"?folder=".end((explode('/',$value)))."\" data-width='433' data-height='312'>
                </a>\n";
            } ?>

            <?php foreach(gallery::listImages() as $value){
                $size = getimagesize('data'.$value);
                echo "<a href=\"index.php?image=".$value."\" title='".end((explode('/',$value)))."' data-gallery>
                    <img class='align lazy img-responsive' src=\"index.php?thumb=".$value."\" data-width=\"".$size[0]."\" data-height=\"".$size[1]."\">
                </a>\n";
            } ?>
        </div>

    </div>

    <script src="js/jquery.blueimp-gallery.min.js"></script>
    <script src="js/bootstrap-image-gallery.min.js"></script>
    <script>
    $(document).ready(function(){
        $('#blueimp-gallery').data('useBootstrapModal', false);
        $('#blueimp-gallery').toggleClass('blueimp-gallery-controls', true);
    });
    </script>





</body>
</html>
