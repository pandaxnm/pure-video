<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "video".
 *
 * @property int $id
 * @property string $title 名称
 * @property string $poster 海报图片
 * @property string $poster_url
 * @property string $otitle 别名
 * @property string $desc 简介
 * @property string $director 导演
 * @property string $actors 主演
 * @property double $rate 评分
 * @property string $type 类型
 * @property int $year 年代
 * @property int $current_list_count 当前集数
 * @property int $total_list_count 总集数
 * @property int $time 时长(分钟)
 * @property int $views
 * @property string $area 地区
 * @property string $category 分类
 * @property int $out_id
 * @property int $created_at
 * @property int $updated_at
 * @property string $language 语言
 * @property string $source
 * @property string $note 备注
 * @property int $search_count 搜索次数
 * @property int $node_id
 */
class Video extends BaseMongo
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['desc', 'director', 'actors'], 'string'],
            [['id','year', 'current_list_count', 'total_list_count', 'views', 'created_at', 'updated_at', 'search_count', 'node_id'], 'integer'],
            [['title', 'poster', 'poster_url', 'type', 'area', 'category', 'language', 'note'], 'string', 'max' => 255],
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
            'poster',
            'poster_url',
            'desc',
            'director',
            'actors',
            'type',
            'year',
            'current_list_count',
            'total_list_count',
            'views',
            'area',
            'category',
            'created_at',
            'updated_at',
            'language',
            'note',
            'search_count',
            'node_id'
        ];
    }

    public static function insertOrUpdateVideo($data)
    {
        $model = Video::findOne(['title' => $data['title']]);
        if(!$model){
            $data['created_at'] = time();
            $data['poster_url'] = '';
            $data['search_count'] = 0;
            $data['views'] = 0;
            $data['id'] = self::increment(self::collectionName());
            $model = new Video();
            $model->setAttributes($data);
            $model->save(false);
            $vid = $data['id'];
        }else {
            $model->note = $data['note'];
            $model->save();
            $vid = $model->id;
        }
        return $vid;
    }


}
