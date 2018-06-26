<?php

namespace app\tlr\controller;

use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use app\tlr\model\RefundModel;
use think\Controller;
use think\Request;


class Refund extends Controller
{
    public function index(Request $request)
    {
        $Refund = new RefundModel();
        $state = $request->param('state');
        if ($state) {
            $query = ["state" => $state];
            $refund = $Refund->where("delflag", "=", 0)->where("state", "=", "$state")->paginate(10, false, ['type' => 'bootstrap']);
        } else {
            $query = ["state" => 0];
            $refund = $Refund->where("delflag", "=", 0)->paginate(10, false, ['type' => 'bootstrap']);
        }
        if ($refund->count() > 0) {
            $page = $refund->render();

            $data = array_merge(["refund" => $refund, "page" => $page], $query);
            $this->assign($data);

            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("查无记录", $_SERVER["HTTP_REFERER"], null, 1);
            return null;
        }
    }

    // 根据ID获取退费
    public function refById(Request $request)
    {
        $Refund = new RefundModel();
        echo json_encode(array('ref'=>$Refund->where('rid', $_POST['rid'])->find(),'success'=>true));
    }

//    public function search(Request $request)
//    {
//        $Refund = new RefundModel();
//        $stuid = $request->param('stuid');
//        $query = ["stuid" => $stuid];
//        $refund = $Refund->table('refund')->where(refund.sid ,eq,"%$stuid%")->where("delflag", "=", 0)->order('rid', 'asc')->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
//        if ($refund->count() > 0) {
//            $page = $refund->render();
//
//            $data = array_merge(["refund" => $refund, "page" => $page], $query);
//            $this->assign($data);
//
//            $htmls = $this->fetch("index");
//            return $htmls;
//        } else {
//            $this->error("此人未退款", $_SERVER["HTTP_REFERER"], null, 1);
//            return null;
//        }
//    }

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
            $this->success("添加成功", "Refund/index", null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function del(Request $request)
    {
        if (session('uid')) {
            $Refund = new RefundModel();
            $Log = new LogModel();
            $Refund->allowField(['delflag'])->save(["delflag" => 1], ['rid' => $request->param("rid")]);
            $Log->save(["uid" => session('uid'), "action" => $Refund->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

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
            $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }
}
