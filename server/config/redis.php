<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/4/14
 * Time: 1:17 PM
 */

return [
    'class' => 'yii\redis\Connection',
    'hostname' => getenv('redis.hostname'),
    'port' => getenv('redis.port'),
    'database' => getenv('redis.database'),
];