<?php 

/*图片裁剪*/
use src\ImageMagick;

require_once "../src/ImageMagick.php";

//传入要裁剪的图片地址
$source = "../image/1.jpg";
$ImageMagick = new ImageMagick($source);

//传入该参数,裁剪后的图片保存到该文件中
$target = "../image/1_2.jpg";
$ImageMagick->resizeImage($target,1,false);