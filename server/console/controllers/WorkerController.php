<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/27
 * Time: 4:42 PM
 */
namespace console\controllers;

use console\models\Image;
use console\models\Video;
use console\models\Yj;
use yii\console\Controller;

class WorkerController extends Controller{

    public function actionGetVideos()
    {
        $model = new Video();
        $model->getVideos();
    }

    /**
     * 图片本地化
     */
    public function actionGetImages()
    {
        $model = new Image();
        $model->getImageToLocal();
    }

    /**
     * 采集资源
     */
    public function actionYj()
    {
        $model = new Yj();
        $model->getVideo();
    }

}