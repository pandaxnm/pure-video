<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/27
 * Time: 7:43 PM
 */
namespace app\commands\models;

use Yii;
use app\models\Video;
use yii\helpers\Console;

class ServiceImage{

    /**
     * 图片本地化
     */
    public function getImageToLocal()
    {
        $page = 1;
        $pageSize = 500;
        $count = 0;
        while(true){
            $lists = Video::find()
                ->where(['poster_url'=>''])
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
                echo $v['title']. ' >>> ';
                try{
                    $newUrl = $this->getImage($v['poster']);
                    if(!$newUrl) {
                        continue;
                    }
                    Console::output($newUrl);
                    Video::updateAll(['poster_url' => $newUrl], ['id'=>$v['id']]);
                    $count++;
                }catch (\Exception $e){
                    Console::output(Console::ansiFormat($e->getMessage(),[Console::FG_RED]));
                }

            }
            $page++;

        }
        Console::output(Console::ansiFormat("本地化完毕，一共下载了 {$count} 张图片",[Console::FG_GREEN]));

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
