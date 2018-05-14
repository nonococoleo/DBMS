<?php

namespace app\tlr\controller;

use app\tlr\model\StudentModel;
use think\Controller;
use think\Request;

class Student extends Controller
{

    public function index(Request $request) {
        return $this->fetch('index');
    }

    public function students(Request $request) {
        if (!request()->isGet()) {
            $this->error("404 not found");
            exit();
        }
        $page = (isset($_GET['page']))? $_GET['page'] : 1;
        $student = new StudentModel;
        $students = $student->page($page,10)->select();
        $totolPage = ceil(db('student')->count()/10);
        echo json_encode(array("students" => $students,"totolPage"=>$totolPage, "success" => true));
    }

    public function update(Request $request) {
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found");
            exit();   
        }

        if ($_POST['sname']=='' || !isset($_POST['sname'])){
            echo json_encode(array("success"=>false,'msg'=>"学生姓名不能为空"));
            exit();   
        }
        if ($_POST['phone']=='' || $_POST['phone']==null){
            echo json_encode(array("success"=>false,'msg'=>"手机号码不能为空"));
            exit();   
        }

        $student = new StudentModel;
        if($student->allowField(['sname','sex','grade','school','home','tel','phone','memo'])->save($_POST, ['sid' => $_POST['sid']])) {
            echo json_encode(array("success"=>true));
            exit();
        } else {
            echo json_encode(array("success"=>false,'msg'=>"服务器繁忙，请稍后重试"));
            exit();
        }
    }

    public function add(Request $request) {
        if (!request()->isPost() || empty($_POST)) {
            $this->error("404 not found");
            exit();   
        }

        if ($_POST['sname']=='' || !isset($_POST['sname'])){
            echo json_encode(array("success"=>false,'msg'=>"学生姓名不能为空"));
            exit();   
        }
        if ($_POST['phone']=='' || !isset($_POST['phone'])){
            echo json_encode(array("success"=>false,'msg'=>"手机号码不能为空"));
            exit();   
        }

        $student = new StudentModel($_POST);
        if($student->allowField(['sname','sex','grade','school','home','tel','phone','memo'])->save($_POST)) {
            echo json_encode(array("success"=>true));
            exit();
        } else {
            echo json_encode(array("success"=>false,'msg'=>"服务器繁忙，请稍后重试"));
            exit();
        }
    }
}
