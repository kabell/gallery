<?php

class Settings{

    const
        DIR = "dir",
        IMAGE = "image",
        VIDEO = "video",
        UNKNOWN = "unknown";

    public
        $video = Array("webm"),
        $image = Array("jpg","JPG","jpeg","JPEG","png","PNG"),
        $rootPrefix,
        $dataPrefix,
        $dataDir = 'data';

    public function __construct(){
        $this->rootPrefix = rtrim(getcwd(),'/');
        $this->dataPrefix = $this->rootPrefix.'/'.$this->dataDir;
    }
}

class Item extends Settings{

    public
        $type,
        $path,
        $fullPath;

    public function __construct($path){
        parent::__construct();
        //echo "__construct path='".$path."'<br/>";

        //normalize path string
        $path = trim($path,'/');

        //set full path of object
        $this->path = $path;
        $this->fullPath = $this->dataPrefix.'/'.$path;

        //echo "this->path = '".$this->path."'<br/>";
        //echo "this->fullPath = ".$this->fullPath."<br/>";

        //determine type
        if(!is_dir($this->fullPath))
            $ex = $this->getExtension();

        if(is_dir($this->fullPath))
            $this->type = self::DIR;
        else if(in_array($ex,$this->video) && substr($this->getBaseName(),0,10)=='converted_')
            $this->type = self::VIDEO;
        else if(in_array($ex,$this->image))
            $this->type = self::IMAGE;
        else $this->type = self::UNKNOWN;
    }

    public function isDir(){
        return $this->type == self::DIR;
    }
    public function isImage(){
        return $this->type == self::IMAGE;
    }
    public function isVideo(){
        return $this->type == self::VIDEO;
    }

    public function getPath(){
        return $this->path;
    }

    public function getBaseName(){
        return basename($this->path);
    }

    public function getParentDirectory(){
        //echo $this->path."<br/>";
        $array = (explode('/',$this->path));
        //print_r($array);
        if(count($array) <= 1)
            return "/";
        array_pop($array);
        return implode('/',$array);
    }

    public function getExtension(){
        return end((explode('.',$this->path)));
    }

    public function getURL(){
        return $this->dataDir.'/'.$this->getPath();
    }

    public function getThumbAddress(){
        return $this->dataDir.'/'.$this->getParentDirectory().'/.thumb/'.$this->getBaseName().".jpg";
    }

}



class Dir extends Item{

    public function __construct($path){
        parent::__construct($path);
    }

    public function getThumb(){
        // Create the image
        $im = imagecreatefromstring(file_get_contents($this->rootPrefix.'/img/folder.jpg'));

        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 399, 29, $white);

        // The text to draw

        // Replace path by your own font path
        $font = $this->rootPrefix.'/fonts/arialb.ttf';
        //echo $text;

        $text = $this->getBaseName();
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
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);

    }

    private function listDir(){
        //echo "listing directory path=".$this->fullPath."<br/>";
        $dir = preg_grep('/^([^.])/', scandir($this->fullPath));
        $list = Array();
        foreach($dir as $value){
            $list[] = new Item($this->path.'/'.$value);
        }
        return $list;
    }

    public function getContent(){
        $data = $this->listDir();
        $dirs = Array();
        $files = Array();
        foreach($data as $value)
            if($value->isDir())
                $dirs[]=$value;
            else
                $files[]=$value;

        usort($dirs,function($a,$b){
                return ($a->getBaseName() < $b->getBaseName()) ? -1 : 1;
            }
        );
        usort($files,function($a,$b){
                return ($a->getBaseName() < $b->getBaseName()) ? -1 : 1;
            }
        );
        include $this->rootPrefix.'/src/page.html';
    }
}


class Image extends Item {

    private
        $thumbDir;

    public function __construct($path){
        parent::__construct($path);
        $this->thumbDir = $this->rootPrefix.'/'.$this->dataDir.'/'.$this->getParentDirectory().'/.thumb';
    }

    private function resize($source, $dest, $maxHeigth){
        $fn = $source;
        $size = getimagesize($fn);
        $ratio = $size[0]/$size[1]; // width/height
        $width = $maxHeigth*$ratio;
        $height = $maxHeigth;

        $src = imagecreatefromstring(file_get_contents($fn));
        $dst = imagecreatetruecolor($width,$height);
        imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
        imagedestroy($src);
        imagejpeg($dst,$dest); // adjust format as needed
        imagedestroy($dst);
    }

    private function rotate($source,$dest,$angle){
        $src = imagecreatefromstring(file_get_contents($source));
        $rotate = imagerotate($src, 270, 0);
        imagedestroy($src);
        imagejpeg($rotate,$dest); // adjust format as needed
        imagedestroy($rotate);
    }

    public function getThumb(){

        //check for a thumb dir
        //echo "thumbdir = ".$this->thumbDir."<br/>";

        if(!is_dir($this->thumbDir))
            mkdir($this->thumbDir);

        //check for a thumb
        if(!file_exists($this->thumbDir.'/'.$this->getBaseName())){
            $this->resize($this->fullPath,$this->thumbDir.'/'.$this->getBaseName(),200);
        }

        //output image
        header("Content-Type: image/jpeg");
        echo readfile($this->thumbDir.'/'.$this->getBaseName());
    }

    public function getContent(){

        // resize if neccesary
        $size = getimagesize($this->fullPath);;
        if($size[1]>1000){
            $this->resize($this->fullPath,$this->fullPath,1000);
        }

        //output image
        header("Content-Type: image/jpeg");
        echo readfile($this->fullPath);
    }

    public function getRotatedThumb(){

            //rotate image
            $this->rotate($this->fullPath,$this->fullPath,270);
            //remove old thumbnail
            unlink($this->thumbDir.'/'.$this->getBaseName());
            //create and output new thumbnail
            $this->getThumb();
    }

}

?>
