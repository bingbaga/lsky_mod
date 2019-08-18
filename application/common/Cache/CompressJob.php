<?php

namespace app\common\Cache;

class CompressJob extends BaseCache {
    public function __construct() {
        parent::__construct();
    }

    public function addCompressJob(array $imgInfo): void {
        $this->redis->lpush('imgCompress', json_encode($imgInfo));
    }
}
