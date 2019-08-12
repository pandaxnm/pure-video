<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/8/7
 * Time: 3:59 PM
 */

namespace console\models;

use common\models\VideoList;

class Yj{

    const URL = 'http://cj.yongjiuzyw.com/inc/yjm3u8.php';
    public static $skip = ['美女写真','伦理片','嫩妹写真','美女视频秀','街拍系列','高跟赤足视频','VIP视频秀'];

    function getVideo()
    {
        $url = self::URL . "?ac=list&pg=%d";
        $p = 1;
        $count = 1;

        while (true) {
            try {
                $newUrl = sprintf($url, $p);
                echo $newUrl . PHP_EOL;
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
                foreach ($xmlarray['list']['video'] as $video) {
                    echo $video['name'] . PHP_EOL;
                    $params = [
                        'source' => 'yj',
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
                echo $e->getMessage() . PHP_EOL;
//                continue;
            }
            $p++;
        }

    }


    public function insertOrUpdateVideo($data)
    {
        $model = new \common\models\Video();
        $exists = \common\models\Video::findOne(['out_id' => $data['out_id']]);
        if(!$exists){
            $data['created_at'] = time();
            $model->setAttributes($data);
            $model->save(false);
            $vid = $model->attributes['id'];
        }else {
            \common\models\Video::updateAll($data, ['id' => $exists['id']]);
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




}