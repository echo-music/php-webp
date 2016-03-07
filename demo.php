<?php
/**
 * Created by PhpStorm.
 * User: liuft
 * Date: 2016/1/22
 * Time: 11:51
 */

date_default_timezone_set('PRC');


/**
 * 裁剪图片
 * @param $write_file_name [当前要裁剪的图片路径]
 * @param string $new_filename [重新裁剪的图片路径]
 * @param bool $type [裁剪的尺寸类型]
 * @param int $n [是否压缩]0 不压缩 1 压缩
 * @return int
 */
function createImg($write_file_name, $new_filename = '', $type = false, $n = 0)
{
    $resource = new Imagick($write_file_name);
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
            exit_script('#500-6' . $e->getMessage());
            exit;
        }
    }

    $resource->setImageCompression(Imagick::COMPRESSION_JPEG);
    $current = $resource->getImageCompressionQuality();
    if ($n) {
        $resource->setImageCompressionQuality($current);//压缩质量
    }
    if (!empty($new_filename)) {
        $write_file_name = $new_filename;
    }
    $resource->writeImage($write_file_name);
    $resource->clear();
    $resource->destroy();


}

/**
 * jpg格式转换成webp格式
 * @param $jpg_img_path [jpg图片的真实路径]
 * @param $webp_img_path [webp图片的真实路径]
 * @param int $q [图片的压缩质量]
 */
function jpg_transform_webp($jpg_img_path, $webp_img_path, $q = 65)
{
    exec("cwebp -q {$q} {$jpg_img_path} -o $webp_img_path");
}

/**
 * jpg格式批量转换成webp格式
 * @param string $dir [存储图片的临时目录]
 * @param int $n [图片的张数]
 * @param int $w [图片的宽度]
 * @param int $type [图片的尺寸类型]
 */
function do_jpg_transform_webp($dir = '', $n = 100, $w = 150, $type = 6)
{
    global $log_msg;
    global $jpg_dir;
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
        chmod($dir,077)
    }
    for ($j = 1; $j <= $n; $j++) {
        $filename = $jpg_dir . $j . '.jpg';//要裁剪的图片
        $newfilename = $j . '_' . $w;//裁剪后新的图片名称
        $new_jpg_path = $dir . '/' . $newfilename;//新的图片路径
        $log_msg .= 'JPG:' . $newfilename . '.jpg' . PHP_EOL;
        createImg($filename, $new_jpg_path . '.jpg', $type, 0);//裁剪生成jpg图片


//生成webp格式的图片
        $new_q = 0;
        $m = 0;
        for ($i = 11; $i <= 18; ++$i) {
            $m = $i * 5;
            $log_msg .= 'm:' . $m . PHP_EOL;
            $m_q = $m / 100;//新的压缩百分比
            $q = get_img_quality($new_jpg_path . '.jpg');//原图的质量
            $new_q = $q * $m_q;//新的图片质量
            $log_msg .= 'q:' . $q . PHP_EOL;
            $log_msg .= 'm_q:' . $m_q . PHP_EOL;
            $log_msg .= 'new_q:' . $new_q . PHP_EOL;
            $log_msg .= 'webp:' . $newfilename . '_' . $m . '.webp' . PHP_EOL;
//开始压缩
            jpg_transform_webp($new_jpg_path . '.jpg', $new_jpg_path . '_' . $m . '.webp', $new_q);
        }
        $log_msg .= "\n" . PHP_EOL;
    }
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
    return $current;

}

/**
 * 打印日志的信息
 * @param $log_msg
 */

function echo_msg($log_msg)
{
    global $log_file;

    @file_put_contents('./a.log', $log_msg . PHP_EOL, FILE_APPEND);

}

//调用方式

$jpg_dir = './image/';//变量名称不要改变，值可以改变
do_jpg_transform_webp('./charbar_circle', 100, 0, 0);

