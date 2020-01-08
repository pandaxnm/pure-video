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
class Node extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'navi_id' => 'Navi ID',
            'name' => 'Name',
            'sort' => 'Sort',
        ];
    }

    public function getNavi()
    {
        return $this->hasOne(Navi::className(), ['navi_id' => 'id']);
    }
}
