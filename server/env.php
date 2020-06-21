<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/8/23
 * Time: 4:52 PM
 */

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

defined('YII_DEBUG') or define('YII_DEBUG', $_ENV['YII_DEBUG'] === 'true');
defined('YII_ENV') or define('YII_ENV', $_ENV['YII_ENV'] ?: 'prod');
