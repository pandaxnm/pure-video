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
class VideoList extends BaseMongo
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
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

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'id',
            'video_id',
            'download_url',
            'web_url',
            'play_url',
            'list_num',
            'created_at',
            'updated_at',
            'views',
            'xianlu',
        ];
    }



    public static function insertOrUpdateList($params)
    {
        $listExists = VideoList::findOne(['video_id' => (int)$params['video_id'], 'list_num' => $params['list_num'], 'xianlu'=>$params['xianlu']]);

        if(!$listExists){
            $params['id'] = self::increment(self::collectionName());
            $params['created_at'] = time();
            $listModel = new VideoList();
            $listModel->setAttributes($params);
            $listModel->save(false);
        }else{
            VideoList::updateAll(['play_url' => $params['play_url']],['id' => $listExists->id]);
        }
    }
}
//db.video.createIndex({"id":1})
//db.video.createIndex({"node_id":1})
//db.video.createIndex({"title":1})
//db.video.createIndex({"created_at":1})
//db.video.createIndex({"updated_at":1})
//db.video.createIndex({"search_count":1})
//
//db.video_list.createIndex({"id":1})
//db.video_list.createIndex({"video_id":1})
//db.video_list.createIndex({"list_num":1})
//db.video_list.createIndex({"created_at":1})
//db.video_list.createIndex({"updated_at":1})