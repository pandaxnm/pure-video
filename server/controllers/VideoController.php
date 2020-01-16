<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/7/29
 * Time: 8:28 PM
 */
namespace app\controllers;

use app\models\Navi;
use app\models\Utils;
use app\models\ServiceVideo;

class VideoController extends BaseController {

    /**
     * 首页列表
     * @return string
     */
    public function actionIndex()
    {
        $params = $this->getRequest();
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'navi_id' => 'int',
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }
        $model = new ServiceVideo();
        $videos = $model->getIndexVideo($params['navi_id']);

        return $this->jsonResult($videos);
    }

    /**
     * 影片详情
     * @return string
     */
    public function actionDetail()
    {
        $params = $this->getRequest();
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'id' => 'int'
            ],
            'OPTIONAL' => [
                'from' => 'string'
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }
        $from = isset($params['from']) ? $params['from'] : '';

        $model = new ServiceVideo();
        $detail = $model->getVideoDetail($params['id'], $from);
        return $this->jsonResult($detail);
    }


    /**
     * 获取播放信息
     * @return string
     */
    public function actionPlayInfo()
    {
        $params = $this->getRequest();
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'id' => 'int',
                'list_num' => 'string'
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }

        $model = new ServiceVideo();
        $detail = $model->getPlayInfo($params['id'], $params['list_num']);
        return $this->jsonResult($detail);
    }


    /**
     * 搜索
     * @return string
     */
    public function actionSearch()
    {
        $params = $this->getRequest();
        $ret = Utils::verifyParams($params, [
            'REQUIRED' => [
                'keyword' => 'string',
            ]
        ]);

        if(!$ret){
            return $this->jsonError('参数错误');
        }

        $model = new ServiceVideo();
        $detail = $model->search($params['keyword']);
        return $this->jsonResult($detail);
    }

    /**
     * 热门搜索
     * @return string
     */
    public function actionHot()
    {
        $model = new ServiceVideo();
        $hot = $model->getHotList();
        return $this->jsonResult($hot);
    }

    /**
     * 所有分类
     * @return string
     */
    public function actionCategories()
    {
        return $this->jsonResult(Navi::getNavis());
    }

    public function actionBanner()
    {
        $model = new ServiceVideo();
        return $this->jsonResult($model->getBanners());
    }
}
