<?php

namespace app\command;

//use think\Cache;
use app\common\Cache\BaseCache;
use app\Job\CompressJob;
use app\Job\ErrorJob;
use app\Job\JobBase;
use app\Job\WaterMarkJob;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Exception;
use think\facade\Cache;

class Queue extends Command {
    private function getQueueJob(): array {
        return [
            BaseCache::COMPRESS  => CompressJob::class,
            BaseCache::WATERMARK => WaterMarkJob::class
        ];
    }

    protected function configure() {
        // 指令配置
        $this->setName('queue');
        // 设置参数

    }

    protected function execute(Input $input, Output $output) {
        $redis = Cache::store('redis')->handler();
        $redis->select(9);
        while (true) {
            try {
                $data = $redis->blpop([BaseCache::COMPRESS, BaseCache::WATERMARK], 120);
                [$key, $jobData] = $data;
                $jobData = json_decode($jobData, true);
                if(!empty($jobData)){
                    switch ($key) {
                        case BaseCache::COMPRESS:
                            $classPath = $this->getQueueJob()[BaseCache::COMPRESS];
                            break;
                        case BaseCache::WATERMARK:
                            $classPath = $this->getQueueJob()[BaseCache::WATERMARK];
                            break;
                        default:
                            $classPath = ErrorJob::class;
                    }
                    /**
                     * @var JobBase $class
                     */
                    $class = new $classPath($jobData);
                    $class->handle();
                }
            } catch (Exception $exception) {
                throw $exception;
            }

        }
    }

}
