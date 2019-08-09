<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/27
 * Time: 4:45 PM
 */
namespace console\models;

use common\models\Utils;
use common\models\VideoList;

class Video {

    const VIDEO_DETAIL_AND_LIST_URL = 'http://app.beijingboli.com/meijutiantangxiangxi/%s.html';
    public static $types = [
        '美剧' =>'http://app.beijingboli.com/meijutiantang/8-0-%s.html',
        '韩剧' =>'http://app.beijingboli.com/meijutiantang/8-1-%s.html',
        '日剧' =>'http://app.beijingboli.com/meijutiantang/8-2-%s.html',
        '泰剧' =>'http://app.beijingboli.com/meijutiantang/8-3-%s.html',
    ];

    public static $p=  [
        '美剧' => '1',
        '韩剧' => '1',
        '日剧' => '1',
        '泰剧' => '1',
    ];

    //获取视频列表
    public function getVideos()
    {
//        \Yii::$app->db->getSchema()->refresh();
        foreach (self::$types as $k=> $type){
//            $p = \Yii::$app->redis->incr('renren_p'.$k);
            $p = self::$p[$k];
            while(true){
                file_put_contents('/tmp/v.log', $type.$p.PHP_EOL,FILE_APPEND);
                $url = sprintf($type, $p);
                echo $url . PHP_EOL;
                $res = $this->get($url);
                $data = json_decode($res, true);
                if(!$data['list']){
                    break;
                }
                foreach ($data['list'] as $v){
                    $this->insertVideo($v, $k);
                }
                $p++;
            }
        }
    }

    public function insertVideo($data, $area)
    {
        $detail = $this->getVideoDetail($data['id']);
        //插入或更新视频
        $vid = $this->insertOrUpdateVideo($data, $detail, $area);
        if(!$vid){
            return false;
        }
        if($detail['list']){
            foreach ($detail['list'] as $j){
                echo $data['title'];
                $this->insertOrUpdateList($vid, $j);
            }
        }

    }

    public function insertOrUpdateVideo($data, $detail, $area)
    {
        $model = new \common\models\Video();
        $exists = \common\models\Video::findOne(['out_id' => $data['id']]);
        $params = [
            'title' => $data['title'],
            'otitle' => $data['otitle'],
            'poster' => $data['img'],
            'current_list_count' => mb_substr($data['zhuti'],1,2,'utf-8'),
            'director' => $data['daoyan'],
            'actors' => $data['zhuyan'],
            'desc' => $detail['jianjie'],
            'category' => $area,
            'out_id' => $data['id'],
            'source' => 'renren',
            'type' => 'tv',
        ];
        if(!$exists){
            echo '新建：';
            $model->setAttributes($params);
            $model->save(false);
            $vid = $model->attributes['id'];
        }else {
            if($params['current_list_count'] > $exists['current_list_count']){
                echo '更新：';
                \common\models\Video::updateAll($params, ['id' => $exists['id']]);
            }
            $vid = $exists['id'];
        }
        return $vid;
    }

    public function insertOrUpdateList($vid, $j)
    {
        $listExists = VideoList::findOne(['video_id' => $vid, 'list_num' => $j]);
        echo $j['zhuti'] . PHP_EOL;
        $params = [
            'video_id' => $vid,
            'list_num' => $j,
            'download_url' => $j['down'],
            'web_url' => $j['weburl'],
            'play_url' => $j['bofang'],
            'xianlu' => 1,
            'updated_at' => time()
        ];

        if(!$listExists){
            $params['created_at'] = time();
            $listModel = new VideoList();
            $listModel->setAttributes($params);
            $listModel->save(false);
        }else{
            VideoList::updateAll($params,[
                'video_id' => $vid,
                'list_num' => $j['zhuti']
            ]);
        }
    }

    //获取某个视频的详细信息
    public function getVideoDetail($id)
    {
        $url = sprintf(self::VIDEO_DETAIL_AND_LIST_URL, $id);
        $res = $this->get($url);
        return json_decode($res, true);
    }


    function get($url)
    {
        $ch = curl_init();
        $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$this->ip(), 'CLIENT-IP:'.$this->ip()));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        $dom = curl_exec($ch);
        curl_close($ch);
        return $dom;
    }

    private function ip(){
        $ip2id= round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
        $ip3id= round(rand(600000, 2550000) / 10000);
        $ip4id= round(rand(600000, 2550000) / 10000);
        //下面是第二种方法，在以下数据中随机抽取
        $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
        $randarr= mt_rand(0,count($arr_1)-1);
        $ip1id = $arr_1[$randarr];
        return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;
    }

}