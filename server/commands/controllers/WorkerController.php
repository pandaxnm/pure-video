<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/27
 * Time: 4:42 PM
 */
namespace app\commands\controllers;

use app\commands\models\ServiceBanner;
use app\commands\models\ServiceImage;
use app\commands\models\ServiceVideo;
use app\models\Node;
use app\models\Video;
use yii\console\Controller;
use yii\data\Pagination;

class WorkerController extends Controller{

    /**
     * 图片本地化
     */
    public function actionGetImages()
    {
        $model = new ServiceImage();
        $model->getImageToLocal();
    }

    /**
     * @param bool
     */
    public function actionGetVideos()
    {
        $model = new ServiceVideo();
        $urls = [
            'http://www.zdziyuan.com/inc/api_zuidam3u8.php', //最大
            'https://cj.wlzy.tv/inc/s_api_zp_m3u8.php', //卧龙
            'http://cj.yongjiuzyw.com/inc/yjm3u8.php', //永久
        ];

        foreach ($urls as $url){
            $model->collectVideo($url);
        }
    }

    public function actionGetBanner()
    {
        $model = new ServiceBanner();
        $model->getBanner();

    }

}
