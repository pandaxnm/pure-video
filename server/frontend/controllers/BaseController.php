<?php
/**
 * Created by PhpStorm.
 * User: chaojie.xiong
 * Date: 2019/2/5
 * Time: 2:31 PM
 */

namespace frontend\controllers;

use common\models\Utils;
use yii\helpers\Json;
use yii\web\Controller;
use yii;

class BaseController extends Controller {

    const ENCRYPT_KEY = '1234123412ABCDEF';
    const IV = 'ABCDEF1234123412';
    const REQUEST_LIMIT_COUNT = 150;

    public $requestId = '';
    protected $startDoTime = 0;//开始时间
    protected $endDoTime = 0;//结束时间
    protected $doTimeLog = true;

    public $settings;
    public $me;

    //初始化
    public function init()
    {
        $this->settings = Yii::$app->params['settings'];
    }

    //
    public function beforeAction($action)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
        header("Access-Control-Allow-Headers:x-requested-with,content-type,authorization,token");
        header("Access-Control-Max-Age", "2592000");
        usleep(500000);

        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->data = $this->jsonResult();
            return false;
        }
        $this->layout               = false;
        $this->enableCsrfValidation = false;

        $beforeApi = $this->beforeApi();
        if($beforeApi !== true){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->data = $beforeApi;
            return false;
        }

        $this->startDoTime = $this->microtime_float();
        $route = $this->getRoute();
        $this->writeRequestLog($route, $_REQUEST);

        return true;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        //记录返回结果
        $route = $this->getRoute();
        $this->writeRequestLog($route, $result);

        //记录程序的执行时间
        $this->endDoTime = $this->microtime_float();
        if($this->doTimeLog){
            $route = $this->getRoute();
            $time = $this->endDoTime-$this->startDoTime;
            $this->writeTimeLog($route, $time);
        }

        return $result;
    }


    protected function beforeApi()
    {
        //检查访问速率
        if( YII_ENV_PROD && !$this->checkRequestSpeed()){
            return $this->jsonError('请求过快');
        }

        $this->requestId = REQUEST_ID;
        $action = Yii::$app->controller->action->id;
        $api = $this->id.'/'.$action;

        return true;
    }


    public function checkRequestSpeed()
    {
        $ip = Utils::getClientIp();
        $key = 'request_count_' . $ip;

        $count = Yii::$app->redis->get($key);
        if($count >= $this->settings['request_limit']){
            return false;
        }

        if(!$count){
            Yii::$app->redis->set($key, 1);
            Yii::$app->redis->expire($key, 60);
        }else{
            Yii::$app->redis->incr($key);
        }

        return true;
    }

    public function jsonResult($value = '', $msg = '', $key = 'data')
    {
        $arr = [
            'retCode' => 0,
            'retMsg'  => $msg ? $msg : 'ok',
        ];

        if ($key && $value) {
            $arr[$key] = $value;
        }

        return $this->encrypt(Json::encode($arr));

    }

    public function jsonError($errno = 1)
    {
        if(is_array($errno)){
            $arr = $errno;
        }else{
            $arr = [];
            if(isset($this->msgArr[$errno])){
                $arr['retCode'] = $errno;
                $arr['retMsg'] = Yii::t('app',$this->msgArr[$errno]);
            }else{
                $arr['retCode'] = $errno;
                $arr['retMsg'] = Yii::t('app','System Error');
            }
        }

        return $this->encrypt(Json::encode($arr));
    }


    public function decryptData($post = true)
    {
        $request = Yii::$app->getRequest();
        if($post){
            $encryptedData = $request->post('encryptedData');
        }else{
            $encryptedData = $request->get('encryptedData');
        }

        if (!$encryptedData) {
            return [];
        }

        @$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::ENCRYPT_KEY, base64_decode($encryptedData), MCRYPT_MODE_CBC, self::IV);

        $data = json_decode(trim($decrypted), true);

        return $data;
    }

    public function encrypt($data) {
        $check_php_version = PHP_VERSION > '5.5.0';

        if($check_php_version){
            $td = @mcrypt_module_open('rijndael-128', '', 'cbc', '');
            if (@mcrypt_generic_init($td, self::ENCRYPT_KEY, self::IV) != -1) {
                $encryptedcbc = @mcrypt_generic($td, $data);
                @mcrypt_generic_deinit($td);
                @mcrypt_module_close($td);
            }else{
                $encryptedcbc = '';
            }
        }else{
            $encryptedcbc = @mcrypt_cbc(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_ENCRYPT, $iv);
        }
        return base64_encode($encryptedcbc);
    }

    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }


    public function writeRequestLog($route, $log)
    {
        $logDir = Yii::getAlias('@frontend/runtime/logs');
        if(!is_dir($logDir)){
            return false;
        }
        if(is_array($log)){
            ksort($log);
            $logStr = json_encode($log);
        }else{
            $logStr = $log;
        }
        $file = $logDir.'/request_'.date('Ymd').'.log';
        $lineStr = date("Y-m-d H:i:s")."|||".$this->requestId."|||".$route."|||".$logStr.PHP_EOL;
        file_put_contents($file, $lineStr, FILE_APPEND);
    }

    public function writeTimeLog($route, $time)
    {
        $logDir = Yii::getAlias('@frontend/runtime/logs');
        if(!is_dir($logDir)){
            return false;
        }
        $file = $logDir.'/time_'.date('Ymd').'.log';
        $logStr = "{$route}=".date('Hi').'|'.$time.PHP_EOL;
        file_put_contents($file, $logStr, FILE_APPEND);
    }




}