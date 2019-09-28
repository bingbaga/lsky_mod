<?php

namespace app\common\Service;

use Exception;
use GuzzleHttp\Client;
use think\facade\App;

class OneDriveService {
    public const BASE = 'https://graph.microsoft.com/v1.0';

    public function getDriverId(): array {
        $token = (new AuthGraphService(config('mod.oneDrive.appId'), config('mod.oneDrive.appSecret')))->getToken();
        $http = new Client();
        $response = $http->get(self::BASE . '/me/drive/root/children', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
        ]);
        /**
         * @var array $data
         */
        $data = json_decode($response->getBody(), true);
        return $data['id'];
    }

    public function upload($filePath): bool {
        $token = (new AuthGraphService(config('mod.oneDrive.appId'), config('mod.oneDrive.appSecret')))->getToken();
        $modFile = str_replace(App::getRootPath() . '/public', '', $filePath);
        $http = new Client();
        try {
            $response = $http->put(self::BASE . '/me/drive/root:/images' . $modFile . ':/content', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => file_get_contents($filePath)
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
        $data = json_decode($response->getBody(), true);
        if (!empty($data['id'] && !empty($data['name']))) {
            return true;
        }
        return false;
    }
}
