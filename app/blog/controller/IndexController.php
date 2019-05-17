<?php
namespace app\blog\controller;

use cmf\controller\HomeBaseController;

class IndexController extends HomeBaseController{

    // 首页
    public function index(){
        $this->assign('rt','waht?');
        return $this->fetch();
    }

}