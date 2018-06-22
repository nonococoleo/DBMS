<?php

namespace app\tlr\controller;

use app\tlr\model\RefundModel;
use think\Controller;
use think\Request;

class Refund extends Controller
{
    public function index(Request $request)
    {
        $Refund = new RefundModel();
        $refund = $Refund->paginate(10, false, ['type' => 'bootstrap']);
        $page = $refund->render();

        $data = ["refund" => $refund, "page" => $page, "name" => null];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls; 
    }
}
