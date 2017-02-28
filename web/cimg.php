<?php
ini_set("display_errors","on");
require dirname(__FILE__).'/../vendor/zebra/MyZebraImage.php';

function saveimage($url, $filename = ""){
  if(file_exists($filename))
    return true;
  if ($url == "")
    return false;
  if ($filename == "")
    return false;
  ob_start();//打开输出
  readfile($url);//输出图片文件
  $img = ob_get_contents();//得到浏览器输出
  ob_end_clean();//清除输出并关闭
  $size = strlen($img);//得到图片大小
  $fp2 = @fopen($filename, "a");
  fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
  fclose($fp2);
  return true;
}

// url = style=w_$width,h_$height&image=$path
$path = $_GET['image']?$_GET['image']:'';
$style = $_GET['style']?$_GET['style']:'';

$path = urldecode($path);
$path = ltrim($path, '/');
preg_match_all("/(^http[s]?:\/\/)([^\/]*)\/(.*)\/([^\/]*)/", $path, $newpath, PREG_SET_ORDER);
if(isset($newpath['0']['1']) && ($newpath['0']['1'] == 'https://' || $newpath['0']['1'] == 'http://')){//save online image
  $filefold = isset($newpath['0']['2'])?md5($newpath['0']['2']):'nodomain';
  $filename = (isset($newpath['0']['3'])?md5($newpath['0']['3']):'noname1').(isset($newpath['0']['4'])?md5($newpath['0']['4']):'noname2');
  $filepath = "upload/".$filefold."/".$filename.".jpg";
  if(saveimage($path, $filepath))
    $path = $filepath;
}
// print_r($path);exit;

if(strpos(realpath($path),dirname(__FILE__)) === false){//file not in the up folder
  Header ('Content-type:image/jpg');
  print file_get_contents("source/img/unknowimage.jpg");
  exit;
}

if(isset($_SERVER['HTTP_REFERER'])){// refuse cross-domain request
  preg_match_all("/^http[s]?:\/\/([^\/]*)\/.*/", $_SERVER['HTTP_REFERER'], $host, PREG_SET_ORDER);
  if($host['0']['1'] != $_SERVER['HTTP_HOST']){
    Header ('Content-type:image/jpg');
    print file_get_contents("source/img/unknowimage.jpg");
    exit;
  }
}

if(!file_exists($path)){//not exist
  Header ('Content-type:image/jpg');
  print file_get_contents("source/img/unknowimage.jpg");
  exit;
}

$image = new MyZebraImage($path);
if(!in_array($image->checkImagetype(), array('jpg','png','jpeg', 'jpe', 'gif', 'bmp', 'myimage'))){///efuse other ext
  Header ('Content-type:image/jpg');
  print file_get_contents("source/img/unknowimage.jpg");
  exit;
}

$ar = explode(",", $style);
$out = array();
foreach($ar as $x){
  $x_do = explode("_", $x);
  if(count($x_do) == 2)
    $out[$x_do['0']] = $x_do['1'];
}

$outpath = dirname(realpath($path))."/";
$opath = $outpath;
if(isset($out['w']))
  $outpath = $outpath.'w'.$out['w'];
if(isset($out['h']))
  $outpath = $outpath.'h'.$out['h'];

Header ('Content-type:'.$image->mimeType($image->checkImagetype()));
if($outpath == $opath || !file_exists($outpath)){
  print file_get_contents($path);
  exit;
}

$outpath = $outpath.'/'.$image->getImagename();
if(file_exists($outpath)){
  print file_get_contents($outpath);
  exit;
}

// print_r($outpath);exit;

$image->resizeOnall($outpath, isset($out['w'])?$out['w']:0, isset($out['h'])?$out['h']:0);
print file_get_contents($outpath);
