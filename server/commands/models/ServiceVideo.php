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

    const URL = 'http://www.zdziyuan.com/inc/s_api_zuidam3u8.php';
    public static $skip = ['福利片','伦理片'];//不抓取的影片类型

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
            if(!$listDetail) continue;
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

    function getVideo($foreUpdate)
    {
        $url = self::URL . "?ac=list&pg=%d";
        $p = 1;
        $count = 0;

        while (true) {
            try {
                $newUrl = sprintf($url, $p);
                Console::output($newUrl);
                $data1 = file_get_contents($newUrl);
                $data1 = simplexml_load_string($data1, 'SimpleXMLElement', LIBXML_NOCDATA);
                $xmljson1 = json_encode($data1);//将对象转换个JSON
                $xmlarray1 = json_decode($xmljson1, true);
                if (!$xmlarray1['list']['video']) {
                    break;
                }
                $ids = [];
                foreach ($xmlarray1['list']['video'] as $video) {
                    if(in_array($video['type'],self::$skip)){
                        continue;
                    }
                    $ids[] = $video['id'];
                }
                $url2 = self::URL . '?ac=videolist&ids='.implode(',', $ids);
                $data = file_get_contents($url2);

                $data = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
                $xmljson = json_encode($data);//将对象转换个JSON
                $xmlarray = json_decode($xmljson, true);
                if (!$xmlarray['list']['video']) {
                    break;
                }
                $lastUpdatedTime = $this->getLastUpdatedTime();
                foreach ($xmlarray['list']['video'] as $k => $video) {
                    if($video['last'] && strtotime($video['last']) <= $lastUpdatedTime && !$foreUpdate){
                        break 2;
                    }
                    Console::output($video['name']);
                    $nodeId = $this->getNodeId(trim($video['type']));
                    $params = [
                        'source' => 'zd',
                        'out_id' => (int)$video['id'],
                        'updated_at' => (int)strtotime($video['last']),
                        'title' => $video['name'],
                        'category' => $video['type'],
                        'node_id' => $nodeId,
                        'poster' => $video['pic'],
                        'language' => $video['lang'] ? $video['lang'] : '',
                        'area' => $video['area'] ? $video['area'] : '',
                        'year' => $video['year'] ? $video['year'] : '',
                        'current_list_count' => (int)$video['state'],
                        'actors' => $video['actor'] ? $video['actor'] : '',
                        'director' => $video['director'] ? $video['director'] : '',
                        'desc' => $video['des'] ? strip_tags($video['des']) : '',
                        'note' => $video['note'] ? $video['note'] : '',
                    ];
                    $vid = $this->insertOrUpdateVideo($params);
                    if (!$vid) {
                        continue;
                    }
                    $count++;

                    foreach ($video['dl'] as $j=> $dd) {
                        $xianlu = 1;
                        if (is_array($dd)) {
                            foreach ($dd as $l) {
                                $lists = explode('#', $l);
                                foreach ($lists as $list) {
                                    $listData = explode('$', $list);
                                    if (count($listData) < 2) continue;
                                    $listParams = [
                                        'video_id' => (int)$vid,
                                        'updated_at' => (int)strtotime($video['last']),
                                        'list_num' => str_replace( '修正', '', $listData[0]),
                                        'download_url' => '',
                                        'play_url' => $listData[1],
                                        'xianlu' => $xianlu,
                                    ];
                                    $this->insertOrUpdateList($listParams);
                                    $xianlu++;
                                }
                            }
                        } else {
                            $lists = explode('#', $dd);
                            foreach ($lists as $list) {
                                $listData = explode('$', $list);
                                if (count($listData) < 2) continue;
                                $listParams = [
                                    'video_id' => (int)$vid,
                                    'updated_at' => (int)strtotime($video['last']),
                                    'list_num' => str_replace( '修正', '', $listData[0]),
                                    'download_url' => '',
                                    'play_url' => $listData[1],
                                    'xianlu' => $xianlu,
                                ];
                                $this->insertOrUpdateList($listParams);
                            }
                        }

                    }
                }
            } catch (\Exception $e) {
                Console::output(Console::ansiFormat($e->getMessage(),[Console::FG_GREEN]));
//                continue;
            }
            $p++;
        }

        Console::output(Console::ansiFormat("抓取完毕，一共更新了 {$count} 部影片",[Console::FG_GREEN]));
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
