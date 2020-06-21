<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/4/14
 * Time: 1:17 PM
 */

return [
    'class' => 'yii\redis\Connection',
    'hostname' => $_ENV['redis.hostname'],
    'port' => $_ENV['redis.port'],
    'database' => $_ENV['redis.database'],
];
