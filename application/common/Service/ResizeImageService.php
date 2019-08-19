<?php

namespace app\common\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use RuntimeException;

class ResizeImageService {
    public function resize(string $filePath, bool $save = true): void {
        $picInfo = $this->getSourceImage($filePath);
        $realFile = $picInfo['real'];
        if (!file_exists($realFile)) {
            throw new RuntimeException('错误,源图片不存在，错误图片：' . $realFile);
        }
        $imagine = new Imagine();
        $pic = $imagine->open($realFile);
        $boxSize = new Box($picInfo['with'], $picInfo['height']);
        $pic->resize($boxSize);
        if($save){
            $pic->save($filePath);
        }
        $pic->show($picInfo['ext']);
        exit(0);
    }

    private function getSourceImage(string $filePath): array {
        //$file = '/path/to/name#3#4.jpg'
        $pathArr = explode('/', $filePath);
        $num = count($pathArr);
        $fileName = $pathArr[$num - 1];
        $nameArr = explode('_', $fileName);// /path/to/name 3 4.jpg
        $nameNum = count($nameArr);
        if($nameNum === 1){
            throw new RuntimeException('错误的裁剪格式，使用#分隔');
        }
        $with = $nameArr[1];
        $extArr = explode('.', $nameArr[$nameNum - 1]);
        [$height, $ext] = $extArr;
        $file = str_replace('_' . $with . '_' . $height, '', $filePath);
        return ['with' => $with, 'height' => $height, 'ext' => $ext, 'real' => $file];

    }
}
