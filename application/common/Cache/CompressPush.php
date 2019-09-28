<?php

namespace app\common\Cache;

class CompressPush extends BaseCache {

    public function __construct() {
        parent::__construct();
    }

    public function addCompressJob(array $imgInfo): void {
        $this->redis->lpush(self::COMPRESS, json_encode($imgInfo));
        $this->redis->lpush(self::WATERMARK, json_encode($imgInfo));
    }
}
