<?php

namespace app\tlr\controller;

use app\tlr\model\LogModel;
use app\tlr\model\TeacherModel;
use think\Controller;
use think\Request;

class Teacher extends Controller
{
    //首页显示所有教师情况
    public function index(Request $request)
    {
        $Teacher = new TeacherModel();
        $teacher = $Teacher->where("delflag", "=", 0);
        $name = $request->param('name');
        if (!$name)
            $name = null;
        else
            $teacher = $teacher->where('tname', 'like', "%$name%");
        $teacher = $teacher->paginate(10, false, ['type' => 'bootstrap']);
        if ($teacher->count() > 0) {
            $page = $teacher->render();

            $data = ["teacher" => $teacher, "page" => $page, "name" => $name];
            $this->assign($data);

            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("查无此人", $_SERVER["HTTP_REFERER"], null, 1);
            return null;
        }
    }


    //修改教师信息接口
    public function mod(Request $request)
    {
        if (session('uid')) {
            $Teacher = new TeacherModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Teacher->allowField(['tname', 'school', 'phone', 'price', 'memo'])->save($_POST, ['tid' => $request->param("tid")]);
            $Log->save(["uid" => session('uid'), "action" => $Teacher->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    //删除教师信息接口
    public function del(Request $request)
    {
        if (session('uid')) {
            $Teacher = new TeacherModel();
            $Log = new LogModel();
            $Teacher->save(["delflag" => 1], ['tid' => $request->param("tid")]);
            $Log->save(["uid" => session('uid'), "action" => $Teacher->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }

    //添加教师信息接口
    public function add(Request $request)
    {
        if (session('uid')) {
            $Teacher = new TeacherModel();
            $Log = new LogModel();
            foreach ($_POST as $key => $value)
                if ($value == "")
                    $_POST[$key] = null;
            $Teacher->save($_POST);
            $Log->save(["uid" => session('uid'), "action" => $Teacher->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("添加成功", "Teacher/index", null, 1);
        } else {
            $this->error("没有权限", $_SERVER["HTTP_REFERER"], null, 1);
        }
        return null;
    }
}
