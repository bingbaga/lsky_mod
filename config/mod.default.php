<?php

use think\facade\App;

return [
    'waterMark'          => 'echo少年 echoteen.com',//水印文字
    'waterMarkFontColor' => '#FF83FA',//水印字体颜色
    'saveThumbnail'      => 'true',//缩略图是否存储
    'thumbnailSize'      => ['280*210', '530*200'],//缩略图裁剪尺寸集合
    'rootPath'           => App::getRootPath() . '/public/upload/',//图片的上传目录，保持
    'syncPath'           => [
        'upload',//系统的默认上传目录
        '2019'//兼容其他的上传目录
    ],
    'syncSource'         => ['COS', 'ONEDRIVE'],//资源同步的方式，目前支持这两个COS表示腾讯云cos,ONEDRIVE表示onedrive学生版，其他没测试
    'sync'               => [
        'cos' => [
            'id'        => '111',//密钥id
            'region'    => 'ap-shanghai',//地区，去看腾讯云的信息填写
            'secretId'  => '',
            'secretKey' => '',
            'bucket'    => ''
        ]
    ],
    'oneDrive'           => [
        'graphGate' => 'https://graph.microsoft.com/v1.0',//graph授权终结点
        'authGate'  => '',//授权网关
        'scope'     => 'offline_access Files.ReadWrite',//保持
        'callBack'  => 'https://yourdomain.com/callback',//回调地址
        'appId'     => '',//clientId
        'appSecret' => ''//密钥
    ],

];
