<?php

namespace app\Job;

use app\common\Service\CompressService;
use think\facade\App;
use think\facade\Cache;

class CompressJob extends JobBase {
    public function handle(): void {

        $root = App::getRootPath() . '/public/';
        $redis = Cache::store('redis')->handler();
        $redis->select(9);
        $data = $redis->lpop('imgCompress');
        if ($data) {
            /**
             * @var string $data
             */
            $picInfo = json_decode($data, true);
            $source = $root . $picInfo['pathname'];
            $percent = 1;  #原图压缩，不缩放，但体积大大降低
            //echo $root . $source;
            (new CompressService($source, $percent))->compressImg($source);
            echo '处理-' . $picInfo['pathname'] . '完成' . PHP_EOL;
        }
    }
}
