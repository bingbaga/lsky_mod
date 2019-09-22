<?php

namespace app\common\Service;

use Exception;
use GuzzleHttp\Client;
use Redis;
use think\facade\Cache;

class AuthGraphService {
    public const ACCESS_TOKEN = 'ACCESS_TOKEN_';
    public const REFRESH_TOKEN = 'REFRESH_TOKEN_';

    private $clientId;

    private $secret;

    /**
     * @var Redis $redisHandle
     */
    private $redisHandle;

    public function __construct(string $clientId, string $secret) {
        $this->clientId = $clientId;
        $this->secret = $secret;
    }

    public function getCodeUrl(): string {
        $url = config('mod.oneDrive.authGate') . '/authorize' . '?client_id=' . $this->clientId
            . '&response_type=code&redirect_uri=' . config('mod.oneDrive.callBack')
            . '&response_mode=query'
            . '&scope=' . config('mod.oneDrive.scope')
            . '&state=success';
        return $url;
    }

    public function getTokenByAuth(String $code): void {//id不为空，则是初始化的id,不能使用本类的id
        $http = new Client();
        $url = config('mod.oneDrive.authGate') . '/token';
        try {//todo 这里必须判断
            $response = $http->post($url, [
                'form_params' => [
                    'client_id'     => $this->clientId,
                    'scope'         => config('mod.oneDrive.scope'),
                    'code'          => $code,
                    'redirect_uri'  => config('mod.oneDrive.callBack'),
                    'grant_type'    => 'authorization_code',
                    'client_secret' => $this->secret
                ],
            ]);
        } catch (Exception  $exception) {
            var_dump($exception->getMessage());
            die();
        }
        $response = json_decode((string)$response->getBody(), true);
        if (!empty($response['token_type']) && $response['token_type'] === 'Bearer') {
            $this->cacheToken(
                $response['access_token'],
                $response['refresh_token'], $response
                ['expires_in'] - 20
            );
        }
    }

    //存储token
    private function cacheToken(string $accessToken, string $refreshToken, int $ttl): void {
        $this->getCacheHandle()->set($this->getTokenCacheKey(), $accessToken, $ttl);
        $this->getCacheHandle()->set($this->getRefreshCacheKey(), $refreshToken);
    }

//通过freshToken 获取最新的token
    public function getTokenByFresh() {
        $fresh = $this->getCacheHandle()->get($this->getRefreshCacheKey());
        if (!$fresh) {
            return json_encode(['msg' => '错误，该oneDrive未授权']);
        }
        $http = new Client();
        $url = config('mod.oneDrive.authGate') . '/token';
        try {
            $response = $http->post($url, [
                'form_params' => [
                    'client_id'     => $this->clientId,
                    'scope'         => config('mod.oneDrive.scope'),
                    'refresh_token' => $fresh,
                    'redirect_uri'  => config('mod.oneDrive.callBack'),
                    'grant_type'    => 'refresh_token',
                    'client_secret' => $this->secret
                ],
            ]);
            $response = json_decode((string)$response->getBody(), true);
            if (isset($response['token_type']) && $response['token_type'] === 'Bearer') {
                $this->cacheToken($response['access_token'], $response['refresh_token'], $response['expires_in'] - 20);
                return $response['access_token'];
            }
            return false;
        } catch (Exception $exception) {
            return json_encode(['msg' => $exception->getMessage()]);
        }


    }

    public function getToken() {
        $token = $this->getCacheHandle()->get($this->getTokenCacheKey());
        if (empty($token)) {
            return $this->getTokenByFresh();
        }
        return $token;
    }

    private function getCacheHandle(): Redis {
        if (empty($this->redisHandle)) {
            $this->redisHandle = Cache::store('redis')->handler();
            $this->redisHandle->select(9);
        }
        return $this->redisHandle;
    }

    private function getRefreshCacheKey(): string {
        return self::REFRESH_TOKEN . $this->clientId;
    }

    private function getTokenCacheKey(): string {
        return self::ACCESS_TOKEN . $this->clientId;
    }


}
