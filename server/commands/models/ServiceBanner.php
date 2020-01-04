<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/27
 * Time: 7:43 PM
 */
namespace app\commands\models;

use app\models\Banner;
use Yii;
use app\models\Video;
use yii\helpers\Console;

class ServiceBanner{

    public function getBanner()
    {
        $url = 'https://www.kuhuiv.com';
        $contents = file_get_contents($url);

        $banner = $this->get_tag_data($contents, "ul", "class","banner-pic-inner");
        if($banner) {
            $banner = $banner[0];
        }

        $res = $this->extract_attrib($banner);

        if($res['titles'] && $res['urls']) {
            Banner::deleteAll();
            $titles = $res['titles'];
            foreach ($titles as $k => $title) {
                $video = Video::findOne(['title' => trim($title)]);
                if($video) {
                    $banner = new Banner();
                    $banner->title = $video->title;
                    $banner->url = $res['urls'][$k];
                    $banner->video_id = $video->id;
                    $banner->created_at = time();
                    $banner->save();
                }
            }
        }

    }

    function extract_attrib($tag) {
        preg_match_all('/(style|title|)=("[^"]*")/i', $tag, $matches);
        $titles = [];
        $urls = [];
        foreach($matches[1] as $i => $v) {
            if($v == 'title') {
                $titles[] = trim($matches[2][$i],'"');
            }else if($v == 'style'){
                $reg = '/https?:\/\/(.+\/)+.+(\.(gif|png|jpg|jpeg|webp|svg|psd|bmp|tif))/';
                $matches2 = array();
                preg_match($reg, $matches[2][$i], $matches2);
                if(count($matches2) > 0){
                    $urls[] = $matches2[0];
                }
            }
        }
        return ['titles' => array_values(array_unique($titles)), 'urls' => array_values(array_unique($urls))];
    }

    function get_tag_data($html,$tag,$class,$value){
        //$value 为空，则获取class=$class的所有内容
        $regex = $value ? "/<$tag.*?$class=\"$value\".*?>(.*?)<\/$tag>/is" :  "/<$tag.*?$class=\".*?$value.*?\".*?>(.*?)<\/$tag>/is";
        preg_match_all($regex,$html,$matches,PREG_PATTERN_ORDER);
        return $matches[1];//返回值为数组 ,查找到的标签内的内容
    }

    /**
     * @param $url
     * @return string
     * @throws \yii\base\Exception
     */
    private function getImage($url)
    {
        $name = explode('/', $url);
        if(count($name) < 2){
            throw new \Exception('url有误');
        }
        $name = end($name) . PHP_EOL;
        $extension = explode('.', $name);
        if($extension < 2){
            throw new \Exception('文件名有误');
        }
        $extension = trim($extension[1]);
        $newName = time() . Yii::$app->security->generateRandomString(6). '.' . $extension;
        $dir = Yii::getAlias('@app/web/images/');
        $nextDir = date('Y').'/'.date('m').'/'.date('d').'/';
        @mkdir($dir . $nextDir,0777, true);
        $path = $dir . $nextDir . $newName;
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $imagedata = file_get_contents($url, false, stream_context_create($arrContextOptions));
        if($imagedata){
            file_put_contents($path, $imagedata);
            $relativePath = '/images/'.$nextDir.$newName;
            return $relativePath;
        }

        throw new \Exception('未获取到图片资源');
    }

}