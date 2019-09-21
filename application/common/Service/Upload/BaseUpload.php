<?php

namespace app\common\Service\Upload;

abstract class BaseUpload {

    abstract public function upload(string $file): void;

}
