<?php

namespace app\command;

use app\common\Service\SyncService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Sync extends Command {

    protected function configure() {
        // 指令配置
        $this->setName('sync');
        // 设置参数

    }

    protected function execute(Input $input, Output $output) {
        echo '开始同步本地文件和远程文件，当前时间为：' . date('Y-m-d H:i:s') . PHP_EOL;
        (new SyncService())->sync();

    }
}

