<?php

namespace app\common\Cache;

use think\facade\Cache;

class BaseCache {
    public const COMPRESS = 'imgCompress';
    public const WATERMARK = 'imgWaterMark';
    //
    public const UPLOAD = 'UPLOAD';
    protected $redis;

    public function __construct() {
        $this->redis = Cache::store('redis')->handler();
        $this->redis->select(9);
    }
}
