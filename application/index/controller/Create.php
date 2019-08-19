<?php

namespace app\index\controller;

use app\common\Service\ResizeImageService;
use Imagine\Gd\Imagine;
use think\Controller;
use think\facade\App;
use think\Request;

/**
 * Class Create 图片裁剪
 * @package app\index\controller
 */
class Create extends Controller {
    public function index(Request $request): void {
        $root = App::getRootPath() . '/public/';
        $file = $root . $request->url();
        if (!file_exists($file)) {
            (new ResizeImageService())->resize($file);
        }
        $imagine = new Imagine();
        $imagine->open($file)->show($file);
    }
}
