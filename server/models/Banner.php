<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banner".
 *
 * @property int $id
 * @property string $title
 * @property string $url
 * @property int $video_id
 * @property int $created_at
 */
class Banner extends BaseMongo
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'banner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['video_id', 'created_at'], 'required'],
            [['video_id', 'created_at'], 'integer'],
            [['title', 'url'], 'string', 'max' => 255],
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
            'title',
            'url',
            'video_id',
            'created_at',
        ];
    }
}
