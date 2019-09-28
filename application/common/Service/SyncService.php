<?php

namespace app\common\Service;

use app\common\Cache\UploadPush;
use think\facade\App;

class SyncService {

    public function sync(): void {
        $cosList = $this->getCosFileList();
        $localFileList = $this->getLocalFileList();
        $tmpList = array_diff($localFileList, $cosList);
        echo '当前有：' . count($tmpList) . '个文件需要上传同步';
        $queue = new UploadPush();
        foreach ($tmpList as $local) {
            $sourceList = config('mod.syncSource');
            if (!empty($sourceList)) {
                foreach ($sourceList as $source) {
                    $queue->upload($local, $source);
                }
            }

        }
    }

    private function getLocalFileList(): array {
        $root = App::getRootPath() . '/public';
        $uploadKey = config('mod.syncPath');
        $ret = [];
        if (empty($uploadKey)) {
            return [];
        }
        foreach ($uploadKey as $upload) {
            $uploadPath = $root . '/' . $upload;
            $folderList = $this->folderList($uploadPath);
            foreach ($folderList as $file) {
                if (substr_count($file, '.') > 0) {
                    $ret[md5($file)] = $file;
                }
            }
        }
        return $ret;
    }

    private function getCosFileList(): array {
        $bucket = $this->getConfig()['cos']['bucket'];
        $root = App::getRootPath() . '/public';
        $ret = [];
        $marker = '';
        while (true) {
            $result = UploadConnection::getInstance()->getConnection('COS')->listObjects([
                'Bucket'  => $bucket,
                'Marker'  => $marker,
                'MaxKeys' => 1000 //设置单次查询打印的最大数量，最大为1000
            ]);
            if (empty($result['Contents'])) {
                return [];
            }
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

    private function folderList($dir): array {
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

    private function getConfig(): array {
        return config('mod.sync');
    }
}
