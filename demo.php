<?php
/**
 * Created by PhpStorm.
 * User: liuft
 * Date: 2016/1/22
 * Time: 11:51
 */

date_default_timezone_set('PRC');


/**
 * jpg压缩成webp格式
 * @param string $dir [webp图片的存储路径]
 * @param int $n [压缩图片的张数]
 * @param int $prefix [图片名称的后缀]
 * @param int $type [裁剪图片的类型]
 */
function start_multi_jpg_transform_webp($dir = '', $n = 1, $prefix = '_a', $type = 6)
{
    global $jpg_dir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755);
        chmod($dir, 0755);
    }
    if (strrchr($jpg_dir, '/') != '/') {

        $jpg_dir = $jpg_dir . '/';
    }
    if (strrchr($dir, '/') != '/') {

        $dir = $dir . '/';
    }
    for ($j = 1; $j <= $n; $j++) {
        $file_name = $jpg_dir . $j . '.jpg';//要裁剪的图片
        $new_jpg_path = $dir . $j . '_' . $prefix;//新的图片路径
        createImg($file_name, $new_jpg_path . '.jpg', $type, 0);//裁剪生成jpg图片
        //生成webp格式的图片
        $new_q = 0;
        $m = 0;
        for ($i = 11; $i <= 18; ++$i) {
            $m = $i * 5;
            $m_q = $m / 100;//新的压缩百分比
            $q = get_img_quality($new_jpg_path . '.jpg');//原图的质量
            $new_q = $q * $m_q;//新的图片质量
            //开始压缩
            do_jpg_transform_webp($new_jpg_path . '.jpg', $new_jpg_path . '_' . $m . '.webp', $new_q);
        }
    }
}


/**
 * @param $jpg_file_name_path [jpg图片的路径]
 * @param string $new_jpg_filename_path [裁剪后jpg图片路径,不设置会将默认的jpg图片覆盖]
 * @param bool $type [裁剪图片类型]
 * @param int $is_compression
 */
function createImg($jpg_file_name_path, $new_jpg_filename_path = '', $type = false, $is_compression = 0)
{
    $resource = new Imagick($jpg_file_name_path);
    if ($type) {
        try {
            $size = $resource->getImageGeometry();
            if ($size['width'] > $size['height']) {
                $left = round(($size['width'] - $size['height']) / 2);
                $resource->cropImage($size['height'], $size['height'], $left, 0);
            } else if ($size['width'] < $size['height']) {

                $top = round(($size['height'] - $size['width']) / 2);
                $resource->cropImage($size['width'], $size['width'], 0, $top);
            }

            if ($type == 1) {
                $resource->resizeImage(210, 210, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 2) {
                $resource->resizeImage(720, 268, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 3) {
                $resource->resizeImage(298, 198, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 4) {
                $resource->resizeImage(338, 365, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 5) {
                $resource->resizeImage(425, 425, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 6) {
                $resource->resizeImage(150, 150, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 7) {
                $resource->resizeImage(640, 640, Imagick::FILTER_CATROM, 1.0, true);
            } elseif ($type == 8) {
                $resource->resizeImage(32, 32, Imagick::FILTER_CATROM, 1.0, true);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    $resource->setImageCompression(Imagick::COMPRESSION_JPEG);
    $current = $resource->getImageCompressionQuality();
    if ($is_compression) {
        $resource->setImageCompressionQuality($current);//压缩质量
    }
    if (!empty($new_jpg_filename_path)) {
        $jpg_file_name_path = $new_jpg_filename_path;
    }
    $resource->writeImage($jpg_file_name_path);
    $resource->clear();
    $resource->destroy();


}

/**
 * jpg格式转换成webp格式
 * @param $jpg_img_path [jpg图片的真实路径]
 * @param $webp_img_path [webp图片的真实路径]
 * @param int $q [图片的压缩质量]
 */
function do_jpg_transform_webp($jpg_img_path, $webp_img_path, $q = 65)
{
    exec("cwebp -q {$q} {$jpg_img_path} -o $webp_img_path");
}


/**
 * 获取图片的质量
 * @param $fileName [图片的具体路径]
 * @return int[图片的质量]
 */
function get_img_quality($fileName)
{
    $resource = new Imagick($fileName);
    $resource->setImageCompression(Imagick::COMPRESSION_JPEG);
    $current = $resource->getImageCompressionQuality();
    $resource->clear();
    $resource->destroy();
    if (empty($current)) {
        $current = 65;
    }
    $resource->clear();
    $resource->destroy();
    return $current;

}


//调用方式
//原始图片，连续用数字命名 ！如 1~10张图片,就1.jpg~10.jpg
$jpg_dir = './image/';//[准备压缩的jpg图片路径,必须设置!] 
//压缩出webp格式的图片
start_multi_jpg_transform_webp('./webp', 1, '_a', 0);
start_multi_jpg_transform_webp('./webp1', 1, '_a', 1);
start_multi_jpg_transform_webp('./webp2', 1, '_a', 2);
start_multi_jpg_transform_webp('./webp3', 1, '_a', 3);


