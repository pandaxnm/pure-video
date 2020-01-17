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
class Video extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
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
            [['rate'], 'number'],
            [['year', 'current_list_count', 'total_list_count', 'time', 'views', 'out_id', 'created_at', 'updated_at', 'search_count', 'node_id'], 'integer'],
            [['title', 'poster', 'poster_url', 'otitle', 'type', 'area', 'category', 'language', 'source', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'poster' => 'Poster',
            'poster_url' => 'Poster Url',
            'otitle' => 'Otitle',
            'desc' => 'Desc',
            'director' => 'Director',
            'actors' => 'Actors',
            'rate' => 'Rate',
            'type' => 'Type',
            'year' => 'Year',
            'current_list_count' => 'Current List Count',
            'total_list_count' => 'Total List Count',
            'time' => 'Time',
            'views' => 'Views',
            'area' => 'Area',
            'category' => 'Category',
            'out_id' => 'Out ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'language' => 'Language',
            'source' => 'Source',
            'note' => 'Note',
            'search_count' => 'Search Count',
            'node_id' => 'Node Id'
        ];
    }


    public static function insertOrUpdateVideo($data)
    {
        $exists = Video::findOne(['title' => $data['title']]);
        if(!$exists){
            $data['created_at'] = time();
            $model = new Video();
            $model->setAttributes($data);
            $model->save(false);
            $vid = $model->attributes['id'];
        }else {
            Video::updateAll($data, ['id' => $exists['id']]);
            $vid = $exists['id'];
        }
        return $vid;
    }
}
