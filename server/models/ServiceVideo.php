<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/29
 * Time: 8:30 PM
 */
namespace app\models;

use Yii;
use yii\caching\DbDependency;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ServiceVideo extends \yii\db\ActiveRecord{

    private $settings;

    public function __construct(array $config = [])
    {
        $this->settings = Yii::$app->params['settings'];
        parent::__construct($config);
    }

    /**
     * 首页视频
     * @return array|mixed
     */
    public function getIndexVideo($naviId)
    {
        if($naviId == 5){
            $where = [];
        }else{
            $nodes = Navi::getNaviNodes($naviId);
            $nodeIds = ArrayHelper::getColumn($nodes, 'id');

            $where = ['in', 'node_id', $nodeIds];
        }

        $query = Video::find();
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->where($where)->count(Video::tableName() . '.id'),
            'pageSize' => intval($this->settings['index_pagesize']),
            'pageParam' => 'p'
        ]);

        $key = 'video-index-' . '-' . $naviId . '-' . $pages->getPage();
        $cache = Yii::$app->getCache();
        $totalPage = ceil($pages->totalCount / $this->settings['index_pagesize']);

        if(!$this->settings['cache_enable'] || !$data = $cache->get($key)){
            $videos = $query
                ->where($where)
                ->orderBy(['updated_at' => SORT_DESC,'created_at'=>SORT_DESC])
//                ->orderBy(['id' => SORT_DESC])
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->asArray()
                ->all();

            foreach ($videos as &$video) {
                if($video['poster_url']) {
                    $video['poster'] = getenv('domain') . $video['poster_url'];
                }
            }

            $data = [
                'list' => $videos,
                'totalCount' => $pages->totalCount,
                'totalPage' => $totalPage,
            ];

            $dep = new DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '. Video::tableName()]);
            $cache->set($key, $data, intval($this->settings['cache_time'])*60, $dep);
        }

        return $data;
    }


    /**
     * 视频详情
     * @param $id
     * @return array|mixed
     */
    public function getVideoDetail($id, $from = '')
    {
        $videoInfo = Video::find()->where(['id' => $id])->asArray()->one();
        if(!$videoInfo){
            return [];
        }

        if($videoInfo['poster_url']) {
            $videoInfo['poster'] = getenv('domain') . $videoInfo['poster_url'];
        }

        $key = 'video-detail-' . $id;
        $cache = Yii::$app->getCache();

        if(!$this->settings['cache_enable'] || !$data = $cache->get($key)) {
            //获取剧集
            $list = VideoList::find()
                ->where(['video_id' => $id])
                ->orderBy(['id' => SORT_ASC])
                ->asArray()
                ->all();
            $newList = [];
            foreach ($list as $v){
                $newList[$v['xianlu']][] = $v;
            }
            $data = [
                'detail' => $videoInfo,
                'list' => array_values($newList),
            ];
            $dep = new DbDependency(['sql'=>'SELECT updated_at,current_list_count FROM '. Video::tableName()]);
            $cache->set($key, $data, intval($this->settings['cache_time'])*60, $dep);
        }

        $updateCounters = [
            'views' => 1,
        ];
        if($from == 'search'){
            $updateCounters['search_count'] = 1;
        }
        Video::updateAllCounters($updateCounters, ['id' => $data['detail']['id']]);

        return $data;
    }


    /**
     * 获取播放信息
     * @param $id
     * @param $listNum
     * @return array|mixed
     */
    public function getPlayInfo($id, $listNum)
    {
        $key = 'video-play-info-' . $id;
        $cache = Yii::$app->getCache();

        if(!$this->settings['cache_enable'] || !$data = $cache->get($key)){
            $detail = $this->getVideoDetail($id);
            $lines = VideoList::find()
                ->where(['video_id' => $id, 'list_num' => $listNum])
                ->orderBy(['list_num' => SORT_ASC])
                ->asArray()
                ->all();

            $data = [
                'info' => $detail,
                'lines' => $lines
            ];
            $dep = new DbDependency(['sql'=>'SELECT MAX(updated_at),SUM(list_num) FROM '. VideoList::tableName() . ' where video_id=' . $id]);
            $cache->set($key, $data, intval($this->settings['cache_time'])*60, $dep);
        }


        return $data;
    }


    /**
     * 搜索
     * @param $keyword
     * @return array
     */
    public function search($keyword)
    {
        $query = Video::find()->where(['like','title',$keyword]);
        $countQuery = clone $query;

        $pages = new Pagination([
            'totalCount' => $countQuery->count(Video::tableName() . '.id'),
            'pageSize' => intval($this->settings['index_pagesize']),
            'pageParam' => 'p'
        ]);

        $totalPage = ceil($pages->totalCount / $this->settings['index_pagesize']);

        $videos = $query->orderBy(['updated_at' => SORT_DESC])
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        $data = [
            'list' => $videos,
            'totalCount' => $pages->totalCount,
            'totalPage' => $totalPage,
        ];

        return $data;
    }

    /**
     * 获取热门搜索
     * @param int $limit
     * @return array
     */
    public function getHotList()
    {
        $key = 'hot-list';
        $cache = Yii::$app->getCache();

        if(!$this->settings['cache_enable'] || !$data = $cache->get($key)) {
            $videos = Video::find()
                ->select(['id', 'title', 'search_count'])
                ->where(['>', 'search_count', 0])
                ->orderBy(['search_count' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();

            $data = [
                'list' => $videos,
            ];

            $cache->set($key, $data, intval($this->settings['cache_time'])*60);
        }

        return $data;
    }


    /**
     * 获取所有分类
     * @return array|mixed
     */
    public function getCategories()
    {
        $key = 'video-categories';
        $cache = Yii::$app->getCache();

        if(!$this->settings['cache_enable'] || !$data = $cache->get($key)) {
            $query = new Query();
            $categories = $query->select(['category'])->from('video')->groupBy(['category'])->all();
            $categories = ArrayHelper::getColumn($categories, 'category');

            $data = [];
            array_unshift($categories, '全部');
            $id = 1;
            foreach ($categories as $category){
                $data[] = [
                    'id' => $id,
                    'name' => $category,
                    'value' => $category
                ];
                $id ++;
            }

            $dep = new DbDependency(['sql'=>'SELECT MAX(updated_at) FROM '. VideoList::tableName()]);
            $cache->set($key, $data, intval($this->settings['cache_time'])*60, $dep);
        }

        return $data;
    }

    public  function getBanners()
    {
        $key = 'video-banners';
        $cache = Yii::$app->getCache();

        if(!$this->settings['cache_enable'] || !$data = $cache->get($key)) {

            $data = Banner::find()->asArray()->all();

            $dep = new DbDependency(['sql'=>'SELECT MAX(created_at) FROM '. Banner::tableName()]);
            $cache->set($key, $data, intval($this->settings['cache_time'])*60, $dep);
        }

        return $data;
    }

}
