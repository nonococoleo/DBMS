<?php

namespace app\tlr\controller;
use think\Controller;
use app\tlr\model\IndexModel;
use think\Request;

class Index extends Controller
{
    public function index(Request $request)
    {
        $htmls = $this->fetch();

        // 将数据返回给用户
        return $htmls;
    }
}
