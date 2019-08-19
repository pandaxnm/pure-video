<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/8/7
 * Time: 3:59 PM
 */

namespace app\commands\models;

use Yii;
use app\models\Video;
use app\models\VideoList;
use yii\helpers\Console;

class ServiceVideo{

    const URL = 'http://www.zdziyuan.com/inc/api_zuidam3u8.php';
    public static $skip = ['福利片','伦理片'];//不抓取的影片类型

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
                foreach ($xmlarray['list']['video'] as $video) {
                    if($video['last'] && strtotime($video['last']) <= $lastUpdatedTime && !$foreUpdate){
                        break 2;
                    }
                    Console::output($video['name']);
                    $params = [
                        'source' => 'zd',
                        'out_id' => (int)$video['id'],
                        'updated_at' => (int)strtotime($video['last']),
                        'title' => $video['name'],
                        'category' => $video['type'],
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

                    foreach ($video['dl'] as $dd) {
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
                                        'list_num' => $listData[0],
                                        'download_url' => '',
                                        'web_url' => $listData[1],
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
                                    'list_num' => $listData[0],
                                    'download_url' => '',
                                    'web_url' => $listData[1],
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
        $exists = Video::findOne(['out_id' => $data['out_id']]);
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




}