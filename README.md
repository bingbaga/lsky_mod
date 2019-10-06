魔改功能
---

- 大都功能队列化，减少请求时间
- 自动水印
- 自动缩略图，静态化，缓存本地，资源可以被CDN cache到，不带参数
- 本地存储支持分布式备份

  同时针对腾讯云COS和微软oneDrive增量备份，后续增加Google等
- 支持定时检测配置的备份数据是否遗漏，则推给队列

教程
---
1. 博客地址：https://www.echoteen.com/lsky-mode-release-intro.html
2. 如有疑问可以直接博客评论或者issue提出

主要特性
---
- 支持第三方云储存，支持本地、阿里云 OSS、腾讯云 COS、七牛云、又拍云。
- 支持多图上传、拖拽上传、上传预览、全屏预览、页面响应式布局。
- 简洁的图片管理功能，支持鼠标右键、单选多选等操作。
- 强大的图片预览功能，支持响应式。
- 支持全局配置用户初始剩余储存空间、支持单个设置用户剩余储存空间。
- 支持一键复制图片外链、二维码扫描链接。
- 支持设置上传文件、文件夹路径命名规则。
- 支持图片鉴黄功能。
- 支持文件夹分类功能。
- 对外开放的上传接口。

安装需求
---
* PHP 必须php7.1+
* mysql 版本 &ge; 5.5
* mysqli 支持
* fileinfo 拓展
* curl 拓展
* rewrite
* 严重依赖redis,不适请离开

安装教程
---
1. 下载兰空，上传至 web 运行环境，解压。
2. 设置运行目录为 public。
3. 配置 Rewrite 规则：
    ##### Nginx：
    ```
    location / {
        if (!-e $request_filename) {
        	rewrite ^(.*)$ /index.php?s=$1 last; break;
        }
    }
    ```

    ##### Apache:
    Apache 直接使用 .htaccess 即可

4. 访问首页，未安装自动跳转至安装页面，根据页面提示安装即可。
5. 安装完成以后请设置 runtime 目录0755权限，如果你使用本地存储，public 目录也需要设置为0755权限

如何更新到最新版？
---
- 直接在软件目录执行git pull即可更新到最新版

鸣谢
---
- ThinkPHP
- Jquery
- BootStrap
- Mdui
- viewer.js
- context.js

开源许可
---
[GPL 3.0](https://opensource.org/licenses/GPL-3.0)

原作者开源地址
- https://github.com/wisp-x/lsky-pro

Copyright (c) 2018-present Lsky Pro.
