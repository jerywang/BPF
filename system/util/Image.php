<?php

/**
 * Image 图片处理（缩放|裁剪|加水印）
 * @author jerry
 *
 */
class Util_Image {
    var $imageResource = NULL;
    var $target = NULL;
    var $enableTypes = array();
    var $imageInfo = array();
    var $createFunc = '';
    var $imageType = NULL;
    var $resource = NULL;
    var $watermark = 'login.png'; //水印图片
    var $outwidth = 450; //图片输出的宽度
    var $outheight = 450; //图片输出的高度
    var $opacity = 80; //水印图片的透明度

    /**
     * Construct for this class
     *
     * @param string $image
     * @return Image
     */
    function Image($image = NULL) {
        //get enables
        if (imagetypes() & IMG_GIF) {
            $this->enableTypes[] = 'image/gif';
        }
        if (imagetypes() & IMG_JPEG) {
            $this->enableTypes[] = 'image/jpeg';
        }
        if (imagetypes() & IMG_JPG) {
            $this->enableTypes[] = 'image/jpg';
        }
        if (imagetypes() & IMG_PNG) {
            $this->enableTypes[] = 'image/png';
        }
        //end get

        if ($image != NULL) {
            $this->setImage($image);
        }
    }

    /**
     * set a image resource
     *
     * @param string $image
     * @return boolean
     */
    function setImage($image) {
        if (file_exists($image) && is_file($image)) {
            $this->imageInfo = getimagesize($image);
            $img_mime = strtolower($this->imageInfo['mime']);
            if (!in_array($img_mime, $this->enableTypes)) {
                exit('系统不能操作这种图片类型.');
            }
            switch ($img_mime) {
                case 'image/gif':
                    $this->resource = $link = imagecreatefromgif($image);
                    $this->createFunc = 'imagegif';
                    $this->imageType = 'gif';
                    break;
                case 'image/jpeg':
                case 'image/jpg':
                    $this->resource = $link = imagecreatefromjpeg($image);
                    $this->createFunc = 'imagejpeg';
                    $this->imageType = 'jpeg';
                    break;
                case 'image/png':
                    $this->resource = $link = imagecreatefrompng($image);
                    $this->createFunc = 'imagepng';
                    $this->imageType = 'png';
                    break;
                default:
                    $link = 'unknow';
                    $this->imageType = 'unknow';
                    break;
            }
            if ($link !== 'unknow') {
                $this->imageResource = $link;
            } else {
                exit('这种图片类型不能改变尺寸.');
            }
            unset($link);
            return true;
        } else {
            return false;
        }
    }

    /**
     * set header
     *
     */
    function setHeader() {
        switch ($this->imageType) {
            case 'gif':
                header('content-type:image/gif');
                break;
            case 'jpeg':
                header('content-type:image/jpeg');
                break;
            case 'png':
                header('content-type:image/png');
                break;
            default:
                exit('Can not set header.');
                break;
        }
        return true;
    }

    /**
     * display origin image
     */
    public function origin() {
    }

    /**
     * change the image size
     *
     * @param int $width
     * @param int $height
     * @return boolean
     */
    function changeSize($width, $height = -1) {
        if (!is_resource($this->imageResource)) {
            exit('不能改变图片的尺寸,可能是你没有设置图片来源.');
        }
        $s_width = $this->imageInfo[0];
        $s_height = $this->imageInfo[1];
        $width = intval($width);
        $height = intval($height);

        if ($width <= 0) exit('图片宽度必须大于零.');
        if ($height <= 0) {
            $height = ($s_height / $s_width) * $width;
        }

        $this->target = imagecreatetruecolor($width, $height);
        if (@imagecopyresized($this->target, $this->imageResource, 0, 0, 0, 0, $width, $height, $s_width, $s_height)) {
//         	//判断是否需要添加水印
//         	if($width>$this->outwidth && $height>$this->outheight){
//         		$s_info = getimagesize($this->watermark);
//         		$resource = imagecreatefrompng($this->watermark);
//         		$r_width = $s_info[0];
//         		$r_height = $s_info[1];
//         		$posX = ($width - $r_width) / 2; //背景图片宽-水印图片宽
//         		$posY = ($height - $r_height) / 2; //背景图片高-水印图片高
//         		imagecopymerge($this->target, $resource, $posX, $posY, 0,0 ,$r_width, $r_height, $this->opacity);
//         		imagedestroy($resource);
//         		unset($resource);
//         	}
            return true;
        } else
            return false;
    }

