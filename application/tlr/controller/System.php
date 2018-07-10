<?php

namespace app\tlr\controller;

use app\tlr\model\EnrollModel;
use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use app\tlr\model\RefundModel;
use app\tlr\model\SemesterModel;
use gmars\rbac\Rbac;
use think\Controller;
use think\Request;

class System extends Controller
{
    //首页 统计信息
    public function index(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "index/index", null, 1);
            exit();
        }
        $seme = $request->param("seme");
        if (!$seme)
            $seme = session("cur_semester");
        $query = ["seme" => $seme];
        $Enroll = new EnrollModel();
        $enroll = $Enroll->alias("e")->where("e.delflag", "=", 0)->join("course c", "c.cid=e.cid")->where("semester", "=", $seme)->field("count(*) sum,cname,price*unit price,price*unit*count(*) rev")->group("e.cid")->order("rev", "desc")->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
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

    //设置当前学期 接口
    public function setseme(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", null, null, 3);
            exit();
        }
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

//导出缴费csv
    public function csv_pay(Request $request)
    {
        $seme = $request->param("seme");
        $Pay = new PayModel();
        $data = $Pay->where("semester", $seme)->select();
        $str = 'pid' . ',' . 'delflag' . ',' . 'sid' . ',' . 'semester' . ',' . 'fee' . ',' . 'detail' . ',' . 'method' . ',' . 'iid' . ',' . 'rid' . ',' . 'date' . ',' . 'uid' . ',' . 'memo' . "\n";
        foreach ($data as $key => $value) {
            $str .= $value['pid'] . ',' . $value['delflag'] . ',' . $value['sid'] . ',' . $value['semester'] . ',' . $value['fee'] . ',' . $value['detail'] . ',' . $value['method'] . ',' . $value['iid'] . ',' . $value['rid'] . ',' . $value['date'] . ',' . $value['uid'] . ',' . $value['memo'] . "\n";
        }
        $filename = './缴费明细.csv';
        header('Content-type:text/csv');
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $str;
    }

//    导出退费csv
    public function csv_refund(Request $request)
    {
        $seme = $request->param("seme");
        $Refund = new RefundModel();
        $data = $Refund->where("semester", $seme)->select();
        $str = 'rid' . ',' . 'delflag' . ',' . 'pid' . ',' . 'semester' . ',' . 'fee' . ',' . 'detail' . ',' . 'method' . ',' . 'card' . ',' . 'bank' . ',' . 'person' . ',' . 'date' . ',' . 'state' . ',' . 'uid' . ',' . 'memo' . "\n";
        foreach ($data as $key => $value) {
            $str .= $value['rid'] . ',' . $value['delflag'] . ',' . $value['pid'] . ',' . $value['semester'] . ',' . $value['fee'] . ',' . $value['detail'] . ',' . $value['method'] . ',' . $value['card'] . ',' . $value['bank'] . ',' . $value['person'] . ',' . $value['date'] . ',' . $value['state'] . ',' . $value['uid'] . ',' . $value['memo'] . "\n";
        }
        $filename = './退费明细.csv';
        header('Content-type:text/csv');
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
//
//        $file = fopen($filename, 'w');
//        fwrite($file, chr(OxEF) . chr(OxBB) . chr(OxBF));
//        foreach ($str as $str) {
//            fputcsv($file, $str);
//        }
//


        echo $str;
    }

    public function test()
    {
        $filename = 'G:\Users\HP\PhpstormProjects\thinkphp\public\uploads\test.csv';
        $file = fopen($filename, 'w');
        fwrite($file, chr(OxEF) . chr(OxBB) . chr(OxBF));
//        foreach ()
    }




}
