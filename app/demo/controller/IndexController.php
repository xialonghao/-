<?php
namespace app\demo\controller;
use cmf\controller\HomeBaseController;
use think\console\Table;
use think\Db;
use think\Session;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: toresetpwdken, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
class IndexController extends HomeBaseController
{
    public $appid = "wxc512b8496a77a2d8";
    public $appsecret = "e69220cb2831cdfce91a749b2cc45ca5";
    public function what(){
        echo phpinfo();
    }
    public function huidiao(){
        $code = $_GET['code'];
//        header("Location:http://www.opjpg.com?code=".$code);
        echo $code;
//        $_SESSION['code'] = $code;
    }
    public function codes(){
       return json(array(
           'code'=>1,
       ));
    }
    public function index()
    {
//        $callback = "http://game.iars-expo.com/demo/index/huidiao";
        $callback="http://www.opjpg.com";
//        $code = $this->request->param('code');//获取code值
        $userinfourl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appid . "&redirect_uri=" . urlencode($callback) . "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header("Location:" . $userinfourl);


    }

    public function toke()
    {

    }

    public function jiekou()
    {
        $appid = 'wxc512b8496a77a2d8';
        $appsecret = 'e69220cb2831cdfce91a749b2cc45ca5';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret . '';
        //初始化一个新的会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = json_decode(curl_exec($ch), true);
        // curl_exec抓取URL并把它传递给浏览器;
        //curl_close($ch)关闭cURL资源，并且释放系统资源
        curl_close($ch);
        $token = $output['access_token'];
        return $token;
    }

