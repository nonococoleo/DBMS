<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use app\tlr\model\EnrollModel;
use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use think\Controller;
use think\Request;

class Enroll extends Controller
{
    public function index(Request $request)
    {
        $Class = new CourseModel();
        $seme = $Class->distinct("true")->column("semester");
        $semester = $request->param('semester');
        $name = $request->param('name');
        $Enroll = new EnrollModel();
        if ($name)
            $Enroll = $Enroll->where("sid", "=", $name);
//        if($semester)
//            $Enroll=$Enroll->where("semester","=",$semester);
        $enroll = $Enroll->where("delflag", "=", 0)->paginate(10, false, ['type' => 'bootstrap']);
        $page = $enroll->render();
        $data = ["seme" => $seme, "enroll" => $enroll, "page" => $page, "semester" => null];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function choose(Request $request)
    {
        $semester = $request->param('semester');
        if (!$semester) {
            $this->error("确定学期", "Enroll/index", null, 1);
            return null;
        } else {
            $Class = new CourseModel();
            $seme = $Class->distinct("true")->column("semester");
            $class = $Class->where("semester", "=", $semester)->distinct("true")->column("memo");
            $data = ["seme" => $seme, "classes" => $class, "semester" => $semester];
            $this->assign($data);
            $htmls = $this->fetch('choose');
            return $htmls;
        }
    }

    public function mod(Request $request)
    {
        if (session('uid')) {
            $Enroll = new EnrollModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Enroll->allowField(['sid', 'cid', 'attend', 'pid', 'memo'])->save($_POST, ['eid' => $request->param("eid")]);
            $Log->save(["uid" => session('uid'), "action" => $Enroll->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function del(Request $request)
    {
        if (session('uid')) {
            $Enroll = new EnrollModel();
            $Log = new LogModel();
            $Enroll->save(["delflag" => 1], ['eid' => $request->param("eid")]);
            $Log->save(["uid" => session('uid'), "action" => $Enroll->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function add(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Student/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Enroll/index", null, 1);
            exit();
        }

        if ($_POST['sid'] == '' || !isset($_POST['sid'])) {
            echo json_encode(array("success" => false, 'msg' => "学号不能为空"));
            exit();
        } else
            $sid = $_POST['sid'];
        if ($_POST['course'] == '' || $_POST['course'] == null) {
            echo json_encode(array("success" => false, 'msg' => "课程不能为空"));
            exit();
        }
        if ($_POST['pid'] == '' || $_POST['pid'] == null) {
            echo json_encode(array("success" => false, 'msg' => "缴费不能为空"));
            exit();
        } else
            $pid = $_POST['pid'];

        $enroll = new EnrollModel();
        $data = array();
        foreach ($_POST['course'] as $cid) {
            $temp = ["sid" => $sid, "cid" => $cid, "pid" => $pid];
            array_push($data, $temp);
        }
        if ($enroll->saveAll($data)) {
            $Log = new LogModel();
            $Log->save(["uid" => session('uid'), "action" => $enroll->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            echo json_encode(array("success" => true));
            exit();
        } else {
            echo json_encode(array("success" => false, 'msg' => "服务器繁忙，请稍后重试"));
            exit();
        }
    }

    public function succ(Request $request)
    {
        $pid = $request->param('pid');
        if ($pid) {
            $name = "text";
            $Pay = new PayModel();
            $Class = new CourseModel();
            $Enroll = new EnrollModel();
            $course = $Enroll->where("delflag", "=", 0)->where("pid", "=", $pid)->column("cid");
            $pay = $Pay->join("student s", "pay.sid=s.sid")->where("pid", "=", $pid)->select();
            $class = $Class->wherein("cid", $course)->select();
            $data = ["course" => $class, "name" => $name, "pay" => $pay[0]];
            $this->assign($data);
            $htmls = $this->fetch('success');
            return $htmls;
        }
    }
}
