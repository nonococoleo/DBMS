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
        if (!isset($_GET)) {
            $this->error("404 not found");
            exit();
        }
        $page = (isset($_GET['page']))? $_GET['page'] : 1;
        $studentModel = new StudentModel;
        $students = $studentModel->page($page,10)->select();
        $totolPage = ceil(db('student')->count()/10);
        echo json_encode(array("students" => $students,"totolPage"=>$totolPage, "success" => true));
    }

    public function new_student(Request $request) {
        echo "111";
    }
}
