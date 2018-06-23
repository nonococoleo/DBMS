<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use app\tlr\model\LogModel;
use think\Controller;
use think\Request;

//use app\tlr\model\TeacherModel;

class Course extends Controller
{
    public function index(Request $request)
    {
        $Course = new CourseModel();
        $course = $Course->where("delflag", "=", 0)->paginate(10, false, ['type' => 'bootstrap']);
        $page = $course->render();

        $data = ["course" => $course, "page" => $page, "name" => null];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }


    public function search(Request $request)
    {
        $Course = new CourseModel();
        $name = $request->param('name');
        $query = ["name" => $name];
        $course = $Course->where('cname', 'like', "%$name%")->order('cid', 'asc')->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        if ($course->count() > 0) {
            $page = $course->render();

            $data = array_merge(["course" => $course, "page" => $page], $query);
            $this->assign($data);

            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("无此课程", $_SERVER["HTTP_REFERER"], null, 1);
            return null;
        }
    }

    public function mod(Request $request)
    {
        if (session('uid')) {
            $Course = new CourseModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value) {
                if ($value == "") {
                    $_POST[$key] = null;
                }
                $Course->allowField(['cname', 'time', 'date', 'semester', 'campus', 'room', 'price', 'unit', 'tid', 'fee', 'memo'])->save($_POST, ['cid' => $request->param("cid")]);
                $Log->save(["uid" => session('uid'), "action" => $Course->getlastsql(), "time" => date("Y-m-d H:i:s")]);
                $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
            }
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function del(Request $request)
    {
        if (session('uid')) {
            $Course = new CourseModel();
            $Log = new LogModel();
            $Course->allowField(['delflag'])->save(['delflag' => 1], ['cid' => $request->param("cid")]);
            $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function add(Request $request)
    {
        if (session('uid')) {
            $Course = new CourseModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Course->save($_POST);
            $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("添加成功", "Course/index", null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    public function ser(Request $request)
    {
        $Course = new CourseModel();
        if (request()->isGet()) {
            $this->error("404 not found", "Student/index", null, 1);
            exit();
        }
        if ($course = $Course->where('semester', '=', "$_POST[seme]")->where('memo', '=', "$_POST[memo]")->select()) {
            echo json_encode(array("course" => $course, "success" => true));
        } else {
            echo json_encode(array("msg" => "查无此人", "success" => false));
        }
    }
}
