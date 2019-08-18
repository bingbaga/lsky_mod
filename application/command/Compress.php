<?php

namespace app\command;

//use think\Cache;
use app\common\Service\CompressService;
use think\facade\App;
use think\facade\Cache;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Compress extends Command {
    protected function configure() {
        // 指令配置
        $this->setName('compress');
        // 设置参数

    }

    protected function execute(Input $input, Output $output) {
        // 指令输出
        //$output->writeln('compress');
        $root = App::getRootPath() . '/public/';
        $redis = Cache::store('redis')->handler();
        $redis->select(9);
        while (true) {
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

}
