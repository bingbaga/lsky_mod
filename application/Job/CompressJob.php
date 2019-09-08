<?php

namespace app\Job;

use Exception;
use app\common\model\QueueLog;
use app\common\Service\CompressService;
use think\facade\App;

class CompressJob extends JobBase {
    public function handle(): void {

        $root = App::getRootPath() . '/public/';
        $data = $this->redis->blpop('imgCompress', 60);
        if ($data) {
            try {
                /**
                 * @var string $data
                 */
                if(is_string($data)){
                    $picInfo = json_decode($data, true);
                }else{
                    $picInfo = $data;
                }

                $source = $root . $picInfo['pathname'];
                $percent = 1;  #原图压缩，不缩放，但体积大大降低
                //echo $root . $source;
                (new CompressService($source, $percent))->compressImg($source);
                echo '压缩图片-' . $picInfo['pathname'] . '完成' . PHP_EOL;
            } catch (Exception $exception) {
                (new QueueLog())->insert(['queue_name' => self::class, 'data' => $data, 'error_msg' => $exception->getMessage()]);
                echo '压缩图片脚本出现异常，异常消息为：' . $exception->getMessage() . PHP_EOL;
            }

        }
    }
}
