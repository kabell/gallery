<?php


class gallery{

    const

    HOME = 0,
    DIR = 1,
    IMAGE = 2,
    THUMB = 3;


    public static
    $name = '',
    $full_path,
    $relative_path,
    $parent;

    private static
    $root = '',

    $img_dir,
    $thumb_dir,
    $cwd,
    $mode = self::HOME,
    $image_dir = 'data';






public static function init(){
    self::$root = getcwd()."/data";
    self::$cwd = getcwd();


    if(isset($_GET['dir'])){
        if(self::setDir($_GET['dir']))
            $mode = self::DIR;
    }
    else if(isset($_GET['image'])){
        if(self::setFile($_GET['image'])){
            $mode = self::IMAGE;
            self::getImage();
            exit();
        }
    }
    else if(isset($_GET['thumb'])){
        if(self::setFile($_GET['thumb'])){
            $mode = self::THUMB;
            self::getThumb();
            exit();
        }
    }
    else if(isset($_GET['folder'])){
        $text = $_GET['folder'];
        self::folder($text);
        exit();
    }
    else if(isset($_GET['rotate'])){
        if(self::setFile($_GET['rotate'])){
            $mode = self::IMAGE;
            self::rotate();
            exit();
        }
    }

    else{
        if(self::setDir(''))
            $mode = self::DIR;
    }
}


private static function folder($text){
    header('Content-Type: image/png');

    // Create the image
    $im = imagecreatefromstring(file_get_contents(self::$cwd.'/img/folder.jpg'));;

    // Create some colors
    $white = imagecolorallocate($im, 255, 255, 255);
    $grey = imagecolorallocate($im, 128, 128, 128);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 399, 29, $white);

    // The text to draw

    // Replace path by your own font path
    $font = self::$cwd.'/arialb.ttf';
    //echo $text;

    $text_box = imagettfbbox(20,0,$font,$text);

    // Get your Text Width and Height
    $text_width = $text_box[2]-$text_box[0];
    $text_height = $text_box[7]-$text_box[1];

    $width = 425;
    $height = 312;

    // Calculate coordinates of the text
    $x = ($width/2) - ($text_width/2);

    // Add some shadow to the text
    imagettftext($im, 20, 0, $x+1, $height-30, $grey, $font, $text);

    // Add the text
    imagettftext($im, 20, 0, $x, $height-30, $black, $font, $text);

    // Using imagepng() results in clearer text compared with imagejpeg()
    imagepng($im);
    imagedestroy($im);

}

private static function setDir($path){
        if(chdir(self::$root.'/'.$path)){
            $currDir = getcwd();
            chdir(self::$cwd);
            $pos = stripos($currDir,self::$root);

            //echo $currDir.' '.self::$root;

            if($pos !== false && $pos == 0){
                self::$full_path = $currDir;
                self::$relative_path = str_replace(self::$root,'',$currDir);
            }
            else{
                echo "This dir isn't a subdir!!";
                return false;
            }
        }
        else return false;
        //echo self::$root." ".self::$relative_path." ".self::$full_path;

        self::$name = end((explode('/',self::$relative_path)));
        $tmp = (explode('/',self::$relative_path));
        array_pop($tmp);
        self::$parent = implode('/',$tmp);


        return true;
}


private static function setFile($fullpath){
        $fullpath = './'.$fullpath;
        $path = substr($fullpath,0,strrpos($fullpath,'/'));
        $filename = substr($fullpath,strrpos($fullpath,'/')+1);

        if($filename[0]=='.')
            return false;

        //pozrieme sa na zlozku
        if(chdir(self::$root.'/'.$path)){
            $currDir = getcwd();
            $pos = stripos($currDir,self::$root);
            chdir(self::$cwd);

            if($pos !== false && $pos == 0){
                self::$full_path = $currDir;
                self::$relative_path = str_replace(self::$root,'',$currDir);
            }
            else{
                echo "This dir isn't a subdir!!";
                return false;
            }
        }
        else return false;

        //pozrieme sa na subor
        if(file_exists(self::$full_path.'/'.$filename)){
            self::$full_path = self::$full_path."/".$filename;
            self::$relative_path = self::$relative_path.'/'.$filename;
        }
        else return false;

        //echo self::$root." ".self::$relative_path." ".self::$full_path;
        self::$img_dir = substr(self::$full_path,0,strrpos(self::$full_path,'/'));
        self::$thumb_dir = self::$img_dir.'/.thumb';

        self::$name = $filename;
        return true;
}

private static function getImage(){
    header("Content-Type: image/jpeg");
    $size = getimagesize(self::$full_path);
    if($size[1]>1000){
        $ratio = $size[0]/$size[1]; // width/height
        $width = 1000*$ratio;
        $height = 1000;

        $src = imagecreatefromstring(file_get_contents(self::$full_path));
        $dst = imagecreatetruecolor($width,$height);
        imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
        imagedestroy($src);
        imagejpeg($dst,self::$full_path); // adjust format as needed
        imagedestroy($dst);
    }

    echo readfile(self::$full_path);
}

public static function getThumb(){
    header("Content-Type: image/jpeg");
    if(!is_dir(self::$thumb_dir))
        mkdir(self::$thumb_dir);
    if(!file_exists(self::$thumb_dir.'/'.self::$name)){
        self::makeThumb();
    }
    echo readfile(self::$thumb_dir.'/'.self::$name);
}

private static function makeThumb(){
    // echo self::$img_dir.'/'.self::$name.' '.self::$thumb_dir.'/'.self::$name;

    $fn = self::$img_dir.'/'.self::$name;
    $size = getimagesize($fn);
    $ratio = $size[0]/$size[1]; // width/height
    $width = 200*$ratio;
    $height = 200;

    $src = imagecreatefromstring(file_get_contents($fn));
    $dst = imagecreatetruecolor($width,$height);
    imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
    imagedestroy($src);
    imagejpeg($dst,self::$thumb_dir.'/'.self::$name); // adjust format as needed
    imagedestroy($dst);
}

public static function randomThumbnail($folder){


}

private static function rotate(){
        $fullpath = self::$full_path;
        $path = substr($fullpath,0,strrpos($fullpath,'/'));
        $filename = substr($fullpath,strrpos($fullpath,'/')+1);

        $src = imagecreatefromstring(file_get_contents(self::$full_path));
        $rotate = imagerotate($src, 270, 0);
        imagedestroy($src);
        imagejpeg($rotate,self::$full_path); // adjust format as needed
        imagedestroy($rotate);
        //rotate thumbnail
        $src = imagecreatefromstring(file_get_contents($path."/.thumb/".$filename));
        $rotate = imagerotate($src, 270, 0);
        imagedestroy($src);
        imagejpeg($rotate,$path."/.thumb/".$filename); // adjust format as needed
        imagedestroy($rotate);
        self::getThumb();

}



public static function listDirs(){
    $list = preg_grep('/^([^.])/', scandir(self::$full_path));
    //echo self::$full_path;
    $dirs = Array();
    foreach($list as $value){
        if(is_dir(self::$full_path.'/'.$value))
        $dirs[]=self::$relative_path.'/'.$value;
    }
    sort($dirs);
    return $dirs;
}

public static function listImages(){
    $list = preg_grep('/^([^.]).*\.(JPG|jpg|jpeg|JPEG)$/', scandir(self::$full_path));

    $dirs = Array();
    foreach($list as $value){
        if(!is_dir(self::$full_path.'/'.$value))
        $dirs[]=self::$relative_path.'/'.$value;
    }
    return $dirs;

}


}

?>
