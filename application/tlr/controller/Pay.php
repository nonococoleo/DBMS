<?php

namespace app\tlr\controller;

use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use app\tlr\model\SemesterModel;
use gmars\rbac\Rbac;
use think\Controller;
use think\Request;

class Pay extends Controller
{
    // 缴费页面
    public function index(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "index/index", null, 3);
            exit();
        }
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->where("current", ">=", 0)->select();
        $data = ["semester" => $semester];
        $this->assign($data);
        return $this->fetch('index');
    }
    // 获取缴费列表
    public function pays(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Pay/index", null, 1);
            exit();
        }
        if (request()->isGet()) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }
        $page = (isset($_POST['page'])) ? $_POST['page'] : 1;
        $semester = $_POST['sem'];
        $pay = new PayModel;
        if($semester == 0) {
            $pays = $pay->alias("p")->join("student s", "s.sid=p.sid")->where('p.delflag', '0')->field("pid,p.sid,semester,p.fee,detail,method,iid,rid,date,uid,p.memo,sname")->page($page, 10)->select();
            $totalPage = ceil(db('pay')->where('delflag', '0')->count() / 10);
        }
        else{
            $pays = $pay->alias("p")->join("student s", "s.sid=p.sid")->where(array('p.delflag' => '0', 'semester' => $semester))->field("pid,p.sid,semester,p.fee,detail,method,iid,rid,date,uid,p.memo,sname")->page($page, 10)->select();
            $totalPage = ceil(db('pay')->where(array('delflag'=>'0','semester'=>$semester))->count() / 10);
        }
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->where("current", ">=", 0)->select();
        echo json_encode(array("pays" => $pays, 
            "semester" => $semester,
            "totalPage" => $totalPage, 
            "success" => true));
    }

    // 根据Id获取缴费信息
    public function payById(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Pay/index", null, 1);
            exit();
        }
        $Pay = new PayModel;
        $pay = $Pay->alias("p")->join("user u", "u.uid=p.uid")->where('pid', $_POST['pid'])->field("pid,fee,detail,method,date,u.name user,p.memo")->find();
        echo json_encode(array("pay" => $pay, "success" => true));
    }

    // 修改缴费信息
    public function update(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Pay/index", null, 1);
            exit();
        }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Pay/index", null, 1);
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
        if ($pay->allowField(['fee', 'detail', 'method', 'date', 'uid', 'memo'])->save($_POST, ['pid' => $_POST['pid']])) {
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
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Pay/index", null, 1);
            exit();
        }
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

        $pay = new PayModel();
        if ($pay->allowField(['sid', 'semester', 'fee', 'detail', 'method', 'invoice', 'date', 'uid', 'memo'])->save($_POST)) {
            $Log = new LogModel();
            $Log->save(["uid" => session('uid'), "action" => $pay->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $totalPage = ceil(db('pay')->where('delflag', '0')->count() / 10);
            echo json_encode(
                array("success" => true,
                    "pid" => $pay->pid,
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
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Pay/index", null, 1);
            exit();
        }
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
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Pay/index", null, 1);
            exit();
        }
        if (request()->isGet()) {
            $this->error("404 not found", "Pay/index", null, 1);
            exit();
        }
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->where("current", ">=", 0)->select();
        $pay = new PayModel;
        if ($pays = $pay->alias("p")->join("student s", "s.sid=p.sid")->where('p.delflag', '0')->where('p.sid','=',"$_POST[sid]")->select()) {
            echo json_encode(array("pays" => $pays, "success" => true, "semester" => $semester));
        } else {
            echo json_encode(array("msg" => "没有查询到相关记录", "success" => false));
        }
    }
}
