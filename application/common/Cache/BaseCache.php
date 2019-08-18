<?php
namespace app\common\Cache;

use think\facade\Cache;

class BaseCache{
    protected $redis;
    public function __construct() {
        $this->redis = Cache::store('redis')->handler();
        $this->redis->select(9);
    }
}