    public function shilie()
    {
        $token = $this->jiekou();
        $data = '{
                    "button":[
                    {
                    "name":"活动",
                    "sub_button":[
                    {
                    "type":"view",
                    "name":"抽奖",
                    "url":"http://game.iars-expo.com"
                    },
                    ]
                    },
                    ]
                    }';
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $token;
        //设置一个新的会话也是url
        $ch = curl_init();
        // currl_setopt设置一个cURL传输选项。
        // 	CURLOPT_URL需要获取的URL地址，也可以在curl_init()函数中设置。
        curl_setopt($ch, CURLOPT_URL, $url);
        //CURLOPT_CUSTOMREQUEST请求方式
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //验证url的设置false就行
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //CURLOPT_SSL_VERIFYHOST检查服务器SSL证书中是否存在一个公用名
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //CURLOPT_USERAGENT在HTTP请求中包含一个"User-Agent: "头的字符串。
        curl_setopt($ch, CURLOPT_USERAGENT, 'xialonghao');
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //CURLOPT_AUTOREFERER当根据Location:重定向时，自动设置header中的Referer:信息。
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        //接受全部数据使用HTTP协议中的"POST"操作来发送
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 	在启用CURLOPT_RETURNTRANSFER的时候，返回原生的（Raw）输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }
        curl_close($ch);
        echo $tmpInfo;

    }

    public function shouye()
    {
        $code =  $this->request->request('code');//获取code值
        $userinfourl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=" . $code . "&grant_type=authorization_code";
        $objuser = file_get_contents($userinfourl);
        $objtoken = json_decode($objuser);
        $access_token = $objtoken->access_token;
        $openid = $objtoken->openid;//获取用户的openid
        $refresh_token = $objtoken->refresh_token;
//        print_r($objtoken);die;
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $this->appid . "&grant_type=refresh_token&refresh_token=".$refresh_token."";
        $objuser_s = file_get_contents($url);
        $objtoken_s = json_decode($objuser_s);
        $access_token_s = $objtoken_s->access_token;
        $openid_s = $objtoken_s->openid;//获取用户的openid
//        print_r($objtoken);die;
        $snsapi_userinfo = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token_s."&openid=".$openid_s."&lang=zh_CN";
        $objuser_two = file_get_contents($snsapi_userinfo);
        $objtoken_two = json_decode($objuser_two);
//        print_r($objtoken_two);die;
        $date=array(
            'openid'=>$objtoken_two->openid,
            'nickname'=>$objtoken_two->nickname,
            'sex'=>$objtoken_two->sex,
            'language'=>$objtoken_two->language,
            'city'=>$objtoken_two->city,
            'province'=>$objtoken_two->province,
            'country'=>$objtoken_two->country,
            'headimgurl'=>$objtoken_two->headimgurl,
        );

//        die;
//        $getinfourl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
//        $info=file_get_contents($getinfourl);
//        $users=json_decode($info);
//        print_r($users);

        $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret."";
        $access_msg = json_decode(file_get_contents($access_token));
        $token = $access_msg->access_token;
        $subscribe_msg = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";
        $subscribe = json_decode(file_get_contents($subscribe_msg));
//        print_r($subscribe);
        $gzxx = $subscribe->subscribe;
        $res = Db::table('cmf_wxuser')->where('openid="'.$objtoken_two->openid.'"')->find();
        if ($gzxx === 1) {
           if(empty($res)){
               Db::table('cmf_wxuser')->insert($date);
               return json(
                   array(
                       'code'=>'已关注',
                       'opednid'=>$objtoken_two->openid,
                       'mobile'=>$res['mobile'],
                   )
               );
           }
            return json(
                array(
                    'code'=>'已关注',
                    'opednid'=>$objtoken_two->openid,
                    'mobile'=>$res['mobile'],
                )
            );
        } else {
            if(empty($res)){
                Db::table('cmf_wxuser')->insert($date);
                return json(
                    array(
                        'code'=>'未关注',
                    )
                );
            }
//            $res = Db::table('cmf_wxuser')->insert($date);
            return json(
                array(
                    'code'=>'未关注',
                )
            );
        }
    }
    public function usersname(){

            $username = $this->request->request('usernmae');
            $email = $this->request->request('email');
            $company_name = $this->request->request('company_name');
            $mobile = $this->request->request('mobile');
            $address = $this->request->request('address');
            $verify = $this->request->request('verify');
            $openid = $this->request->request('openid');
            $date = array(
                'username' => $username,
                'email' => $email,
                'company_name' => $company_name,
                'mobile' => $mobile,
                'address'=>$address,
            );
            $res = Db::table('cmf_wxuser')->where('openid="'.$openid.'"')->update($date);

            $res_one = Db::table('cmf_sms')->where('mobile="'.$mobile.'" and code="'.$verify.'"')->count();
            if($res_one==0){
                return json(array(
                    'code'=>400,
                    'msg'=>'验证码有误',
                    'data'=>0,
                ));
            }

                $rest = Db::table('cmf_sms')->where('mobile="' . $mobile . '"')->delete();
                if($rest){
                    return json(array(
                        'code'=>200,
                        'msg'=>'添加成功',
                        'data'=>1
                    ));
                }




    }
    //判断是哪个用户是否填写用户信息
    public function pduser()
    {
        $mobile = $this->request->request('mobile');
        $res = Db::table('cmf_userinfo')->select();
        $prize_one = json_decode(json_encode($res, true), true);
        $arr = array();
        foreach ($prize_one as $key => $val) {
            if (Db::table('cmf_wxuser')->where('mobile="' . $mobile . '"')->count() == 0) {
                return json(array(
                    'code' => 400,
                    'msg' => '未填写用户信息'
                ));
            }

        }
        $ret_one = Db::table('cmf_shop')->select();
//      echo"<pre>";
        $prize_arr = json_decode(json_encode($ret_one, true), true);
        $arr = array();
        foreach ($prize_arr as $key => $val) {
            $arr[$val['Id']] = $val['winrate'];
        }

        $rid = $this->get_rand($arr); //根据概率获取奖项id

        $res_two['yes'] = $prize_arr[$rid]['shopname']; //中奖项
//        print_r($res_two);die;
        unset($prize_arr[$rid]); //将中奖项从数组中剔除，剩下未中奖项
        shuffle($prize_arr); //打乱数组顺序
        for ($i = 0; $i < count($prize_arr); $i++) {
            $pr[] = $prize_arr[$i]['shopname'];
        }
        $res_two['no'] = $pr; //未中奖项
        $user_mobile = Db::table('cmf_wxuser')->where('mobile="' . $mobile . '"')->find();
        $user_shop = Db::table('cmf_shop')->where('shopname="' . $res_two['yes'] . '"')->find();
        $inser = array(
            'client_id' => $user_mobile['Id'],
            'shop_id' => $user_shop['Id'],
            'time' =>date('Y-m-d',time()),
            'xxtime'=>date('Y-m-d H:i:s',time()),
            'status' => 0
        );
        $inf = Db::table('cmf_prize')->insert($inser);
        //
        $jiangpin = Db::table('cmf_shop')->where('shopname="' . $res_two['yes'] . '"')->find();
        $host = "https://dxyzm.market.alicloudapi.com";
        $path = "/chuangxin/dxjk";
        $method = "POST";
        $appcode = "ebac1ff5ed70476d9c84efa8e412a5d0";
        $headers = array();
        $mobile = $mobile;
//        $content = "温馨提示：您的奖品兑换码：".$jiangpin['coding']."，请您在展会期间及时到B3346展位兑换奖品，如不是本人操作请忽略。【东浩兰生集团】";
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "content=温馨提示：您的奖品兑换码：".$jiangpin['coding']."，请您在展会期间及时到B3346展位兑换奖品，如不是本人操作请忽略。【东浩兰生集团】&mobile=".$mobile."";
        //注意测试可用：【创信】你的验证码是：#code#，3分钟内有效！，发送自定义内容联系旺旺或QQ：726980650报备
        $bodys = "";
        $url = $host . $path . "?" . $querys;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($curl);
        $baocuo = json_decode($res, true);
            return json(array(
                'date' => $res_two,
                'code'=>$baocuo['ReturnStatus'],
                'msg'=>$baocuo['Message'],
                'data'=>$baocuo['SuccessCounts'],
            ));
        }
