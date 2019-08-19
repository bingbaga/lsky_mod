<?php

namespace app\Job;

use think\facade\App;
use think\facade\Cache;

class ThumbnailJob extends JobBase {
    private $with;

    public function handle(): void {
        $root = App::getRootPath() . '/public/';
        $redis = Cache::store('redis')->handler();
        $redis->select(9);
        $data = $redis->lpop('thumbnail');
        if ($data) {
            $info = json_decode($data, true);
            $source = $root . $info['pathname'];
            $pathArr = explode('/', $info['pathname']);
            $num = count($pathArr);
            $thumbnailPath = '';
            for ($i = 0; $i < $num; $i++) {
                if($i === $num-1){//说明到了文件名
                    $pathArr[$i] = 'thumb_'.$pathArr[$i];
                }
            }
            $thumbnailSrc = $root . 'thumbnail/' . $info['pathname'];
        }
    }
}
