<?php

namespace app\common\Service\Upload;

use app\common\Service\OneDriveService;
use Exception;

class OnedriveUpload extends BaseUpload {
    public function upload(string $file): void {
        try {
            (new OneDriveService())->upload($file);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
