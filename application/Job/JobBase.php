<?php

namespace app\Job;

abstract class JobBase {
    protected $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    abstract public function handle(): void ;
}
