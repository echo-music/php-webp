<?php
/**
 * Created by PhpStorm.
 * Desc: 图片裁剪,图片压缩,计算图片质量
 * User: liufangting
 * Date: 2021/7/24
 * Time: 下午1:52
 */

namespace src;


use Exception;
use Imagick;

class ImageMagick
{


    private $imagickResource = null; //图片处理对象
    private $source = "";//图片资源地址

    private $resizeImageTypes = [
        1 => ["width" => 210, "height" => 210],
        2 => ["width" => 720, "height" => 268],
        3 => ["width" => 298, "height" => 198],
        4 => ["width" => 338, "height" => 365],
        5 => ["width" => 425, "height" => 425],
        6 => ["width" => 150, "height" => 150],
        7 => ["width" => 640, "height" => 640],
        8 => ["width" => 32, "height" => 32],

    ];


    public function __construct($source = '')
    {
        $this->source = $source;
        $this->imagickResource = new Imagick($this->source);
    }


    /**
     * 图片裁剪
     *
     * @param string $target [要裁剪的图片地址]
     * @param $resizeType    [裁剪类型]
     * @param bool $isCompression [是否对图片进行压缩]
     */
    function resizeImage($target = '', $resizeType, $isCompression = false)
    {
        try {
            if(empty($this->imagickResource)){
                throw new Exception("资源是空的");
            }

            if (!in_array($resizeType, array_keys($this->resizeImageTypes))) {
                throw new Exception("图片压缩类型为必填项");
            }

            $size = $this->imagickResource->getImageGeometry();
            if ($size['width'] > $size['height']) {

                $left = round(($size['width'] - $size['height']) / 2);
                $this->imagickResource->cropImage($size['height'], $size['height'], $left, 0);
            } else if ($size['width'] < $size['height']) {

                $top = round(($size['height'] - $size['width']) / 2);
                $this->imagickResource->cropImage($size['width'], $size['width'], 0, $top);
            }

            //裁剪图片资源
            if (isset($this->resizeImageTypes[$resizeType])) {
                $this->imagickResource->resizeImage($this->resizeImageTypes[$resizeType]['width'], $this->resizeImageTypes[$resizeType]['height'], Imagick::FILTER_CATROM, 1.0, true);
            }

            if ($isCompression) {

                $this->imageCompression();
            }

            if (!empty($target)) {
                $this->source = $target;
            }

            //将处理后的图片资源写入到$source文件
            $this->imagickResource->writeImage($this->source);
            

        } catch (Exception $e) {
            echo $e->getMessage(),$e->getLine();
            exit;
        }


    }


    /**
     * 图片压缩
     */
    private function imageCompression()
    {
        $this->imagickResource->setImageCompression(Imagick::COMPRESSION_JPEG);

        $currentImageCompressionQuality = $this->imagickResource->getImageCompressionQuality();
        $this->imagickResource->setImageCompressionQuality($currentImageCompressionQuality);
    }


    /**
     * 将图片转换为webp格式
     *
     * @param $source   [要转换的图片地址]
     * @param $target   [转换后的图片地址] 
     * @param int $ratio [图片质量系数1～100]
     */
    function execImageTransformWebp($source, $target, $ratio = 75)
    {
        if($ratio<=0 || $ratio>100){
            throw new Exception("图片系数质量系数超出1～100范围");
        }
        $currentImageCompressionQuality = $this->getImageQuality();
       
        $currentImageCompressionQuality = $currentImageCompressionQuality*$ratio/100;
        
            
        exec("cwebp -q {$currentImageCompressionQuality} {$source} -o $target");
    }


    /**
     * 获取图片质量
     *
     * @return int
     */
    public function getImageQuality()
    {
        $this->imagickResource->setImageCompression(Imagick::COMPRESSION_JPEG);
        $currentImageCompressionQuality = $this->imagickResource->getImageCompressionQuality();
    
        if (empty($currentImageCompressionQuality)) {
            $currentImageCompressionQuality = 65;
        }
        return $currentImageCompressionQuality;
    }


    


    //释放资源
    public function __destruct()
    {
    
        $this->imagickResource->clear();
        $this->imagickResource->destroy();
    }



}

