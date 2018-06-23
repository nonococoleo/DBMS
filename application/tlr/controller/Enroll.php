<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use think\Controller;
use think\Request;

class Enroll extends Controller
{
    public function index(Request $request)
    {
        $Class = new CourseModel();
        $seme = $Class->distinct("true")->column("semester");

        $data = ["seme" => $seme];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function choose(Request $request)
    {
        $Class = new CourseModel();
        $semester = $request->param('semester');
        $seme = $Class->distinct("true")->column("semester");
        if (!$semester)
            $semester = $seme[0];
        $class = $Class->where("semester", "=", $semester)->distinct("true")->column("memo");
        $data = ["seme" => $seme, "classes" => $class, "semester" => $semester];
        $this->assign($data);
        $htmls = $this->fetch('choose');
        return $htmls;
    }
}
