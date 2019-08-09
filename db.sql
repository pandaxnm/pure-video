# Dump of table video
# ------------------------------------------------------------

DROP TABLE IF EXISTS `video`;

CREATE TABLE `video` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '海报图片',
  `poster_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `otitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '别名',
  `desc` text COLLATE utf8mb4_unicode_ci COMMENT '简介',
  `director` text COLLATE utf8mb4_unicode_ci COMMENT '导演',
  `actors` text COLLATE utf8mb4_unicode_ci COMMENT '主演',
  `rate` float DEFAULT '0' COMMENT '评分',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型',
  `year` int(11) DEFAULT '0' COMMENT '年代',
  `current_list_count` int(11) DEFAULT '0' COMMENT '当前集数',
  `total_list_count` int(11) DEFAULT '0' COMMENT '总集数',
  `time` int(11) DEFAULT '0' COMMENT '时长(分钟)',
  `views` int(11) DEFAULT '0',
  `area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '地区',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分类',
  `out_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '语言',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `search_count` int(11) DEFAULT '0' COMMENT '搜索次数',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `updated_at` (`updated_at`),
  KEY `views` (`views`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table video_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `video_list`;

CREATE TABLE `video_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(10) unsigned NOT NULL,
  `download_url` text COLLATE utf8mb4_unicode_ci,
  `web_url` text COLLATE utf8mb4_unicode_ci,
  `play_url` text COLLATE utf8mb4_unicode_ci,
  `list_num` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '第几集',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `xianlu` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `list_num` (`list_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;