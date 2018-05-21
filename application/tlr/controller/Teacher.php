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
        // 向V层传数据
        $data = ["teacher" => $teacher, "page" => $page];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function search(Request $request)
    {
        $Teacher = new TeacherModel();
        $name = $request->param('name');
        $school = $request->param('school');
        $query = $request->param();
        $teacher = $Teacher->where('tname', 'like', "%$name%")->where('school', 'like', "%$school%")->order('tid', 'asc')->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        $page = $teacher->render();
        // 向V层传数据
        $data = ["teacher" => $teacher, "page" => $page];
        $this->assign($data);

        // 取回打包后的数据
        $htmls = $this->fetch("index");
        return $htmls;
    }
}
