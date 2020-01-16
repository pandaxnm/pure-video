-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- 主机： 
-- 生成日期： 2020-01-16 10:39:11
-- 服务器版本： 5.6.30
-- PHP 版本： 7.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `video`
--

-- --------------------------------------------------------

--
-- 表的结构 `banner`
--

CREATE TABLE `banner` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `video_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `navi`
--

CREATE TABLE `navi` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort` int(11) DEFAULT '99'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `navi`
--

INSERT INTO `navi` (`id`, `name`, `sort`) VALUES
(1, '电影', 2),
(2, '电视剧', 3),
(3, '动漫', 4),
(4, '综艺', 5),
(5, '最近', 1);

-- --------------------------------------------------------

--
-- 表的结构 `node`
--

CREATE TABLE `node` (
  `id` int(11) UNSIGNED NOT NULL,
  `navi_id` int(11) DEFAULT '0',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort` int(11) DEFAULT '99'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `node`
--

INSERT INTO `node` (`id`, `navi_id`, `name`, `sort`) VALUES
(22, 2, '连续剧', 99),
(21, 4, '综艺片', 99),
(20, 3, '动漫片', 99),
(5, 1, '动作片', 99),
(6, 1, '喜剧片', 99),
(7, 1, '爱情片', 99),
(8, 1, '科幻片', 99),
(9, 1, '恐怖片', 99),
(10, 1, '剧情片', 99),
(11, 1, '战争片', 99),
(12, 1, '记录片', 99),
(13, 2, '国产剧', 99),
(14, 2, '香港剧', 99),
(15, 2, '韩国剧', 99),
(16, 2, '欧美剧', 99),
(17, 2, '台湾剧', 99),
(18, 2, '日本剧', 99),
(19, 2, '海外剧', 99),
(23, 3, '国产动漫', 99),
(24, 3, '日韩动漫', 99),
(25, 3, '欧美动漫', 99),
(26, 3, '海外动漫', 99),
(27, 3, '港台动漫', 99),
(28, 4, '内地综艺', 99),
(29, 4, '日韩综艺', 99),
(30, 4, '欧美综艺', 99),
(31, 3, '动漫', 99),
(32, 2, '台剧', 99),
(33, 2, '日剧', 99),
(34, 2, '泰剧', 99);

-- --------------------------------------------------------

--
-- 表的结构 `video`
--

CREATE TABLE `video` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `node_id` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `video_list`
--

CREATE TABLE `video_list` (
  `id` int(11) UNSIGNED NOT NULL,
  `video_id` int(10) UNSIGNED NOT NULL,
  `download_url` text COLLATE utf8mb4_unicode_ci,
  `web_url` text COLLATE utf8mb4_unicode_ci,
  `play_url` text COLLATE utf8mb4_unicode_ci,
  `list_num` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '第几集',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `xianlu` text COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `navi`
--
ALTER TABLE `navi`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`(250)),
  ADD KEY `updated_at` (`updated_at`),
  ADD KEY `views` (`views`);

--
-- 表的索引 `video_list`
--
ALTER TABLE `video_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `list_num` (`list_num`(250));

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `navi`
--
ALTER TABLE `navi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `node`
--
ALTER TABLE `node`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- 使用表AUTO_INCREMENT `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `video_list`
--
ALTER TABLE `video_list`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
