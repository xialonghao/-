<?php
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\db\Query;
use think\Loader;

class ShopController extends AdminBaseController{
    public function defult(){
    }
    public function index(){
        $res = Db::table('cmf_shop')->select();
        $this->assign('shop',$res);
        return $this->fetch();
    }
    public function shopadd(){
        return $this->fetch();
    }
    public function addpost(){
        $name = $this->request->request('username');
        $photo = $this->request->request('photo_url');
        $winrate = $this->request->request('winrate');
        $coding = $this->request->request('coding');
        $res = Db::table('cmf_asset')->where('filename="'.$photo.'"')->field('file_path')->find();
//        print_r($res);die;
//        print_r(cmf_get_user_avatar_url($photo));die;
//        $images = cmf_get_image_url($photo);
//        $dizhi = explode("/",$images,5);
//        $dz = '/'.$dizhi[3].'/admin/'.date('Ymd',time()).'/'.$dizhi[4];
        $arr = array(
            'winrate'=>$winrate,
            'shopname'=>$name,
            'image'=>'/upload/'.$res['file_path'],
            'coding'=>$coding,
        );
        $res = Db::table('cmf_shop')->insert($arr);
        if($res){
            $this->success('成功');
        }else{
            $this->error('失败');
        }
    }
    public function delete()
    {
        $id = $this->request->param('id');
        $res = Db::table('cmf_shop')->where("id", $id)->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    public function updata()
    {
        $content = hook_one('admin_user_edit_view');
        if (!empty($content)) {
            return $content;
        }
        $id    = $this->request->param('id');
        $res = Db::table('cmf_shop')->where('id="'.$id.'"')->find();
        $this->assign('res',$res);
        return $this->fetch();
    }
    public function addupdate(){
        $id = $this->request->request('Id');
        $name = $this->request->request('username');
        $image = $this->request->request('photo_url');
        $winrate = $this->request->request('winrate');
        $coding = $this->request->request('coding');
        $res = Db::table('cmf_asset')->where('filename="'.$image.'"')->field('file_path')->find();
        $date = array(
            'Id'=>$id,
            'winrate'=>$winrate,
            'shopname'=>$name,
            'image'=>'/upload/'.$res['file_path'],
            'coding'=>$coding,
        );
        $res = Db::table('cmf_shop')->update($date);
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }
    public function prize(){
        /**搜索条件**/
        $mobile= $this->request->param('mobile');
        $userEmail = trim($this->request->param('user_email'));
        $res = Db::name('prize')->alias('a')
            ->join('cmf_shop b','a.shop_id = b.id')
            ->join('cmf_wxuser c','c.Id=a.client_id')
            ->field('a.Id as user_id,c.username,c.mobile,b.shopname,b.image,a.status,b.coding,a.xxtime,a.dhtime')
            ->where(function (Query $query) use ($mobile) {
                if ($mobile) {
                    $query->where('mobile', 'like', "%$mobile%");
                }
            })

            ->paginate(10);
        $page = $res->render();
        $res->appends(['user_login' => $mobile]);
        $this->assign('res',$res);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function stu(){
        $id = $this->request->request('id');

        $res = Db::table('cmf_prize')->where('Id="'.$id.'"')->update(array('status'=>1,'dhtime'=>date('Y-m-d H:i:s',time())));

        if($res){
            return json(array(
                'code'=>200,
                'msg'=>'已兑换',
            ));
        }else{
            return json(array(
                'code'=>500,
                'msg'=>'兑换失败',
            ));
        }
    }
    public function usersname(){
//        $res = Db::table('cmf_wxuser')->paginate(10);
        /**搜索条件**/
        $mobile= $this->request->param('mobile');
        $userEmail = trim($this->request->param('user_email'));

        $users = Db::name('wxuser')
            ->where(function (Query $query) use ($mobile, $userEmail) {
                if ($mobile) {
                    $query->where('mobile', 'like', "%$mobile%");
                }

                if ($userEmail) {
                    $query->where('email', 'like', "%$userEmail%");
                }
            })
            ->order("id DESC")
            ->paginate(10);
        $users->appends(['user_login' => $mobile, 'user_email' => $userEmail]);

        $page = $users->render();
        $this->assign('res',$users);
        $this->assign('page',$page);
        return $this->fetch();
    }


}