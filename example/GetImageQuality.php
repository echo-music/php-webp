<?php 


use src\ImageMagick;

require_once "../src/ImageMagick.php";


//传入要计算的图片地址
$source = "../image/1.jpg";
$ImageMagick = new ImageMagick($source);

//查询当前图片的质量
$num = $ImageMagick->getImageQuality();

//输出图片质量
echo $num,"\n";


