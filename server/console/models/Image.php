<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/27
 * Time: 7:43 PM
 */
namespace console\models;

use Yii;
use common\models\Video;

class Image{

    /**
     * 图片本地化
     */
    public function getImageToLocal()
    {
        $page = 1;
        $pageSize = 500;
        while(true){
            $lists = Video::find()
                ->where(['poster_url'=>''])
//                ->orderBy(['id'=>SORT_ASC])
                ->orderBy(['updated_at'=>SORT_DESC])
                ->limit($pageSize)
                ->offset(($page-1)*$pageSize)
                ->asArray()
                ->all();
            if(!$lists){
                break;
            }

            foreach ($lists as $v){
                $imgUrl = $v['poster'];
                if(!$imgUrl){
                    continue;
                }
                echo $v['poster']. ' >>> ';
                try{
                    $newUrl = $this->getImage($v['poster']);
                    if(!$newUrl) {
                        continue;
                    }
                    echo $newUrl . PHP_EOL;
                    Video::updateAll(['poster_url' => $newUrl], ['id'=>$v['id']]);
                }catch (\Exception $e){
                    echo $e->getMessage() . PHP_EOL;
                }

            }
            $page++;

        }
    }

    /**
     * @param $url
     * @return string
     * @throws \yii\base\Exception
     */
    private function getImage($url)
    {
        $explode = explode('@',$url);
        if(count($explode) > 1){
            $url = $explode[0];
        }
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
        $dir = Yii::getAlias('@frontend/web/images/');
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
            //如果是webp格式 需要转换
            $imageInfo = getimagesize($url);
            if($imageInfo['mime'] == 'image/webp'){
                echo ' webp ';
                $this->webp2jpg($path, $imageInfo);
            }
            $relativePath = '/images/'.$nextDir.$newName;
            return $relativePath;
        }

        throw new \Exception('未获取到图片资源');
    }


    function webp2jpg($path, $imageInfo)
    {
        try{
            return \yii\imagine\Image::thumbnail($path, $imageInfo[0], $imageInfo[1])->save($path);
        }catch (\Exception $e){
            echo $e->getMessage() . PHP_EOL;
        }

    }

}