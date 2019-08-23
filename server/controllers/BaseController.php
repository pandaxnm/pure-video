<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/2/5
 * Time: 2:31 PM
 */

namespace app\controllers;

use yii\helpers\Json;
use yii\web\Controller;
use yii;

class BaseController extends Controller {

    const ENCRYPT_KEY = '1234123412ABCDEF';
    const IV = 'ABCDEF1234123412';

    public $settings;

    public function init()
    {
        $this->settings = Yii::$app->params['settings'];
    }


    public function beforeAction($action)
    {
        $this->layout               = false;
        $this->enableCsrfValidation = false;
        return true;
    }

    public function jsonResult($value = '', $msg = '', $key = 'data')
    {
        $arr = [
            'retCode' => 0,
            'retMsg'  => $msg ? $msg : 'ok',
        ];

        if ($key) {
            $arr[$key] = $value;
        }

        return $this->encrypt(Json::encode($arr));
    }

    public function jsonError($msg, $errno = 1)
    {
        $arr = [
            'retCode' => $errno,
            'retMsg' => $msg,
        ];

        return $this->encrypt(Json::encode($arr));
    }


    public function getRequest($method = 'get')
    {
        $request = Yii::$app->getRequest();
        if($method == 'post'){
            $encryptedData = $request->post('encryptedData');
        }else{
            $encryptedData = $request->get('encryptedData');
        }

        if (!$encryptedData) {
            return [];
        }

        $cryptText = base64_decode($encryptedData);
        $decrypted =  trim(openssl_decrypt($cryptText, 'aes-128-cbc', self::ENCRYPT_KEY, OPENSSL_RAW_DATA,self::IV));

        return json_decode($decrypted, true);
    }

    private function encrypt($data) {
        $cryptText = openssl_encrypt($data,"aes-128-cbc",self::ENCRYPT_KEY,OPENSSL_RAW_DATA,self::IV);
        return base64_encode($cryptText);
    }





}