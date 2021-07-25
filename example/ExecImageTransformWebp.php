<?php 


use src\ImageMagick;

require_once "../src/ImageMagick.php";

//传入要裁剪的图片地址
$source = "../image/1.jpg";
$ImageMagick = new ImageMagick($source);

//将裁剪后的图片转换成webp格式
$target = "../image/1_1.webp";
$ImageMagick->execImageTransformWebp($source, $target,90);