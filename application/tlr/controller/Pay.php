<?php

namespace app\tlr\controller;

use app\tlr\model\PayModel;
use app\tlr\model\LogModel;
use think\Controller;
use think\Request;

class Pay extends Controller
{
    // 缴费页面
    public function index(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限");
        //     exit();
        // }
        return $this->fetch('index');
    }
    // 获取缴费列表
    public function pays(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Pay/index", null, 1);
        //     exit();
        // }
        if (request()->isGet()) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }
        $page = (isset($_POST['page'])) ? $_POST['page'] : 1;
        $pay = new PayModel;
        $pays = $pay->where('delflag', '0')->page($page, 10)->select();
        $totalPage = ceil(db('pay')->where('delflag', '0')->count() / 10);
        echo json_encode(array("pays" => $pays, "totalPage" => $totalPage, "success" => true));
    }

    // 修改缴费信息
    public function update(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Pay/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }

        if ($_POST['sid'] == '' || !isset($_POST['sid'])) {
            echo json_encode(array("success" => false, 'msg' => "缴费学号不能为空"));
            exit();
        }
        if ($_POST['semester'] == '' || $_POST['semester'] == null) {
            echo json_encode(array("success" => false, 'msg' => "学期不能为空"));
            exit();
        }
        if ($_POST['fee'] == '' || $_POST['fee'] == null) {
            echo json_encode(array("success" => false, 'msg' => "费用不能为空"));
            exit();
        }
        if ($_POST['date'] == '' || $_POST['date'] == null) {
            echo json_encode(array("success" => false, 'msg' => "日期不能为空"));
            exit();
        }
        if ($_POST['uid'] == '' || $_POST['uid'] == null) {
            echo json_encode(array("success" => false, 'msg' => "员工号不能为空"));   
            exit();
        }

        $pay = new PayModel;
        if ($pay->allowField(['sid', 'semester', 'fee', 'detail', 'method', 'iid', 'date', 'uid', 'memo'])->save($_POST, ['pid' => $_POST['pid']])) {
            echo json_encode(array("success" => true));
            exit();
        } else {
            echo json_encode(array("success" => false, 'msg' => "服务器繁忙，请稍后重试"));
            exit();
        }
    }

    //新增缴费
    public function add(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Pay/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }

        if ($_POST['sid'] == '' || !isset($_POST['sid'])) {
            echo json_encode(array("success" => false, 'msg' => "学号不能为空"));
            exit();
        }
        if ($_POST['semester'] == '' || $_POST['semester'] == null) {
            echo json_encode(array("success" => false, 'msg' => "学期不能为空"));
            exit();
        }
        if ($_POST['fee'] == '' || $_POST['fee'] == null) {
            echo json_encode(array("success" => false, 'msg' => "费用不能为空"));
            exit();
        }
        if ($_POST['method'] == '' || $_POST['method'] == null) {
            echo json_encode(array("success" => false, 'msg' => "缴费方式不能为空"));
            exit();
        }
        if ($_POST['date'] == '' || $_POST['date'] == null) {
            echo json_encode(array("success" => false, 'msg' => "日期不能为空"));
            exit();
        }
        if ($_POST['uid'] == '' || $_POST['uid'] == null) {
            echo json_encode(array("success" => false, 'msg' => "员工号不能为空"));
            exit();
        }
 
        $pay = new PayModel($_POST);
        if ($pay->allowField(['sid', 'semester', 'fee', 'detail', 'method', 'iid', 'date', 'uid', 'memo'])->save($_POST)) {
            $Log = new LogModel();
            $Log->save(["uid" => session('uid'), "action" => $pay->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $totalPage = ceil(db('pay')->where('delflag', '0')->count() / 10);
            echo json_encode(
                array("success" => true, 
                    "id" => $pay->pid,
                    "totalPage" => $totalPage
                ));
            exit();
        } else {
            echo json_encode(array("success" => false, 'msg' => "服务器繁忙，请稍后重试"));
            exit();
        }
    }

    //删除缴费
    public function deleteone(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Pay/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }
        $pay = new PayModel;
        if ($pay->save(['delflag'  => 1],['pid' => $_POST['pid']])) {
            $Log = new LogModel();
            $Log->save(["uid" => session('uid'), "action" => $pay->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("msg" => "删除失败", "success" => false));
        }
    }

    //搜索缴费
    public function search(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Pay/index", null, 1);
        //     exit();
        // }
        if (request()->isGet()) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }
        $pay = new PayModel;
        if ($pays = $pay->where('sid','like',"%$_POST[sid]%")->select()) {
            echo json_encode(array("pays" => $pays, "success" => true));
        } else {
            echo json_encode(array("msg" => "查无此人", "success" => false));
        }
    }
}
