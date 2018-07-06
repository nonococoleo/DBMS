<?php

namespace app\tlr\controller;

use app\tlr\model\EnrollModel;
use app\tlr\model\PayModel;
use app\tlr\model\RefundModel;
use app\tlr\model\SemesterModel;
use think\Controller;
use think\Request;

class System extends Controller
{
    public function index(Request $request)
    {
        $seme = $request->param("seme");
        if (!$seme)
            $seme = session("cur_semester");
        $Enroll = new EnrollModel();
        $enroll = $Enroll->alias("e")->where("e.delflag", "=", 0)->join("course c", "c.cid=e.cid")->where("semester", "=", $seme)->field("count(*),cname")->group("e.cid")->select();
        $Pay = new PayModel();
        $pay = $Pay->where("delflag", "=", 0)->where("semester", "=", $seme)->field("sum(fee) 缴费总计")->select();
        $Refund = new RefundModel();
        $refund = $Refund->where("delflag", "=", 0)->where("semester", "=", $seme)->field("sum(fee) 退费总计")->select();
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->select();
        $data = ["enroll" => $enroll, "semester" => $semester, "seme" => $seme, "pay" => $pay[0], "refund" => $refund[0]];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

}
