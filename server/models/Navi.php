<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "navi".
 *
 * @property int $id
 * @property string $name
 * @property int $sort
 */
class Navi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'navi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort'], 'integer'],
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
            'name' => 'Name',
            'sort' => 'Sort',
        ];
    }

    public function getNodes()
    {
        return $this->hasMany(Node::className(), ['id' => 'navi_id']);
    }

    public static function getNavis()
    {
        $model = Navi::find()->orderBy(['sort' => SORT_ASC])->asArray()->all();
        return $model;
    }

    public static function getNaviNodes($id)
    {
        $model = Node::find()->where(['navi_id' => $id])->asArray()->all();
        return $model;
    }
}
