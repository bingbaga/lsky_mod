<?php

namespace app\index\controller;

use app\common\Service\AuthGraphService;
use think\Controller;
use think\Request;

class Callback extends Controller {
    public function index() {
        return 'ttt';
    }

    public function getAuth() {
        $url = (new AuthGraphService(config('mod.oneDrive.appId'), config('mod.oneDrive.appSecret')))->getCodeUrl();
//        var_dump($url);
//        die();
        return redirect($url);
    }

    public function callBack(Request $request) {//code,state=success
        $code = $request->get('code');
        if (empty($code)) {
            return json_encode(['msg' => 'auth failed']);
        }
//        var_dump($code);
//        die();
        (new AuthGraphService(config('mod.oneDrive.appId'), config('mod.oneDrive.appSecret')))->getTokenByAuth($code);
        return json_encode(['msg' => 'auth success']);
    }
}
