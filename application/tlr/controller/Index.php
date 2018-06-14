<?php

namespace app\tlr\controller;
use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index(Request $request)
    {
        $name = 'index';
        $stu = "./" . url("tlr/student");
        $this->assign(['name' => $name, 'stu' => $stu]);
        $htmls = $this->fetch('index');
        return $htmls;
    }
}
