<?php

namespace app\tlr\controller;

use app\tlr\model\ClassModel;
use think\Controller;
use think\Request;

class Enroll extends Controller
{
    public function index(Request $request)
    {
        $Class = new ClassModel();
//        $enroll = $Enroll->paginate(10, false, ['type' => 'bootstrap']);
//        $page = $enroll->render();
        $class = $Class->distinct("true")->column("semester");
        $data = ["seme" => $class];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }
}
