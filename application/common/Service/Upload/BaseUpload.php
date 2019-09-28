<?php

namespace app\common\Service\Upload;

abstract class BaseUpload {
    protected function needUpload(string $file): bool {
        $str = explode('/', $file);
        $fileName = $str[count($str) - 1];
        if (strpos($fileName, '_') === false) {
            return true;
        }
        return false;
    }

    abstract public function upload(string $file): void;

}
