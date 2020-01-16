<?php
/**
 * Created by PhpStorm.
 * User: xiongchaojie
 * Date: 2020-01-16
 * Time: 10:59
 */


namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;
use yii\mongodb\ActiveRecord;

class BaseMongo extends ActiveRecord
{

    /**
     * @param $msg
     * @param int $code
     * @throws \Exception
     */
    public static function throwException($msg, $code = 1)
    {
        throw new \Exception($msg, $code);
    }


    /**
     * @return mixed
     */
    public static function increment($tablename)
    {
        $coll = Yii::$app->mongodb->getCollection("ids");
        $result = $coll->findandmodify(
            ['name' => $tablename],
            ['$inc' => ['id' => 1]],
            ['upsert' => true, 'fields' => 'id', 'new' => true]
        );
        return $result['id'];
    }
}
