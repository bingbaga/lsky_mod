<?php

namespace app\command;

//use think\Cache;
use app\Job\CompressJob;
use app\Job\JobBase;
use app\Job\WaterMarkJob;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Exception;

class Queue extends Command {
    private function getQueueJob(): array {
        return [
            [
                'class' => CompressJob::class,
                'data'  => []
            ],
            [
                'class' => WaterMarkJob::class,
                'data'  => []
            ]
        ];
    }

    protected function configure() {
        // 指令配置
        $this->setName('queue');
        // 设置参数

    }

    protected function execute(Input $input, Output $output) {
        // 指令输出
        //$output->writeln('compress');
        while (true) {
            foreach ($this->getQueueJob() as $job) {
                try {
                    $class = new $job['class']($job['data']);
                    /**
                     * @var JobBase $class
                     */
                    $class->handle();
                } catch (Exception $exception) {
                    echo 'queue error: ' . $exception->getMessage() . PHP_EOL;
                }

            }
        }
    }

}
