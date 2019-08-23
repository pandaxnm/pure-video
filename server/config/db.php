<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv('db.host') .';dbname=' . getenv('db.dbname'),
    'username' => getenv('db.username'),
    'password' => getenv('db.password'),
    'charset' => getenv('db.charset'),

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
