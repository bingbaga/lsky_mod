<?php

namespace app\Job;


use Redis;

abstract class JobBase {
    protected $jobData;

    public function __construct(array $jobData) {
        $this->jobData = $jobData;
    }

    abstract public function handle(): void;
}
