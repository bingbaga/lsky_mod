<?php

namespace app\common\Service;

use Qcloud\Cos\Client;
use Qcloud\Cos\Client as Cos;
use RuntimeException;

class UploadConnection {
    private static $connection = [];

    private static $instance;

    public static function getInstance(): self {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(string $instance) {
        switch ($instance) {
            case 'COS':
                $ret = $this->createOssInstance();
                break;
            default:
                throw new RuntimeException('错误，获取不到该实例');
        }
        return $ret;
    }

    private function createOssInstance() {
        if (empty(self::$connection['cos'])) {
            $config = config('mod.sync');
            /**
             * @var Client $cos
             */
            self::$connection['cos'] = new Cos([
                'region'      => $config['cos']['region'],
                'schema'      => 'https', //协议头部，默认为http
                'credentials' => [
                    'secretId'  => $config['cos']['secretId'],
                    'secretKey' => $config['cos']['secretKey']
                ]
            ]);
        }
        return self::$connection['cos'];
    }
}
