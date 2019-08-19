<?php

namespace app\controllers;

use Yii;

class SiteController extends BaseController
{

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        return $this->jsonError($exception->getMessage());
    }
}
