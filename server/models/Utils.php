<?php

namespace app\models;

/**
 * 常用的公共方法类,全部为静态方法，与框架无关，保持代码的独立性
 */
class Utils
{

    /**
     * 参数验证
     * @param $para array 数据
     * @param $standard array 参数要求
     * @return bool
     */
    public static function verifyParams($para, $standard)
    {
        if ($para === false || empty($para)) {
            return false;
        }

        if(isset($standard['REQUIRED'])){
            foreach ($standard['REQUIRED'] as $k => $v) {
                if (!array_key_exists($k, $para)) {
                    return false;
                }
                if (!isset($para[$k]) || $para[$k] === '') {
                    return false;
                }

                if ('string' == $v) {
                    if (false === is_string($para[$k])) {
                        return false;
                    }
                } else if ('int' == $v) {
                    if ((string)((int)($para[$k])) != $para[$k]) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        if(isset($standard['OPTIONAL'])){
            foreach ($standard['OPTIONAL'] as $k => $v) {
                if (!array_key_exists($k, $para)) {
                    continue;
                }

                if ('string' == $v) {
                    if ((!isset($para[$k]) || $para[$k] === '') && false === is_string($para[$k])) {
                        return false;
                    }
                } else if ('int' == $v) {
                    if ((!isset($para[$k]) || $para[$k] === '') && (string)((int)($para[$k])) != $para[$k]) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }


        return true;
    }

    /**
     * 签名生成算法
     * @param  array $data 数据
     * @param  [type] $privateKey 加密私有key
     * @return string
     */
    public static function sign($data, $privateKey)
    {
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $sign = strtolower(md5($str . 'key=' . $privateKey));

        return $sign;
    }

    /**
     * 简单的加解密算法
     * @param $data
     * @param $key
     * @return string
     */
    public static function simple_xor($data, $key)
    {
        $return = '';
        $j      = 0;
        $len    = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $return .= chr(ord($data[$i]) ^ ord($key[$j]));
            $j++;
            $j = $j % strlen($key);
        }
        return $return;
    }

    /**
     * 加解密算法
     * @param  [type] $string [description]
     * @param  string $action [description]
     * @param  string $rand [description]
     * @return [type]         [description]
     */
    public static function mymd5($string, $rand = 'gaeamobile', $action = "EN")
    { //字符串加密和解密
        $secret_string = $rand . '5*a,.^&;?.%#@!'; //绝密字符串,可以任意设定

        if ($string == "")
            return "";
        if ($action == "EN")
            $md5code = substr(md5($string), 8, 10);
        else {
            $md5code = substr($string, -10);
            $string  = substr($string, 0, strlen($string) - 10);
        }
        //$key = md5($md5code.$_SERVER["HTTP_USER_AGENT"].$secret_string);
        $key    = md5($md5code . $secret_string);
        $string = ($action == "EN" ? $string : base64_decode($string));
        $len    = strlen($key);
        $code   = "";
        for ($i = 0; $i < strlen($string); $i++) {
            $k    = $i % $len;
            $code .= $string[$i] ^ $key[$k];
        }
        $code = ($action == "DE" ? (substr(md5($code), 8, 10) == $md5code ? $code : NULL) : base64_encode($code) . "$md5code");

        return $code;
    }


    /**
     * 对称加密算法
     * @param $data
     * @return string
     */
    public static function encrypt($data, $key, $iv)
    {
        $check_php_version = PHP_VERSION > '5.5.0';
        if ($check_php_version) {
            $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
            if (mcrypt_generic_init($td, $key, $iv) != -1) {
                $encryptedcbc = mcrypt_generic($td, $data);
                mcrypt_generic_deinit($td);
                mcrypt_module_close($td);
            } else {
                $encryptedcbc = '';
            }
        } else {
            $encryptedcbc = mcrypt_cbc(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_ENCRYPT, $iv);
        }
        return base64_encode($encryptedcbc);
    }

    /**
     * 对称解密算法
     * @param $data
     * @return string
     */
    public static function decrypt($data, $key, $iv)
    {
        $data_decode       = base64_decode($data);
        $check_php_version = PHP_VERSION > '5.5.0';
        if ($check_php_version) {
            $td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
            if (mcrypt_generic_init($td, $key, $iv) != -1) {
                $decryptedcbc = mdecrypt_generic($td, $data_decode);
                mcrypt_generic_deinit($td);
                mcrypt_module_close($td);
            } else {
                $decryptedcbc = '';
            }
        } else {
            $decryptedcbc = mcrypt_cbc(MCRYPT_RIJNDAEL_128, $key, $data_decode, MCRYPT_DECRYPT, $iv);
        }
        return $decryptedcbc;
    }

    /**
     * 友好显示var_dump
     *
     */
    public static function dump($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo $output;
            return null;
        } else
            return $output;
    }

    /**
     * 封装curl函数
     *
     *
     */
    public static function curl($url, $params = array(), $options = array())
    {
        $curlInstance = curl_init($url);
        $defaultOpt   = array(
            CURLOPT_POST           => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => 3,
        );
        if (is_array($options) && !empty($options)) {
            foreach ($options as $k => $v) {
                $defaultOpt[$k] = $v;
            }
        }
        foreach ($defaultOpt as $k => $v) {
            curl_setopt($curlInstance, $k, $v);
        }
        if ($defaultOpt[CURLOPT_POST] && !empty($params)) { //如果输入的是Post请求，并设置了请求参数，将post内容封装到CURLOPT_POSTFIELDS中
            if (is_array($params)) {
                $content = http_build_query($params);
            } else {
                $content = $params;
            }
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $content);
        }

        $count = 0;
        $ret   = null;
        while ($count < 3) {
            $ret    = curl_exec($curlInstance);
            $errno  = curl_errno($curlInstance);
            $errmsg = curl_error($curlInstance);
            if (!$errno) {
                break;
            }
            //此处要记录错误日志
            $count++;
        }
        curl_close($curlInstance);
        return $ret;
    }

    /**
     * @param $url string 请求地址
     * @param $params string json参数
     * @param $header string header参数
     * @return mixed
     */
    public function post($url, $params='', $header=[])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        $params && curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $header && curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(array('Content-Type: application/json', 'Content-Length: ' . strlen($params)), $header));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * base64_encode
     */
    public static function b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * base64_decode
     */
    public static function b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * 验证邮箱
     */
    public static function email($str)
    {
        if (empty($str))
            return false;
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($str, '@') !== false && strpos($str, '.') !== false) {
            if (preg_match($chars, $str)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 验证手机号
     * 13[0-9], 14[5,7], 15[0, 1, 2, 3, 5, 6, 7, 8, 9], 17[6, 7, 8], 18[0-9], 170[0-9]
     * 移动号段: 134,135,136,137,138,139,150,151,152,157,158,159,182,183,184,187,188,147,178,1705
     * 联通号段: 130,131,132,155,156,185,186,145,176,1709
     * 电信号段: 133,153,180,181,189,177,1700
     */
    public static function mobile($mobile)
    {
        //return true;
        if (preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|8[0-9]|70)\\d{8}$/', $mobile)) {
            return 1;
        } elseif (preg_match('/(^1(3[4-9]|4[7]|5[0-27-9]|7[385]|8[2-478])\\d{8}$)|(^1705\\d{7}$)/', $mobile)) {
            return 2;
        } elseif (preg_match('/(^1(3[0-2]|4[5]|5[56]|7[6]|8[56])\\d{8}$)|(^1709\\d{7}$)/', $mobile)) {
            return 3;
        } elseif (preg_match('/(^1(33|53|77|8[019])\\d{8}$)|(^1700\\d{7}$)/', $mobile)) {
            return 4;
        } else {
            return 0;
        }
    }

    /**
     * 验证用户名
     * 字母、数字、下划线组成，字母开头，6-20位
     */
    public static function username($username)
    {
        return preg_match('/^[a-zA-z]\w{5,19}$/', $username);
    }

    /**
     * 验证快速开始用户名
     * 可以包含字符：a-z、A-Z、0-9、_(下划线)、@、-(横杠)、
     * 不可包含单词(select,update,delete,insert,drop,create,add,modify,alert)
     */
    public static function quickUsername($username)
    {
        $ret = self::illegalCharacters($username);
        if (!$ret)
            return false;

        return true;
    }

    /**
     * 字符串非法字符检测
     * 检测是否有非法字符存在
     */
    public static function illegalCharacters($strOrArr)
    {
        $IllegalArr = ['"', '\'', '\\', '\/', '&', '||', '%', '*', '(', ')', 'select', 'update', 'delete', 'insert', 'create', 'modify',];
        if (is_array($strOrArr)) {
            foreach ($strOrArr as $str) {
                foreach ($IllegalArr as $char) {
                    if (strpos(strtolower($str), $char) !== false) {
                        return false;
                    }
                }
            }
        } else {
            foreach ($IllegalArr as $char) {
                if (strpos(strtolower($strOrArr), $char) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 验证密码
     * (6-20位字母数字下划线)
     */
    public static function password($password)
    {
        return preg_match('/^[0-9a-zA-Z]{6,20}$/', $password);
    }

    public static function isCreditNo($vStr)
    {
        $vCity = array(
            '11', '12', '13', '14', '15', '21', '22',
            '23', '31', '32', '33', '34', '35', '36',
            '37', '41', '42', '43', '44', '45', '46',
            '50', '51', '52', '53', '54', '61', '62',
            '63', '64', '65', '71', '81', '82', '91'
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

        if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

        $vStr    = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18) {
            $vSum = 0;

            for ($i = 17; $i >= 0; $i--) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum    += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }

            if ($vSum % 11 != 1) return false;
        }

        return true;
    }

    /**
     * 验证ip
     */
    public static function ip($str)
    {
        if (empty($str))
            return false;

        if (!preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $str)) {
            return false;
        }

        $ip_array = explode('.', $str);

        //真实的ip地址每个数字不能大于255（0-255）
        return ($ip_array[0] <= 255 && $ip_array[1] <= 255 && $ip_array[2] <= 255 && $ip_array[3] <= 255) ? true : false;
    }

    /**
     * 验证网址
     */
    public static function url($str)
    {
        if (empty($str))
            return true;

        return preg_match('#(http|https|ftp|ftps)://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?#i', $str) ? true : false;
    }

    /**
     * 字符截取
     *
     * @param $string
     * @param $length
     * @param $dot
     */
    public static function cutstr($string, $length, $dot = '...', $charset = 'utf-8')
    {
        if (strlen($string) <= $length)
            return $string;

        $pre    = chr(1);
        $end    = chr(1);
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);

        $strcut = '';
        if (strtolower($charset) == 'utf-8') {

            $n = $tn = $noc = 0;
            while ($n < strlen($string)) {

                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn  = 2;
                    $n   += 2;
                    $noc += 2;
                } elseif (224 <= $t && $t <= 239) {
                    $tn  = 3;
                    $n   += 3;
                    $noc += 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn  = 4;
                    $n   += 4;
                    $noc += 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn  = 5;
                    $n   += 5;
                    $noc += 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn  = 6;
                    $n   += 6;
                    $noc += 2;
                } else {
                    $n++;
                }

                if ($noc >= $length) {
                    break;
                }

            }
            if ($noc > $length) {
                $n -= $tn;
            }

            $strcut = substr($string, 0, $n);

        } else {
            for ($i = 0; $i < $length; $i++) {
                $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            }
        }

        $strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        $pos = strrpos($strcut, chr(1));
        if ($pos !== false) {
            $strcut = substr($strcut, 0, $pos);
        }
        return $strcut . $dot;
    }

    /**
     * 自动转换字符集 支持数组转换
     *
     *
     */
    public static function autoCharset($string, $from = 'gbk', $to = 'utf-8')
    {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to   = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($string) || (is_scalar($string) && !is_string($string))) {
            //如果编码相同或者非字符串标量则不转换
            return $string;
        }
        if (is_string($string)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($string, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $string);
            } else {
                return $string;
            }
        } elseif (is_array($string)) {
            foreach ($string as $key => $val) {
                $_key          = self::autoCharset($key, $from, $to);
                $string[$_key] = self::autoCharset($val, $from, $to);
                if ($key != $_key)
                    unset($string[$key]);
            }
            return $string;
        } else {
            return $string;
        }
    }

