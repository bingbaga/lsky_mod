<?php

namespace app\Job;

use app\common\model\QueueLog;
use app\common\Service\Upload\CosUpload;
use app\common\Service\Upload\OnedriveUpload;
use Exception;

class UploadJob extends JobBase {

    public function handle(): void {
        try{
            if ($this->jobData['source'] === 'COS') {
                echo '开始上传到腾讯云COS，文件地址：' . $this->jobData['file'] . PHP_EOL;
                (new CosUpload())->upload($this->jobData['file']);
                echo $this->jobData['file'] . '----上传成功' . PHP_EOL;
            }

            if($this->jobData['source'] === 'ONEDRIVE'){
                echo '开始上传到onedrive，文件地址：' . $this->jobData['file'] . PHP_EOL;
                (new OnedriveUpload())->upload($this->jobData['file']);
                echo $this->jobData['file'] . '----上传成功' . PHP_EOL;
            }
        }catch(Exception $exception){
            (new QueueLog())->insert(['queue_name' => self::class, 'job_data' => json_encode($this->jobData), 'error_msg' => $exception->getMessage()]);
            echo 'upload出现异常，异常消息为：' . $exception->getMessage() . PHP_EOL;
        }

    }
}
