<?php

namespace app\tlr\controller;

use app\tlr\model\InvoiceModel;
use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use think\Controller;
use think\Request;

class Invoice extends Controller
{
//    public function index(Request $request)
//    {
//        $Invoice = new InvoiceModel();
//        $invoice=$Invoice->where("delflag", "=", 0)->paginate(10, false, ['type' => 'bootstrap']);
//        $page = $invoice->render();
//
//        $data = ["invoice" => $invoice, "page" => $page, "state" => 0];
//        $this->assign($data);
//        $htmls = $this->fetch('index');
//        return $htmls;
//    }

    public function index(Request $request)
    {
        $Invoice = new InvoiceModel();
        $state = $request->param('state');
        if ($state) {
            $query = ["state" => $state];
            $invoice = $Invoice->where("delflag", "=", 0)->where("state", "=", "$state")->paginate(10, false, ['type' => 'bootstrap']);
        } else {
            $query = ["state" => 0];
            $invoice = $Invoice->where("delflag", "=", 0)->paginate(10, false, ['type' => 'bootstrap']);
        }
        if ($invoice->count() > 0) {
            $page = $invoice->render();

            $data = array_merge(["invoice" => $invoice, "page" => $page], $query);
            $this->assign($data);

            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("此人未开发票", $_SERVER["HTTP_REFERER"], null, 1);
            return null;
        }
    }

    // 根据ID获取发票
    public function invById(Request $request)
    {
        $Invoice = new InvoiceModel();
        echo json_encode(array('inv'=>$Invoice->where('iid', $_POST['iid'])->find(),'success'=>true));
    }

    public function add(Request $request)
    {
        if (session('uid')) {
            $Invoice = new InvoiceModel();
            $Log = new LogModel();
            $Pay = new PayModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Invoice->save($_POST);
            $Pay->save(["iid" => $Invoice->getLastInsID()], ['pid' => $request->param("pid")]);
            $Log->save(["uid" => session('uid'), "action" => $Invoice->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("添加成功", "Invoice/index", null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function del(Request $request)
    {
        if (session('uid')) {
            $Invoice = new InvoiceModel();
            $Log = new LogModel();
            $Invoice->allowField(['delflag'])->save(["delflag" => 1], ['iid' => $request->param("iid")]);
            $Log->save(["uid" => session('uid'), "action" => $Invoice->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function mod(Request $request)
    {
        if (session('uid')) {
            $Invoice = new InvoiceModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Invoice->allowField(['pid', 'fee', 'title', 'number', 'date', 'state', 'memo'])->save($_POST, ['iid' => $request->param("iid")]);
            $Log->save(["uid" => session('uid'), "action" => $Invoice->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

}
