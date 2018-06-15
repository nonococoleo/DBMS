<?php

namespace app\tlr\controller;

use app\tlr\model\TeacherModel;
use think\Controller;
use think\Request;

class Teacher extends Controller
{
    public function index(Request $request)
    {
        $Teacher = new TeacherModel();
        $teacher = $Teacher->paginate(10, false, ['type' => 'bootstrap']);
        $page = $teacher->render();

        $data = ["teacher" => $teacher, "page" => $page, "name" => null];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function search(Request $request)
    {
        $Teacher = new TeacherModel();
        $name = $request->param('name');
        $query = ["name" => $name];
        $teacher = $Teacher->where('tname', 'like', "%$name%")->order('tid', 'asc')->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        if ($teacher->count() > 0) {
            $page = $teacher->render();

            $data = array_merge(["teacher" => $teacher, "page" => $page], $query);
            $this->assign($data);

            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("查无此人", $_SERVER["HTTP_REFERER"], null, 1);
            return null;
        }
    }

    public function mod(Request $request)
    {
        $Teacher = new TeacherModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        $Teacher->allowField(['tname', 'school', 'phone', 'price', 'memo'])->save($_POST, ['tid' => $request->param("tid")]);
        $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        return null;
    }

    public function del(Request $request)
    {
        $Teacher = new TeacherModel();
        $Teacher->destroy(['tid' => $request->param("tid")]);
//        TODO delflag
        $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
//        TODO 日志
        return null;
    }

    public function add(Request $request)
    {
        $Teacher = new TeacherModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        $Teacher->save($_POST);
        $this->success("添加成功", "Teacher/index", null, 1);
        return null;
    }
}
