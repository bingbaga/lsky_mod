<?php

namespace app\common\Service\Upload;

use app\common\Service\UploadConnection;
use Exception;
use think\facade\App;

class CosUpload extends BaseUpload {
    public function upload(string $file): void {
        if (!$this->needUpload($file)) {
            return;
        }
        $oss = UploadConnection::getInstance()->getConnection('COS');
        $modFile = str_replace(App::getRootPath() . '/public', '', $file);
        $bin = file_get_contents($file);
        try {
            $oss->putObject([
                'Bucket' => config('mod.sync')['cos']['bucket'], //格式：BucketName-APPID
                'Key'    => $modFile,
                'Body'   => $bin,
            ]);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
