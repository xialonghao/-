<?php
namespace app\wx\controller;
use think\Controller;
define("TOKEN","dys");
class WxController extends Controller
{
    public function robots()
    {
    $timestamp = $this->request->request('timestamp');;
    $nonce = $this->request->request('nonce');
    //1）将token、timestamp、nonce三个参数进行字典序排序
    $arr = array(TOKEN, $timestamp, $nonce);
    sort($arr, SORT_STRING);
    //2）将三个参数字符串拼接成一个字符串进行sha1加密
    $str = implode('', $arr);
    $str = sha1($str);
    //3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
    if ($str ==  $this->request->request('signature')) {
        echo $this->request->request('echostr');
    }
    }
}
?>