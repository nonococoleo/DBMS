<?php

namespace app\tlr\controller;
use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index(Request $request)
    {
        $htmls = $this->fetch();
        $temp = 'grthr';
        // 将数据返回给用户
        return $htmls;
    }
}
