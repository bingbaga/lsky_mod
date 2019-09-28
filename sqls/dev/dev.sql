ALTER TABLE `dev`.`lsky_images`
    ADD COLUMN `compress_level` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '压缩等级' AFTER `create_time`;

ALTER TABLE `img`.`lsky_queue_logs`
    CHANGE COLUMN `data` `job_data` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '数据' AFTER `queue_name`;

CREATE TABLE `lsky_queue_logs`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `queue_name` varchar(255)     NOT NULL DEFAULT '' COMMENT '队列名',
    `data`       varchar(255)     NOT NULL DEFAULT '' COMMENT '数据',
    `error_msg`  text COMMENT '错误消息',
    `created_at` timestamp        NULL     DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` timestamp        NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
