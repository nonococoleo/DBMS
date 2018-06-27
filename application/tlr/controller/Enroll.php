<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use app\tlr\model\EnrollModel;
use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use app\tlr\model\SemesterModel;
use think\Controller;
use think\Request;

class Enroll extends Controller
{
    public function index(Request $request)
    {
        $seme = $request->param('seme');
        $sid = $request->param('sid');
        if (!$seme)
            $seme = session('cur_semester');

        $Semester = new SemesterModel();
        $semester = $Semester->select();
        $Course = new CourseModel();
        $course = $Course->where("semester", "=", $seme)->select();

        $Enroll = new EnrollModel();
        $enroll = $Enroll->alias("e")->where("e.delflag", "=", 0)->join("course c", "c.cid=e.cid")->join("student s", "s.sid=e.sid")->where("c.semester", "=", $seme);
        if ($sid)
            $enroll = $enroll->where("e.sid", "=", $sid);

        $enroll = $enroll->paginate(10, false, ['type' => 'bootstrap']);
        $page = $enroll->render();
        $data = ["semester" => $semester, "enroll" => $enroll, "page" => $page, "seme" => $seme, "course" => $course, "sid" => $sid];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function choose(Request $request)
    {
        $seme = $request->param('seme');
        if (!$seme) {
            $this->error("确定学期", "Enroll/index", null, 1);
            return null;
        } else {
            $Class = new CourseModel();
            $Semester = new SemesterModel();
            $semester = $Semester->where("id", "=", $seme)->select();
//            $semester = $Semester->select();
            $class = $Class->where("semester", "=", $seme)->distinct("true")->column("memo");
            $data = ["seme" => $semester[0]->name, "classes" => $class, "semester" => $seme];
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
        } else {
            $this->error("没有信息", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function course(Request $request)
    {
        $cid = $request->param('cid');
        if ($cid) {
            $Enroll = new EnrollModel();
            $enroll = $Enroll->alias("e")->where("e.delflag", "=", 0)->where("e.cid", "=", $cid)->join("course c", "c.cid=e.cid")->join("student s", "s.sid=e.sid")->select();
            $Course = new CourseModel();
            $course = $Course->alias("c")->join("semester s", "s.id=c.semester")->join("teacher t", "t.tid=c.tid")->where("cid", "=", $cid)->select();
            $data = ["enroll" => $enroll, "course" => $course[0]];
            $this->assign($data);
            $htmls = $this->fetch('course');
            return $htmls;
        } else {
            $this->error("没有信息", url("enroll/index"), null, 1);
        }
        return null;
    }

    public function attend(Request $request)
    {
        $eid = input('post.eid/a');
        if ($eid) {
            $Enroll = new EnrollModel();
            $Log = new LogModel();
            foreach ($eid as $key => $value) {
                $Enroll->save(["attend" => $value], ["eid" => $key]);
                $Log->save(["uid" => session('uid'), "action" => $Enroll->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            }
            $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }


}