//    }
//public function whst()
//{
//    //
////    $jiangpin = Db::table('cmf_shop')->where('shopname="' . $res_two['yes'] . '"')->find();
//    $host = "https://chanyoo.market.alicloudapi.com";
//    $path = "/sendsms";
//    $method = "GET";
//    $appcode = "ebac1ff5ed70476d9c84efa8e412a5d0";
//    $mobile = '18337152380';
//    $now = date('Y-m-d H:i:s', time());
//        $content = "温馨提示：您的奖品兑换码：adfasfasd12，请您在展会期间及时到B3346展位兑换奖品，如不是本人操作请忽略。【东浩兰生集团】";
//
//        $headers = array();
//        array_push($headers, "Authorization:APPCODE " . $appcode);
//        $querys = "mobile=" . $mobile . "&content=" . $content . "";
//        $bodys = "";
//        $url = $host . $path . "?" . $querys;
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($curl, CURLOPT_FAILONERROR, false);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//        $res = curl_exec($curl);
//        $baocuo = json_decode($res, true);
//
//}
    private function get_rand($proArr) {

        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
    public function duanxin(){

        $host = "https://dxyzm.market.alicloudapi.com";
        $path = "/chuangxin/dxjk";
        $method = "POST";
        $appcode = "ebac1ff5ed70476d9c84efa8e412a5d0";
        $headers = array();
        $mobile =$this->request->request('mobile');
        $repetition = Db::table('cmf_wxuser')->where('mobile="'.$mobile.'"')->count();
        if($repetition==1){
            return json(array(
                'code'=>200,
                'msg'=>'手机号已注册',
                'data'=>$repetition,
            ));
        }
        $suiji = urlencode(rand(100000,999999));
        $now = date('Y-m-d H:i:s',time());
        $shijian_one = Db::table('cmf_sms')->where('mobile="'.$mobile.'"')->find();

        if(Db::table('cmf_sms')->where('mobile="'.$mobile.'" and time<="'.date('Y-m-d H:i:s',time()).'"')->delete()){
            echo'验证码已过期';
            Db::table('cmf_sms')->where('mobile="'.$mobile.'"')->delete();
            print_r(22222);
        }
//        else if($shijian_one['time']==''){

            $this->sms_verification($mobile,$suiji,date("Y-m-d H:i:s",strtotime("+5minutes",strtotime($now))));
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "content=【东浩兰生集团】您的手机号：".$mobile."，验证码：".$suiji."，5分钟内有效，请及时完成验证，如不是本人操作请忽略。&mobile=".$mobile."";
        $bodys = "";
        $url = $host . $path . "?" . $querys;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $res = curl_exec($curl);
            $baocuo = json_decode($res,true);
            return json(array(
            'code'=>$baocuo['ReturnStatus'],
            'msg'=>$baocuo['Message'],
            'data'=>$baocuo['SuccessCounts'],
            ));

//            }
        }

    function sms_verification($mobile,$code,$time){
//        Session::set('mobile',$mobile);
        session('mobile', $mobile);
        $date = array(
            'mobile'=>$mobile,
            'code'=>$code,
            'time'=>$time,
        );
        Db::table('cmf_sms')->insert($date);
    }
    public function prize(){
        $res = Db::table('cmf_shop')->select();
        if($res){
            return json(array(
                'code'=>200,
                'msg'=>'成功获取抽奖列表',
                'data'=>$res,
            ));
        }
    }
    public function jilu(){
        $openid = $this->request->request('openid');//'odeRt0YdXaRfK9R8iM3OfSb4W-Hc';//
        $res = Db::table('cmf_wxuser')->where('openid="'.$openid.'"')->find();
        $res_one = Db::table('cmf_prize')->where('client_id="'.$res['Id'].'"')->select();
//        print_r($res);die;
        $shop = array();
        foreach($res_one as $key=>$val){
            $shop[] =Db::table('cmf_shop')
                ->where('Id="'.$val['shop_id'].'"')->select();
        }
//        print_r($shop);die;
        return json(array(
            'code'=>200,
            'msg'=>'获取商品记录成功',
            'data'=>$shop,
            'date'=>$res_one,
        ));
    }
    //判断一天抽一次
    public function choujiang(){

        $openid = $this->request->request('openid');
        $shop_id = $this->request->request('shop_id');
        if(empty($shop_id)){

            return json(array(
                'code'=>200,
                'msg'=>'0',
                'data'=>1,
            ));
        }
        $res = Db::table('cmf_wxuser')->where('openid="'.$openid.'"')->find();
        $res_one = Db::table('cmf_prize')->where('client_id="'.$res['Id'].'" and Id in('.$shop_id.')')->order('Id asc')->select();

        foreach($res_one as $key=>$val){
            $res_two = $val['time'];
        }

//    print_r(date('Y-m-d ',time()));
//        echo"/n";
//        print_r($res_two);
//        if($res_two!=date('Y-m-d',time())){
        return json(array(
            'code'=>200,
            'msg'=>'0',
            'data'=>$res_two    ,
        ));
        }
        public function san(){
            $openid = 'odeRt0YdXaRfK9R8iM3OfSb4W-Hc';//$this->request->request('openid');//'odeRt0YdXaRfK9R8iM3OfSb4W-Hc';//
            $res = Db::table('cmf_wxuser')->where('openid="'.$openid.'"')->find();
            $res_one = Db::table('cmf_prize')->where('client_id="'.$res['Id'].'"')->select();
            $shop =0;
            foreach($res_one as $key=>$val){
                $shop += Db::table('cmf_shop')->where('id="'.$val['shop_id'].'" ')->count();

            }
          if($shop>=3) {
              return json(array(
                  'code' => 200,
                  'msg' => '以抽完三次',
                  'data' => $shop,
              ));
          }
        }
//        public function asd(){
//        $host = "https://dxyzm.market.alicloudapi.com";
//        $path = "/chuangxin/dxjk";
//        $method = "POST";
//        $appcode = "ebac1ff5ed70476d9c84efa8e412a5d0";
//        $headers = array();
//        $mobile ='18521940531';//$this->request->request('mobile');
//        $suiji = urlencode(rand(100000,999999));
//        $now = date('Y-m-d H:i:s',time());
//        $shijian_one = Db::table('cmf_sms')->where('mobile="'.$mobile.'"')->find();
//        if(Db::table('cmf_sms')->where('mobile="'.$mobile.'" and time<="'.date('Y-m-d H:i:s',time()).'"')->delete()){
//            echo'验证码已过期';
//            Db::table('cmf_sms')->where('mobile="'.$mobile.'"')->delete();
//        }else if($shijian_one['time']==''){
//            $this->sms_verification($mobile,$suiji,date("Y-m-d H:i:s",strtotime("+5minutes",strtotime($now))));
//        array_push($headers, "Authorization:APPCODE " . $appcode);
//        $querys = "content=【东浩兰生集团】您的手机号：".$mobile."，验证码：".$suiji."，5分钟内有效，请及时完成验证，如不是本人操作请忽略。&mobile=".$mobile."";
//        $bodys = "";
//        $url = $host . $path . "?" . $querys;
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
//            curl_setopt($curl, CURLOPT_URL, $url);
//            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($curl, CURLOPT_FAILONERROR, false);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//            $res = curl_exec($curl);
//            $baocuo = json_decode($res,true);
//            return json(array(
//            'code'=>$baocuo['ReturnStatus'],
//            'msg'=>$baocuo['Message'],
//            'data'=>$baocuo['SuccessCounts'],
//            ));
//
//            }
//
//    }
}
?>
