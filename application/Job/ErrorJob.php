<?php

namespace app\Job;

class ErrorJob extends JobBase {

    public function handle(): void {
        echo 'error!';
    }
}
