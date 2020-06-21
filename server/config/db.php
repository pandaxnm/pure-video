<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . $_ENV['db.host'] .';dbname=' . $_ENV['db.dbname'],
    'username' => $_ENV['db.username'],
    'password' => $_ENV['db.password'],
    'charset' => $_ENV['db.charset'],

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
