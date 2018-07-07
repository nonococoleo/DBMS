<?php

namespace app\tlr\controller;

use app\tlr\model\EnrollModel;
use app\tlr\model\LogModel;
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
        $enroll = $Enroll->alias("e")->where("e.delflag", "=", 0)->join("course c", "c.cid=e.cid")->where("semester", "=", $seme)->field("count(*) sum,cname,price*unit*count(*) price")->group("e.cid")->paginate(10, false, ['type' => 'bootstrap']);
        $page = $enroll->render();
        $Pay = new PayModel();
        $pay = $Pay->where("delflag", "=", 0)->where("semester", "=", $seme)->field("sum(fee) price")->select();
        $Refund = new RefundModel();
        $refund = $Refund->where("delflag", "=", 0)->where("semester", "=", $seme)->field("sum(fee) price")->select();
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->select();
        $data = ["enroll" => $enroll, "semester" => $semester, "seme" => $seme, "pay" => $pay[0], "refund" => $refund[0], "page" => $page];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function setseme(Request $request)
    {
        $seme = $request->param("seme");
        if ($seme) {
            $Semester = new SemesterModel();
            $Semester->where("id", "=", $seme)->setField("current", 1);
            $Log = new LogModel();
            $Log->save(["uid" => session('uid'), "action" => $Semester->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $Semester->where("id", "<>", $seme)->setField("current", 0);
            session("cur_semester", $seme);
            $this->success("更改成功", null, null, 1);
        } else {
            $this->error("确定学期", 'system/index', null, 2);
        }
    }
}
