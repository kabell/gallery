<?php
include 'src/gallerynew.php';

if(isset($_GET['image'])){
    $item = new Image($_GET['image']);
    if(isset($_GET['thumb']))
        $item->getThumb();
    else
        $item->getContent();
}

else if(isset($_GET['dir'])){
    $item = new Dir($_GET['dir']);
    if(isset($_GET['thumb']))
        $item->getThumb();
    else
        $item->getContent();
}
else if(isset($_GET['rotate'])){
    $item = new Image($_GET['rotate']);
    $item->getRotatedThumb();
}
else {
    $item = new Dir('/');
    $item->getContent();
}

 ?>