    // 模拟post提交
    public static function redirectWithPost($url, $param)
    {
        $str = "<form action=\"{$url}\" method=\"post\" name=\"frm\">";
        foreach ($param as $k => $v) {
            $str .= "<input type=\"hidden\" name=\"" . htmlentities($k) . "\" value=\"" . htmlentities($v) . "\">";
        }
        $str .= "</form>
        <script language=\"JavaScript\">document.frm.submit();</script>";
        echo $str;
    }


    /*
    * 函数: 取得一个<已打开的文件>的最后一行字符串
    * 原理: 通过判断，得到最后的2个行号的位置，它们的区间就是最后一行。
    * 参数: $file 查找的文件 ；$tmp_line_num指定文件指针移动到文件末尾的(最后)第几个字符?(应尽可能地小)
    * 返回: 该文件最后一行的字符串内容
    */
    public static function get_file_lastline($file, $tmp_line_num = -1)
    {
        // 检测文件是否存在
        !file_exists($file) && tip_msg("出错了. get_file_lastline函数 => 参数使用不当. {$file}  文件不存在.");
        $fp = fopen($file, 'rb');
        // 指定文件指针移动到文件末尾的(最后)第几个字符?
        $p = fseek($fp, $tmp_line_num, SEEK_END);
        // 先通过fgets函数，读取<当前指针位置>到<该位置到行尾>的整行字符串
        // 将字符串去头尾空格。
        $line_str = trim(fgets($fp));
        // fgets会移动指针到行末尾尾部，现在将指针倒回原位置
        fseek($fp, $tmp_line_num, SEEK_END);
        // 如果处理后，该字串为空值，就持续地往后倒移文件指针，直到找到一个行号。从而确保该行号就是有内容的最后一行的标记。
        $curpos = $tmp_line_num;
        if (empty($line_str) or $line_str == '' or $line_str == ' ') {
            while (fseek($fp, --$curpos, SEEK_END) !== -1) {
                $line_str = fgetc($fp);
                // 兼容win、like *nux  判断行号\n 或 \r
                if ($line_str == "\n" or $line_str == "\r") {
                    break; // 找到后退出循环
                }
            }
        }
        // 如果上一步$str非空值，则往后倒找另一个行尾标记。2个行尾标记之间的内容，就是有内容(非空的)的最后一行字符串
        while (fseek($fp, --$curpos, SEEK_END) !== -1) {
            $line_str = fgetc($fp);
            // 兼容win、like *nux  判断行号\n 或 \r
            if ($line_str == "\n" or $line_str == "\r") {
                break; // 找到后退出循环
            }
        }
        $line_str = fgets($fp);
        fclose($fp);
        return $line_str;
    }


