<?php

use app\common\Service\Upload\BaseUpload;
use Codeception\Test\Unit;

class UploadTest extends Unit {
    public function testFileList(): void {
        $b = (new BaseUpload())->test();
        $c = $b;
    }
}
