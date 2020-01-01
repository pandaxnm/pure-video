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

    public $settings;

    public $requestId = null;
    protected $startDoTime = 0;//开始时间
    protected $endDoTime = 0;//结束时间
    protected $doTimeLog = true;

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

    /**
     *
     */
    public function beforeApi()
    {
        //日志
        $this->requestId = REQUEST_ID;
        $this->startDoTime = $this->microtime_float();
        $route = $this->getRoute();
        $this->writeRequestLog($route, $_REQUEST, '请求');
//        return true;
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
        $decrypted =  trim(openssl_decrypt($cryptText, 'aes-128-cbc', $this->settings['key'], OPENSSL_RAW_DATA,$this->settings['iv']));

        return json_decode($decrypted, true);
    }

    private function encrypt($data) {
        $cryptText = openssl_encrypt($data,"aes-128-cbc",$this->settings['key'],OPENSSL_RAW_DATA,$this->settings['iv']);
        return base64_encode($cryptText);
    }

    /**
     * 解密
     * @param $data
     * @return mixed
     */
    public function decrypt($data) {
        $cryptText = base64_decode($data);
        $decrypted =  trim(openssl_decrypt($cryptText, 'aes-128-cbc', $this->settings['key'], OPENSSL_RAW_DATA,$this->settings['iv']));
        return json_decode($decrypted, true);
    }

    /**
     * @return float
     */
    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * @param $route
     * @param $log
     * @param string $name
     */
    public function writeRequestLog($route, $log,$name = '')
    {
        if(is_array($log)){
            ksort($log);
            $logStr = json_encode($log);
        }else{
            $logStr = $log;
        }
        $file = date('Ymd').'.log';
        $decrypted = $this->getRequest();
        $lineStr = PHP_EOL."=={$name}==".date("Y-m-d H:i:s")."==". $this->requestId."==".$route."==".PHP_EOL.$logStr.PHP_EOL.json_encode($decrypted).PHP_EOL;
        $this->log($file, $lineStr);
    }

    /**
     * @param $route
     * @param $log
     * @param string $name
     */
    public function writeResponseLog($route, $log,$name = '')
    {
        if(is_array($log)){
            ksort($log);
            $logStr = json_encode($log);
        }else{
            $logStr = $log;
        }
        $file = date('Ymd').'.log';
        $decrypted = $this->decrypt($logStr);
        $lineStr = PHP_EOL."=={$name}==".date("Y-m-d H:i:s")."==". $route ."==".$this->requestId."==".PHP_EOL.json_encode($decrypted).PHP_EOL;
        $this->log($file, $lineStr);
    }

    /**
     * @param $route
     * @param $time
     */
    public function writeTimeLog($route, $time)
    {
        $file = 'time_'.date('Ymd').'.log';
        $logStr = "==={$route}===".date('Y-m-d H:i:s').'==='.$time.PHP_EOL;
        $this->log($file, $logStr);
    }

    /**
     * @param $file
     * @param $str
     * @return bool
     */
    public function log($file, $str)
    {
        $logDir = Yii::getAlias('@frontend/runtime/logs/');
        if(!is_dir($logDir)){
            return false;
        }
        $logDir .= $file;
        @file_put_contents($logDir, $str, FILE_APPEND);

    }





}