    public static function getClientIp()
    {
        $ip      = 'unknown';
        $unknown = 'unknown';

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            // 使用透明代理、欺骗性代理的情况
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            // 没有代理、使用普通匿名代理和高匿代理的情况
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP'] && strcasecmp($_SERVER['HTTP_X_REAL_IP'], $unknown)) {
            // 使用百度CDN的情况
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }

        // 处理多层代理的情况
        if (strpos($ip, ',') !== false) {
            // 输出第一个IP
            $ip = reset(explode(',', $ip));
        }

        return $ip;
    }

    /**
     * 获取设备平台
     *
     * @return string
     */
    public static function getDeviceType()
    {
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type  = 'PC';
        //分别进行判断
        if (strpos($agent, 'iphone')) {
            $type = 'iPhone';
        } else if(strpos($agent, 'ipad')){
            $type = 'iPad';
        } else if (strpos($agent, 'android')) {
            $type = 'Android';
        }
        return $type;
    }

    /**
     * 生成随机字符串
     * @param  integer $length 长度
     * @param  integer $type  类型 1-字符和数字，2-字母，3-数字
     * @param  boolean $upperLower 是否区分大小写
     * @return string
     */
    public static function createRandString($length, $type=1, $upperLower=false)
    {
        $chars1 = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $chars2 = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars3 = '0123456789';
        if($type == 2){
            $chars = $chars2;
        }elseif($type == 3){
            $chars = $chars3;
        }else{
            $chars = $chars1;
        }

        $str = '';
        $strLength = strlen($chars);
        for ($i=0; $i<$length; $i++)
        {
            $str .= $chars[mt_rand(0, $strLength-1)];
        }

        return $upperLower ? strtoupper($str) : $str;

    }
}


