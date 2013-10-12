<?php
//echo dirname(__FILE__); exit();
$file = '1.jpg';
if(!is_file($file)) exit('no file');
header('Content-type: image/jpeg');
header("Content-Disposition: inline; filename='simpletext.jpg'");
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
$source = ImageCreateFromJPEG($file);
ImageJPEG($source,null,'80');
imagedestroy($source);
?>