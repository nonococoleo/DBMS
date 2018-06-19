<?php

namespace app\tlr\controller;

use app\tlr\model\StudentModel;
use think\Controller;
use think\Request;

class Student extends Controller
{
    // 学生页面
    public function index(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限");
        //     exit();
        // }
        return $this->fetch('index');
    }

    // 获取学生列表
    public function students(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Student/index", null, 1);
        //     exit();
        // }
        if (request()->isGet()) {
            $this->error("404 not found", "Student/index", null, 1);
            exit();
        }
        $page = (isset($_POST['page'])) ? $_POST['page'] : 1;
        $student = new StudentModel;
        $students = $student->page($page, 10)->select();
        $totalPage = ceil(db('student')->count() / 10);
        echo json_encode(array("students" => $students, "totalPage" => $totalPage, "success" => true));
    }

    // 修改学生信息
    public function update(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Student/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Student/index", null, 1);
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

    //新增学生
    public function add(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Student/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Student/index", null, 1);
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
            echo json_encode(array("success" => true, "id" => $student->sid));
            exit();
        } else {
            echo json_encode(array("success" => false, 'msg' => "服务器繁忙，请稍后重试"));
            exit();
        }
    }

    //删除学生
    public function deleteone(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Student/index", null, 1);
        //     exit();
        // }
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found", "Student/index", null, 1);
            exit();
        }
        $student = new StudentModel;
        if ($students = $student->where('sid', $_POST['sid'])->delete()) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("msg" => "删除失败", "success" => false));
        }
    }

    //搜索学生
    public function search(Request $request)
    {
        // $rbacObj = new Rbac();
        // if(!$rbacObj->can($request->path())) {
        //     $this->error("没有权限", "Student/index", null, 1);
        //     exit();
        // }
        if (request()->isGet()) {
            $this->error("404 not found", "Student/index", null, 1);
            exit();
        }
        $student = new StudentModel;
        if ($students = $student->where('sname','like',"%$_POST[sname]%")->select()) {
            echo json_encode(array("students" => $students, "success" => true));
        } else {
            echo json_encode(array("msg" => "查无此人", "success" => false));
        }
    }
}
