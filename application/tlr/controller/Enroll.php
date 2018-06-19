<?php

namespace app\tlr\controller;

use app\tlr\model\EnrollModel;
use think\Controller;
use think\Request;

class Enroll extends Controller
{
    public function index(Request $request)
    {
        $Enroll = new EnrollModel();
        $enroll = $Enroll->paginate(10, false, ['type' => 'bootstrap']);
        $page = $enroll->render();

        $data = ["enroll" => $enroll, "page" => $page, "name" => null];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

}
