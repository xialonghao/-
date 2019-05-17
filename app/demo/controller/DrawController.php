<?php
namespace app\demo\controller;
use cmf\controller\HomeBaseController;
use think\Db;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: toresetpwdken, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
class DrawController extends HomeBaseController
{
//            $result = array();
//            $arrs =array();
//            foreach ($arr as $key=>$val){
//                $arrs[] = $val['winrate'];
//
//            }
//            $proSum = array_sum($arrs);
////            print_r($proSum);die;
//            asort($arrs);
//            foreach($arrs as $k=>$v){
////                print_r($v);
//                $randNum = mt_rand(1,$proSum);
//                if($randNum<=$v){
//                    $result = $arrs[$k];break;
//                }else{
//                    $proSum -=$v;
//                }
//            }
//        }
//        return $result;
    public function index()
    {
        $attention = 0;
        if ($attention == 0) {
            $username = $this->request->request('usernmae');
            $email = $this->request->request('email');
            $company_name = $this->request->request('company_name');
            $mobile = $this->request->request('mobile');
            $date = array(
                'username' => $username,
                'email' => $email,
                'company_name' => $company_name,
                'mobile' => $mobile,
            );
            $res = Db::table('cmf_userinfo')->insert($date);
            if ($res) {
                $re = Db::table('cmf_sms')->where('mobile="' . $mobile . '"')->delete();
                $ret = Db::table('cmf_shop')->select();
//            echo"<pre>";
                $prize_arr = json_decode(json_encode($ret, true), true);
                $arr = array();
                foreach ($prize_arr as $key => $val) {
                    $arr[$val['Id']] = $val['winrate'];
                }
            echo "<pre>";
            print_r($arr);
            die;
                $rid = $this->get_rand($arr); //根据概率获取奖项id
//                print_r($rid);die;

                $res_two['yes'] = $prize_arr[$rid - 1]['shopname']; //中奖项

                unset($prize_arr[$rid - 1]); //将中奖项从数组中剔除，剩下未中奖项
                shuffle($prize_arr); //打乱数组顺序
                for ($i = 0; $i < count($prize_arr); $i++) {
                    $pr[] = $prize_arr[$i]['shopname'];
                }
                $res_two['no'] = $pr; //未中奖项
                $user_mobile = Db::table('cmf_userinfo')->where('mobile="18521940531"')->find();
                $user_shop = Db::table('cmf_shop')->where('shopname="'.$res_two['yes'].'"')->find();
                $inser = array(
                    'client_id'=>$user_mobile['Id'],
                    'shop_id'=>$user_shop['Id'],
                    'status'=>0
                );
                $inf = Db::table('cmf_prize')->insert($inser);
                return json(array(
                    'code'=>200,
                    'msg'=>'以获取商品',
                    'date'=>$res_two,
                ));
            } else {

            }

        }
    }
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
        $host = "https://chanyoo.market.alicloudapi.com";
        $path = "/sendsms";
        $method = "GET";
        $appcode = "ebac1ff5ed70476d9c84efa8e412a5d0";
        $mobile = "13053985332";
        $suiji = urlencode(rand(100000,999999));
        $now = date('Y-m-d H:i:s',time());
        $shijian_one = Db::table('cmf_sms')->where('mobile="'.$mobile.'"')->find();

        if(Db::table('cmf_sms')->where('mobile="'.$mobile.'" and time<="'.date('Y-m-d H:i:s',time()).'"')->delete()){
            echo'验证码已过期';
           Db::table('cmf_sms')->where('mobile="'.$mobile.'"')->delete();
        }else if($shijian_one['time']==''){
        $this->sms_verification($mobile,$suiji,date("Y-m-d H:i:s",strtotime("+5minutes",strtotime($now))));
        $content = "您的手机号：".$mobile."，验证码：".$suiji."，请及时完成验证，如不是本人操作请忽略。【东浩兰生集团】";

        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "mobile=".$mobile."&content=".$content."";
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
        //print_r($res);
        $res = json_decode($res,true);
        print_r($res);

    }
    }
    function sms_verification($mobile,$code,$time){
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
}



?>
