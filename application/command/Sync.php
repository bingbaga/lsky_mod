<?php

namespace app\command;

//use think\Cache;
use Qcloud\Cos\Client;
use Qcloud\Cos\Client as Cos;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;

class Sync extends Command {
    private $cosConnection;

    protected function configure() {
        // 指令配置
        $this->setName('sync');
        // 设置参数

    }

    protected function execute(Input $input, Output $output) {
        $cosList = $this->getCosFileList();
        $localFileList = $this->getLocalFileList();
        $tmp = array_diff($localFileList, $cosList);
        var_dump($tmp);
//        foreach ($localFileList as $local){
//            foreach ($cosList as $cos){
//
//            }
//        }
    }

    private function getCosFileList(): array {
        $bucket = $this->getConfig()['cos']['bucket'];
        $root = App::getRootPath() . '/public';
        $ret = [];
        $marker = '';
        while (true) {
            $result = $this->getCosClient()->listObjects([
                'Bucket'  => $bucket,
                'Marker'  => $marker,
                'MaxKeys' => 1000 //设置单次查询打印的最大数量，最大为1000
            ]);
            foreach ($result['Contents'] as $rt) {
                // 打印key
                if (substr($rt['Key'], -1) !== '/') {
                    $tmpPath = $root . '/' . $rt['Key'];
                    $ret[md5($tmpPath)] = $tmpPath;
                }
            }
            $marker = $result['NextMarker']; //设置新的断点
            if (!$result['IsTruncated']) {
                break; //判断是否已经查询完
            }
        }
        return $ret;
    }

    private function getLocalFileList(): array {
        $root = App::getRootPath() . '/public';
        $uploadPath = $root . '/2019';
        $folderList = $this->folderList($uploadPath);
        $ret = [];
        foreach ($folderList as $file) {
            if (substr_count($file, '.') > 0) {
                $ret[md5($file)] = $file;
            }
        }
        return $ret;
    }

    public function folderList($dir) {
        $dir .= substr($dir, -1) === '/' ? '' : '/';
        $dirInfo = [];
        foreach (glob($dir . '*') as $v) {
            $dirInfo[] = $v;
            if (is_dir($v)) {
                $tmp = $this->folderList($v);
                $dirInfo = array_merge($dirInfo, $tmp);
            }
        }
        return $dirInfo;

    }

    private function getCosClient(): Cos {
        if (empty($this->cosConnection)) {
            $config = config('mod.sync');
            /**
             * @var Client $cos
             */
            $this->cosConnection = new Cos([
                'region'      => $config['cos']['region'],
                'schema'      => 'https', //协议头部，默认为http
                'credentials' => [
                    'secretId'  => $config['cos']['secretId'],
                    'secretKey' => $config['cos']['secretKey']
                ]
            ]);
        }
        return $this->cosConnection;
    }

    private function getConfig():array {
        return config('mod.sync');
    }
}

