<?php

namespace app\tlr\controller;
use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index(Request $request)
    {
        $name = 'index';
        $this->assign(['name' => $name]);
        $htmls = $this->fetch('index');
        return $htmls;
    }
}
