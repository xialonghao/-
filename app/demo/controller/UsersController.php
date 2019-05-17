<?php
namespace app\demo\controller;

use cmf\controller\UserBaseController;

class UsersController extends UserBaseController
{
    public function articles()
    {
        return $this->fetch();
    }
}