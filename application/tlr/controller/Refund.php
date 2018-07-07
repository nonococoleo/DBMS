<?php

namespace app\tlr\controller;

use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use app\tlr\model\RefundModel;
use app\tlr\model\SemesterModel;
use think\Controller;
use think\Request;


class Refund extends Controller
{
    //首页显示所有退费情况
    public function index(Request $request)
    {
        $Refund = new RefundModel();
        $state = $request->param('state');
        $seme = $request->param('seme');
        $Semester = new SemesterModel();
        $semester = $Semester->select();
        $refund = $Refund->where("delflag", "=", 0);
        if (!$state)
            $state = 0;
        else
            $refund = $refund->where("state", "=", "$state");
        if (!$seme)
            $seme = 0;
        else
            $refund = $refund->where("semester", "=", $seme);
        $refund = $refund->paginate(10, false, ['type' => 'bootstrap']);

        if ($refund->count() > 0) {
            $page = $refund->render();

            $data = ["refund" => $refund, "page" => $page, "semester" => $semester, "state" => $state, "seme" => $seme];
            $this->assign($data);

            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("查无记录", null, null, 1);
            return null;
        }
    }

    //根据ID获取退费
    public function refById(Request $request)
    {
        $Refund = new RefundModel();
        echo json_encode(array('ref'=>$Refund->where('rid', $_POST['rid'])->find(),'success'=>true));
    }

    //添加退费信息接口
    public function add(Request $request)
    {
        if (session('uid')) {
            $Refund = new RefundModel();
            $Log = new LogModel();
            $Pay = new PayModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Refund->save($_POST);
            $Pay->save(["rid" => $Refund->getLastInsID()], ['pid' => $request->param("pid")]);
            $Log->save(["uid" => session('uid'), "action" => $Refund->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("添加成功", null, null, 1);
        } else {
            $this->error("没有权限", null, null, 1);
        }
        return null;
    }

    //删除退费信息接口
    public function del(Request $request)
    {
        if (session('uid')) {
            $Refund = new RefundModel();
            $Log = new LogModel();
            $Refund->save(["delflag" => 1], ['rid' => $request->param("rid")]);
            $Log->save(["uid" => session('uid'), "action" => $Refund->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("删除成功", null, null, 1);
        } else {
            $this->error("没有权限", null, null, 1);
        }
        return null;
    }

    //修改退费信息接口
    public function mod(Request $request)
    {
        if (session('uid')) {
            $Refund = new RefundModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Refund->allowField(['pid', 'semster', 'fee', 'method', 'card', 'bank', 'person', 'date', 'state', 'memo'])->save($_POST, ['rid' => $request->param("rid")]);
            $Log->save(["uid" => session('uid'), "action" => $Refund->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("修改成功", null, null, 1);
        } else {
            $this->error("没有权限", null, null, 1);
        }
        return null;
    }
}
