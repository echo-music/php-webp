<?php


/**
 * Created by PhpStorm.
 * User: liufangting
 * Date: 2021/7/24
 * Time: 下午4:36
 */

use src\ImageMagick;

require_once "./src/ImageMagick.php";


//传入要裁剪的图片
$source = "./image/1.jpg";
$ImageMagick = new ImageMagick($source);

//查询当前图片的质量
$num = $ImageMagick->getImageQuality();

//传入该参数,裁剪后的图片保存到该文件中
$target = "./image/1_1.jpg";
$ImageMagick->resizeImage($target,1,false);

//将裁剪后的图片转换为webp格式
$source = "./image/1_1.jpg";
$target = "./image/1_1.webp";
$ImageMagick->execImageTransformWebp($source, $target,100);



