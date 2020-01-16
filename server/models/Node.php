<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "node".
 *
 * @property int $id
 * @property int $navi_id
 * @property string $name
 * @property int $sort
 */
class Node extends BaseMongo
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'node';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['navi_id', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'id',
            'navi_id',
            'name',
            'sort',
        ];
    }

    public function getNavi()
    {
        return $this->hasOne(Navi::className(), ['navi_id' => 'id']);
    }
}
