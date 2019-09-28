<?php

namespace app\common\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use think\exception\HttpResponseException;
use think\Response;

class ResizeImageService {
    public function resize(string $filePath, bool $save = true): void {
        $picInfo = $this->getSourceImage($filePath);
        $this->canResize($picInfo['with'], $picInfo['height']);
        $realFile = $picInfo['real'];
        if (!file_exists($realFile)) {
            throw new HttpResponseException(new Response('错误的请求', 404));
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
            throw new HttpResponseException(new Response('404 Not Found', 404));
        }
        $with = $nameArr[1];
        $extArr = explode('.', $nameArr[$nameNum - 1]);
        [$height, $ext] = $extArr;
        $file = str_replace('_' . $with . '_' . $height, '', $filePath);
        return ['with' => $with, 'height' => $height, 'ext' => $ext, 'real' => $file];
    }

    private function canResize(int $width, int $height):bool {
        $str = $width.'*'.$height;
        $condition = config('mod.thumbnailSize');
        if(empty($condition)){
            throw  new HttpResponseException(new Response('错误的mod配置',400));
        }
        if(!in_array($str, $condition, true)){
            throw  new HttpResponseException(new Response('非法的请求尺寸',403));
        }
        return true;
    }
}
