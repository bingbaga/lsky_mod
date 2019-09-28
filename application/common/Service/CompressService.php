<?php

namespace app\common\Service;

/**
 * 图片压缩类：通过缩放来压缩。
 * 如果要保持源图比例，把参数$percent保持为1即可。
 * 即使原比例压缩，也可大幅度缩小。数码相机4M图片。也可以缩为700KB左右。如果缩小比例，则体积会更小。
 *
 * 结果：可保存、可直接显示。
 */
class CompressService {
    private $src;
    private $image;
    private $imageInfo;
    private $percent;

    /**
     * 图片压缩
     * @param string $src
     * @param int $percent 压缩比例
     */
    public function __construct(string $src, int $percent = 1) {
        $this->src = $src;
        $this->percent = $percent;
    }

    /** 高清压缩图片
     * @param string $saveName 提供图片名（可不带扩展名，用源图扩展名）用于保存。或不提供文件名直接显示
     */
    public function compressImg($saveName = ''): void {
        $this->_openImage();
        if (!empty($saveName)) {
            if($this->imageInfo['storage'] >= 1024*1024){
                $this->_saveImage($saveName);
            }
        }  //保存
        else {
            $this->_showImage();
        }
    }

    /**
     * 内部：打开图片
     */
    private function _openImage() {
        $info = getimagesize($this->src);
        $this->imageInfo = [
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => image_type_to_extension($info[2], false),
            'attr'   => $info[3],
            'storage'   => filesize($this->src)
        ];
        $fun = "imagecreatefrom" . $this->imageInfo['type'];
        $this->image = $fun($this->src);
        $this->_thumpImage();
    }

    /**
     * 内部：操作图片
     */
    private function _thumpImage() {
        $new_width = $this->imageInfo['width'] * $this->percent;
        $new_height = $this->imageInfo['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageInfo['width'], $this->imageInfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }

    /**
     * 输出图片:保存图片则用saveImage()
     */
    private function _showImage() {
        header('Content-Type: image/' . $this->imageInfo['type']);
        $funcs = "image" . $this->imageInfo['type'];
        $funcs($this->image);
    }

    /**
     * 保存图片到硬盘：
     * @param string $dstImgName 1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     * @return bool
     */
    private function _saveImage($dstImgName) {
        if (empty($dstImgName)) return false;
        $allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp', '.gif'];   //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt = strrchr($dstImgName, '.');
        $sourceExt = strrchr($this->src, '.');
        if (!empty($dstExt)) $dstExt = strtolower($dstExt);
        if (!empty($sourceExt)) $sourceExt = strtolower($sourceExt);

        //有指定目标名扩展名
        if (!empty($dstExt) && in_array($dstExt, $allowImgs, true)) {
            $dstName = $dstImgName;
        } elseif (!empty($sourceExt) && in_array($sourceExt, $allowImgs, true)) {
            $dstName = $dstImgName . $sourceExt;
        } else {
            $dstName = $dstImgName . $this->imageInfo['type'];
        }
        $funcs = 'image' . $this->imageInfo['type'];
        $funcs($this->image, $dstName);
    }

    /**
     * 销毁图片
     */
    public function __destruct() {
        imagedestroy($this->image);
    }
}
