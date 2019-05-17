<?php
namespace app\demo\controller;

use cmf\controller\AdminBaseController;

class AdminsController extends AdminBaseController
{
    public function index()
    {
        return $this->fetch();
    }
    public function alert(){
        return $this->fetch();
    }
}