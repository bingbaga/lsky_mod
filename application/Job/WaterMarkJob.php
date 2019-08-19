<?php

namespace app\Job;

use app\common\model\QueueLog;
use Exception;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use think\facade\App;
use think\facade\Cache;
use Imagine\Image\Color;

class WaterMarkJob extends JobBase {
    public $text = 'echo少年 echoteen.com';
    public const TITLE_FONT_SIZE = 50;
    public const COLOR = '#FF83FA';//十六进制颜色

    public function handle(): void {
        $root = App::getRootPath() . '/public/';
        $redis = Cache::store('redis')->handler();
        $redis->select(9);
        $data = $redis->lpop('imgWaterMark');
        if ($data) {
            try {
                $picInfo = json_decode($data, true);
                $this->addWaterMark($root . $picInfo['pathname']);
                echo '水印图片-' . $picInfo['pathname'] . '完成' . PHP_EOL;
            } catch (Exception $exception) {
                (new QueueLog())->insert(['queue_name' => self::class, 'data' => $data, 'error_msg' => $exception->getMessage()]);
                echo '水印脚本出现异常，异常消息为：' . $exception->getMessage() . PHP_EOL;
            }

        }
    }

    private function addWaterMark(string $imgSource): void {
        $imagine = new Imagine();
        $pic = $imagine->open($imgSource);
        $color = new Color(self::COLOR);
        $titleFont = $imagine->font($this->getFontPath(), self::TITLE_FONT_SIZE, $color);
        $textBox = $titleFont->box($this->text);
        $fontWith = $textBox->getWidth();
        $point = new Point($pic->getSize()->getWidth() - $fontWith - 20, $pic->getSize()->getHeight() - $textBox->getHeight() - 20);
        $pic->draw()->text($this->text, $titleFont, $point);
        $pic->save($imgSource);
    }

    private function getFontPath(): string {
        return App::getRootPath() . '/public/static/font.ttf';
    }
}
