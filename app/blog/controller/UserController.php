<?php
namespace app\blog\controller;

use cmf\controller\UserBaseController;

class UserController extends UserBaseController
{
    public function articles()
    {
        return $this->fetch();
    }
}