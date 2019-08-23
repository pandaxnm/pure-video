<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/29
 * Time: 8:37 PM
 */

return [
    'settings' => [
        'key' => getenv('s.aes_key'), //aes key 需与前端一致
        'iv' => getenv('s.aes_iv'), //aes iv 需与前端一致
        'index_pagesize' => getenv('s.index_pagesize'), //每页条数
        'cache_enable' => getenv('s.cache_enable'), //开启缓存
        'cache_time' => getenv('s.cache_time'), //缓存时间 分钟
        'request_limit' => getenv('s.request_limit'), //请求速率限制 次/每分钟
    ]
];