<?php

namespace app\tlr\controller;

use app\tlr\model\StudentModel;
use think\Controller;
use think\Request;

class Student extends Controller
{

    public function index(Request $request)
    {
//        TODO 和students控制器的区别？合并？
        return $this->fetch('index');
    }

    public function students(Request $request)
    {
        if (!request()->isGet()) {
            $this->error("404 not found");
            exit();
        }
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $student = new StudentModel;
        $students = $student->page($page, 10)->select();
        $totalPage = ceil(db('student')->count() / 10);
        echo json_encode(array("students" => $students, "totalPage" => $totalPage, "success" => true));
    }

    public function update(Request $request)
    {
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found");
            exit();
        }

        if ($_POST['sname'] == '' || !isset($_POST['sname'])) {
            echo json_encode(array("success" => false, 'msg' => "学生姓名不能为空"));
            exit();
        }
        if ($_POST['phone'] == '' || $_POST['phone'] == null) {
            echo json_encode(array("success" => false, 'msg' => "手机号码不能为空"));
            exit();
        }

        $student = new StudentModel;
        if ($student->allowField(['sname', 'sex', 'grade', 'school', 'home', 'tel', 'phone', 'memo'])->save($_POST, ['sid' => $_POST['sid']])) {
            echo json_encode(array("success" => true));
            exit();
        } else {
            echo json_encode(array("success" => false, 'msg' => "服务器繁忙，请稍后重试"));
            exit();
        }
    }

    public function add(Request $request)
    {
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found");
            exit();
        }

        if ($_POST['sname'] == '' || !isset($_POST['sname'])) {
            echo json_encode(array("success" => false, 'msg' => "学生姓名不能为空"));
            exit();
        }
        if ($_POST['phone'] == '' || !isset($_POST['phone'])) {
            echo json_encode(array("success" => false, 'msg' => "手机号码不能为空"));
            exit();
        }

        $student = new StudentModel($_POST);
        if ($student->allowField(['sname', 'sex', 'grade', 'school', 'home', 'tel', 'phone', 'memo'])->save($_POST)) {
            echo json_encode(array("success" => true));
            exit();
        } else {
            echo json_encode(array("success" => false, 'msg' => "服务器繁忙，请稍后重试"));
            exit();
        }
    }

    public function search(Request $request)
    {
        if (!request()->isGet()) {
            $this->error("404 not found");
            exit();
        }
        $student = new StudentModel;
        if ($students = $student->where('sname', $_GET['sname'])->select()) {
            echo json_encode(array("students" => $students, "success" => true));
        } else {
            echo json_encode(array("msg" => "查无此人", "success" => false));
        }
    }
}
