<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/29
 * Time: 8:28 PM
 */
namespace frontend\controllers;

use common\models\Utils;
use console\models\Yj;
use frontend\models\Video;

class VideoController extends BaseController {

    /**
     * 首页列表
     * @return string
     */
    public function actionIndex()
    {
        $params = $this->decryptData(false);
        $ret = Utils::verifyParams($params, [
            'OPTIONAL' => [
                'sort' => 'string'
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }
        $sort = isset($params['sort']) ? $params['sort'] : '';
        $model = new Video();
        $videos = $model->getIndexVideo($sort);

        return $this->jsonResult($videos);
    }

    /**
     * 影片详情
     * @return string
     */
    public function actionDetail()
    {
        $params = $this->decryptData(false);
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'id' => 'string'
            ],
            'OPTIONAL' => [
                'from' => 'string'
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }
        $from = isset($params['from']) ? $params['from'] : '';

        $model = new Video();
        $detail = $model->getVideoDetail($params['id'], $from);
        return $this->jsonResult($detail);
    }


    /**
     * 获取播放信息
     * @return string
     */
    public function actionPlayInfo()
    {
        $params = $this->decryptData(false);
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'id' => 'string',
                'list_num' => 'string'
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }

        $model = new Video();
        $detail = $model->getPlayInfo($params['id'], $params['list_num']);
        return $this->jsonResult($detail);
    }


    /**
     * 搜索
     * @return string
     */
    public function actionSearch()
    {
        $params = $this->decryptData(false);
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'keyword' => 'string',
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }

        $model = new Video();
        $detail = $model->search($params['keyword']);
        return $this->jsonResult($detail);
    }


    public function actionTest()
    {
        $model = new Yj();
        $model->getVideo();
    }
}