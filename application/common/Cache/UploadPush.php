<?php

namespace app\common\Cache;

/**
 * 文件同步队列
 * Class UploadPush
 * @package app\common\Cache
 */
class UploadPush extends BaseCache {
    public function __construct() {
        parent::__construct();
    }

    public function upload(string $filePath, string $source): void {
        $this->redis->lpush(self::UPLOAD, json_encode(['source' => $source, 'file' => $filePath]));
    }
}
