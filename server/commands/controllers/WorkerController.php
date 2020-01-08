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
     * @param bool $foreUpdate 是否采集所有资源 false则只采集最近更新的资源
     */
    public function actionGetVideos($foreUpdate = false)
    {
        $model = new ServiceVideo();
        $model->getVideo($foreUpdate);
    }

    public function actionGetBanner()
    {
        $model = new ServiceBanner();
        $model->getBanner();

    }

}