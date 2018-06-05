<?php
namespace shangxin\zt;
use yii\base\Component;
class Zt extends Component {


    public $params = [];

    public static $_error = '';

    public $username = 'hzjdhy';
    public $password = '5eAcgq';
    public $market_username = 'hzjdyx';
    public $market_password = 'EP6X8s';
    public $voice_username = 'hzjdyzm';
    public $voice_password = 'VhYS7V';


    # 单条接口地址
    public $_single_url = 'http://www.api.zthysms.com/sendSms.do';
    # 语音接口地址
    public $_voice_url = 'http://www.yzmsms.cn/sendSmsYY.do';
    # 批量接口地址
    public $_batch_url = 'http://www.api.zthysms.com/sendSmsBatch.do';
    # 批量定时接口地址（暂不支持）
    public $_batch_time_url = 'http://www.api.zthysms.com/sendSmsBatchTiming.do';
    # 批量个性化接口地址
    public $_batch_identity_url = 'http://www.api.zthysms.com/sendSmsBatchIdentity.do';

    public  $_tkey = 0;

    public function before($content){
        $this->params['_tkey'] = date('YmdHis',time());
        $this->params['content'] = $content;

//        $this->params['password'] = md5(md5($this->password) . $this->params['_tkey']);
    }

    /**
     * 发送单条消息
     */
    public function sendSingleSms($phone, $content)
    {
        $this->params = array_merge(
            [
                'username'=>$this->username,
                'mobile'=>$phone,
                'password'=>md5(md5($this->password) . $this->params['_tkey'])
            ],$this->params);


//        $this->params = array_merge([
//            'username' => $this->username,
//            'mobile' => $phone,
//            'content' => $content,
//        ],  $this->params);

        $sendRes = $this->sendRequest($this->_single_url, $this->params);
        $sendArr = explode(',', $sendRes);
        return $sendArr;
    }

    /**
     * 发送语音
     */
    public function sendVoiceSMs($phone, $content)
    {
//        $this->params = array_merge([
//            'username' => $this->voice_username,
//            'mobile' => $phone,
//            'content' => $content,
//        ],  $this->params);
        $this->params = array_merge(
            [
                'username'=>$this->voice_username,
                'mobile'=>$phone,
                'password'=>md5(md5($this->voice_password) . $this->params['_tkey'])
            ],$this->params);

        $sendRes = $this->sendRequest($this->_voice_url, $this->params);
        $sendArr = explode(',', $sendRes);
        return $sendArr;
    }

    /**
     * 批量发送消息
     */
    public function sendBatchSms($phones, $content)
    {
//        $param = array_merge([
//            'username' => self::MARKET_USERNAME,
//            'mobile' => $phones,
//            'content' => $content,
//        ],  $this->getCommonFields(self::MARKET_PASSWORD));

        $this->params = array_merge(
            [
                'username'=>$this->market_username,
                'mobile'=>$phone,
                'password'=>md5(md5($this->market_password) . $this->params['_tkey'])
            ],$this->params);

        $sendRes = $this->sendRequest($this->_batch_url, $param);
        $sendArr = explode(',', $sendRes);
        return $sendArr;
    }


    public function sendRequest($url, $param)
    {
        $requestRes = $this->sendPost($url, $param, [], true);
        return $requestRes;
    }

    public static function getError()
    {
        return self::$_error;
    }

    public function sendPost($url, $param, $header = [], $isPost = true)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($param)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, false); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
        $result = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Error POST'.curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return $result; // 返回数据
    }

}