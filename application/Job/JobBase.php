<?php

namespace app\Job;


use Redis;

abstract class JobBase {
    protected $data;

    /**
     * @var Redis $redis
     */
    protected $redis;

    public function __construct(array $data, $redis) {
        $this->data = $data;
        $this->redis = $redis;
    }

    abstract public function handle(): void ;
}
