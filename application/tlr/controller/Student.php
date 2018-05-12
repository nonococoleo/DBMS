<?php

namespace app\tlr\controller;

use app\tlr\model\StudentModel;
use think\Controller;
use think\Request;

class Student extends Controller
{
    public function index(Request $request)
    {
        $student = new StudentModel;
        $id = $request->param('id');
        $query = ["id" => $id];
        $stu = $student->where('sid', '>', "$id")->paginate(1, false, ['type' => 'bootstrap', 'query' => $query]);

        $page = $stu->render();
        $this->assign(['page' => $page, 'stu' => $stu]);

        // 取回打包后的数据
        $htmls = $this->fetch();

        // 将数据返回给用户
        return $htmls;
    }
}
