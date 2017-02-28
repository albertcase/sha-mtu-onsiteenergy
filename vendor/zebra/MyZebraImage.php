<?php
require dirname(__FILE__).'/Zebra_Image.php';

class MyZebraImage{

  public $zebra_image;

  public function __construct($path){
    $this->zebra_image = new Zebra_Image();
    $this->zebra_image->source_path = $path;
  }

  public function resizeOnwidth($outpath, $width){
    $this->zebra_image->target_path = $outpath;
    $this->zebra_image->resize(intval($width), 0, ZEBRA_IMAGE_BOXED, -1);
    return true;
  }

  public function resizeOnheight($outpath, $height){
    $this->zebra_image->target_path = $outpath;
    $this->zebra_image->resize(0, intval($height), ZEBRA_IMAGE_BOXED, -1);
    return true;
  }

  public function resizeOnall($outpath, $width, $height){
    $this->zebra_image->target_path = $outpath;
    $this->zebra_image->resize(intval($width), intval($height), ZEBRA_IMAGE_BOXED, -1);
    return true;
  }

  public function checkImagetype(){
    return strtolower(substr($this->zebra_image->source_path, strrpos($this->zebra_image->source_path, '.') + 1));
  }

  public function getImagename(){
    return substr($this->zebra_image->source_path, strrpos($this->zebra_image->source_path, '/') + 1);
  }

  public function mimeType($ext){
    $swift_mime_types = array(
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'myimage' => 'image/jpeg'
    );
    if(isset($swift_mime_types[$ext]))
      return $swift_mime_types[$ext];
    return 'image/jpeg';
  }

}
