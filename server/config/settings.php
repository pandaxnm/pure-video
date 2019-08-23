<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/29
 * Time: 8:37 PM
 */

return [
    'settings' => [
        'index_pagesize' => getenv('s.index_pagesize'), //每页条数
        'cache_enable' => getenv('s.cache_enable'), //开启缓存
        'cache_time' => getenv('s.cache_time'), //缓存时间 分钟
        'request_limit' => getenv('s.request_limit'), //请求速率限制 次/每分钟
    ]
];