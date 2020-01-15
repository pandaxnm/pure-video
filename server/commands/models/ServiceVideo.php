<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/8/7
 * Time: 3:59 PM
 */

namespace app\commands\models;

use app\models\Node;
use app\models\Utils;
use Yii;
use app\models\Video;
use app\models\VideoList;
use yii\helpers\Console;

class ServiceVideo{


    public function collectVideo($url)
    {
        $p = 1;

        while (true) {
            $ids = $this->getVideoIds($url, $p);
            if(!$ids) break;

            $this->getVideoList($url, $ids);
            $p++;
        }
    }

    /**
     * 获取影片列表
     * @param $url
     * @param int $p
     * @param bool $foreUpdate
     */
    public function getVideoIds($url, $p = 1, $foreUpdate = false)
    {
        $videosUrl = $url . "?ac=list&pg=%d";

        $newUrl = sprintf($videosUrl, $p);
        $data = file_get_contents($newUrl);
        $xml = @simplexml_load_string($data);
        if(empty($xml)) {
            Console::output('XML格式不正确，不支持采集');
            return [];
        }

        $ids = [];
        foreach ($xml->list->video as $video) {
            if(strpos((string)$video->type, '伦理') !== false || strpos((string)$video->type, '福利') !== false) {
                continue;
            }
            array_push($ids, (string)$video->id);
        }

        return $ids;
    }

    /**
     * 获取影片详情和剧集
     * @param $url
     * @param $ids
     */
    public function getVideoList($url, $ids)
    {
        $newUrl = $url . '?ac=videolist&ids='.implode(',', $ids);
        $data = file_get_contents($newUrl);
        $xml = @simplexml_load_string($data);
        if(empty($xml)) {
            Console::output('XML格式不正确，不支持采集');
            return [];
        }

        foreach ($xml->list->video as $video) {
            //插入影片
            $nodeId = $this->getNodeId(trim($video->type));
            $videoParams = [
                'source' => 'zd',
                'out_id' => (int)$video->id,
                'updated_at' => (int)strtotime($video->last),
                'title' => $video->name,
                'category' => $video->type,
                'node_id' => $nodeId,
                'poster' => $video->pic,
                'language' => $video->lang ? $video->lang : '',
                'area' => $video->area ? $video->area : '',
                'year' => $video->year ? $video->year : '',
                'current_list_count' => (int)$video->state,
                'actors' => $video->actor ? $video->actor : '',
                'director' => $video->director ? $video->director : '',
                'desc' => $video->des ? strip_tags($video->des) : '',
                'note' => $video->note ? $video->note : '',
            ];
            $vid = $this->insertOrUpdateVideo($videoParams);
            Console::output($video->name);
            //插入影片剧集
            if($count = count($video->dl->dd)){
                for($i=0; $i<$count; $i++){
                    $xianlu = (string)$video->dl->dd[$i]['flag'];
                    $listDetails = $this->getUrls((string)$video->dl->dd[$i]);
                    if($listDetails) {
                        foreach ($listDetails as $ld){
                            $ld['video_id'] = $vid;
                            $ld['xianlu'] = $xianlu;
                            $this->insertOrUpdateList($ld);
                        }
                    }
                }
            }
        }


    }

    /**
     * 格式化剧集
     * @param $url
     * @return array
     */
    public function getUrls($url)
    {
        if(!$url) {
            return [];
        }
        $lists = explode('#', $url);
        if(!$lists){
            return [];
        }

        $listArray = [];
        foreach ($lists as $lk => $list) {
            $listDetail = explode('$', $list);
            if(!$listDetail || count($listDetail) < 2) continue;
            $listArray[] = [
                'list_num' => $this->parseListNum( $listDetail[0]),
                'play_url' => $listDetail[1],
            ];
        }

        return $listArray;
    }

    public function parseListNum($str) {
        $rep = ['第', '修正', '期', '版'];
        $str = str_replace( $rep, '', $str);
        return $str;
    }


    public function insertOrUpdateVideo($data)
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


    public function insertOrUpdateList($params)
    {
        $listExists = VideoList::findOne(['video_id' => $params['video_id'], 'list_num' => $params['list_num'], 'xianlu'=>$params['xianlu']]);

        if(!$listExists){
            $params['created_at'] = time();
            $listModel = new VideoList();
            $listModel->setAttributes($params);
            $listModel->save(false);
        }else{
            VideoList::updateAll($params,[
                'id' => $listExists['id']
            ]);
        }
    }

    /**
     * 获取最后更新时间
     */
    public function getLastUpdatedTime()
    {
        $last = Video::find()->select(['updated_at'])->orderBy(['updated_at' => SORT_DESC])->asArray()->one();
        return $last ? $last['updated_at'] : 0;
    }

    public function getNodeId($name)
    {
        $nodeInfo = Node::find()->where(['name' => trim($name)])->asArray()->one();
        if($nodeInfo){
            return $nodeInfo['id'];
        }
        return 0;
    }




}
