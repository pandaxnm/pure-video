<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "video_list".
 *
 * @property int $id
 * @property int $video_id
 * @property string $download_url
 * @property string $web_url
 * @property string $play_url
 * @property int $list_num 第几集
 * @property int $created_at
 * @property int $updated_at
 * @property int $views
 * @property string $xianlu
 */
class VideoList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'video_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['video_id'], 'required'],
            [['video_id', 'list_num', 'created_at', 'updated_at', 'views'], 'integer'],
            [['download_url', 'web_url', 'play_url', 'xianlu'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'video_id' => 'Video ID',
            'download_url' => 'Download Url',
            'web_url' => 'Web Url',
            'play_url' => 'Play Url',
            'list_num' => 'List Num',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'views' => 'Views',
            'xianlu' => 'Xianlu',
        ];
    }
}