    /**
     * cut the image
     * @param int $zoom 1:缩放,0:不缩放
     * @param int $width
     * @param int $height
     */
    function cut($zoom = 0, $width = 100, $height = 100) {
        $this->target = imagecreatetruecolor($width, $height);
        if (!$zoom) {
            imagecopy($this->target, $this->resource, 0, 0, 0, 0, $width, $height);
        } else {
            $w = $this->imageInfo[0];
            $h = $this->imageInfo[1];

            if (min($w, $h, $width, $height) == 0) exit('裁剪尺寸为零，或者获取图片尺寸');
            $bl = $width / $height;
            $bl1 = $w / $h;
            if ($bl > $bl1) {
                $h = floor($w * $bl);
            } elseif ($bl < $bl1) {
                $w = floor($h / $bl);
            }
            imagecopyresampled($this->target, $this->resource, 0, 0, 0, 0, $width, $height, $w, $h);
//     		//判断是否需要添加水印
//     		if($width>$this->outwidth && $height>$this->outheight){
// 	    		$s_info = getimagesize($this->watermark);
// 	    		$resource = imagecreatefrompng($this->watermark);
// 	    		$r_width = $s_info[0];
// 	    		$r_height = $s_info[1];
// 	    		$posX = ($width - $r_width) / 2; //背景图片宽-水印图片宽
// 	    		$posY = ($height - $r_height) / 2; //背景图片高-水印图片高
// 	    		imagecopymerge($this->target, $resource, $posX, $posY, 0,0 ,$r_width, $r_height, $this->opacity);
// 	    		imagedestroy($resource);
// 	    		unset($resource);
//     		}
        }
    }

    /**
     * Add watermark
     *
     * @param string $image
     * @param int $opacity
     */
    function addWatermark($opacity = 80) {
        if (file_exists($this->watermark) && is_file($this->watermark)) {
            $s_info = getimagesize($this->watermark);
        } else {
            exit($this->watermark . '文件不存在.');
        }
        $r_width = $s_info[0];
        $r_height = $s_info[1];
        if ($r_width > $this->imageInfo[0]) exit('水印图片必须小于目标图片');
        if ($r_height > $this->imageInfo[1]) exit('水印图片必须小于目标图片');

        switch ($s_info['mime']) {
            case 'image/gif':
                $resource = imagecreatefromgif($this->watermark);
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $resource = imagecreatefromjpeg($this->watermark);
                break;
            case 'image/png':
                $resource = imagecreatefrompng($this->watermark);
                break;
            default:
                exit($s_info['mime'] . '类型不能作为水印来源.');
                break;
        }
        $this->target = &$this->imageResource;
        $posX = ($this->imageInfo[0] - $r_width) / 2; //背景图片宽-水印图片宽
        $posY = ($this->imageInfo[1] - $r_height) / 2; //背景图片高-水印图片高
        //imagecopymerge($this->target, $resource, $this->imageInfo[0] - $r_width - 5, $this->imageInfo[1] - $r_height - 5, 0,0 ,$r_width, $r_height, $opacity);
        imagecopymerge($this->target, $resource, $posX, $posY, 0, 0, $r_width, $r_height, $opacity);
        imagedestroy($resource);
        unset($resource);
    }

    /**
     * create image
     *
     * @param string $name
     * @return boolean
     */
    function create($name = NULL) {
        $function = $this->createFunc;
        if ($this->target != NULL && is_resource($this->target)) {
            if ($name != NULL) {
                $function($this->target, $name);
            } else {
                $function($this->target);
            }
            return true;
        } else if ($this->imageResource != NULL && is_resource($this->imageResource)) {
            if ($name != NULL) {
                $function($this->imageResource, $name);
            } else {
                $function($this->imageResource);
            }
            return true;
        } else {
            exit('不能创建图片,原因可能是没有设置图片来源.');
        }
    }

    /**
     * free resource
     *
     */
    function free() {
        if (is_resource($this->imageResource)) {
            @imagedestroy($this->imageResource);
        }
        if (is_resource($this->target)) {
            @imagedestroy($this->target);
        }
        if (is_resource($this->resource)) {
            @imagedestroy($this->resource);
        }
    }
}


//(1)改变图片大小 
// $img = new Image('test.jpg');
// $img->setHeader();
// $img->changeSize(500,500);//改变尺寸
// $img->create();
// $img->free();

//(2)裁剪图片大小
// $img->cut(1,400,400);
// $img->create();
// $img->free();

//(3)添加水印 
// $img->addWatermark(80);//添加水印,参数是透明值
// $img->create();
// $img->free();
?>
