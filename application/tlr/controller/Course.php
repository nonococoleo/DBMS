<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use app\tlr\model\LogModel;
use app\tlr\model\SemesterModel;
use app\tlr\model\TeacherModel;
use think\Controller;
use think\Request;

import("Org.Util.PHPExcel.IOFactory", '', '.php');

class Course extends Controller
{
    public function index(Request $request)
    {
        $Course = new CourseModel();
        $course = $Course->alias("c")->where("c.delflag", "=", 0)->where("semester", "=", session('cur_semester'))->join("teacher t", "c.tid=t.tid")->field('cid,cname,time,date,semester,campus,room,c.price,unit,t.tid,fee,c.memo,tname')->order("campus")->paginate(10, false, ['type' => 'bootstrap']);
        $Teacher = new TeacherModel();
        $teacher = $Teacher->where("delflag", "=", "0")->select();
        $Semester = new SemesterModel();
        $semester = $Semester->select();
        $page = $course->render();

        $data = ["course" => $course, "page" => $page, "name" => null, "teacher" => $teacher, "semester" => $semester, "seme" => session('cur_semester')];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }


    public function search(Request $request)
    {
        $Course = new CourseModel();
        $name = $request->param('name');
        $semester = $request->param('seme');
        $query = ["name" => $name, "seme" => $semester];
        $course = $Course->alias("c")->where("c.delflag", "=", 0);
        if ($name)
            $course = $course->where('cname', 'like', "%$name%");
        if ($semester)
            $course = $course->where('semester', '=', $semester);
        $course = $course->join("teacher t", "c.tid=t.tid")->field('cid,cname,time,date,semester,campus,room,c.price,unit,t.tid,fee,c.memo,tname')->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        if ($course->count() > 0) {
            $Semester = new SemesterModel();
            $semester = $Semester->select();
            $Teacher = new TeacherModel();
            $teacher = $Teacher->where("delflag", "=", "0")->select();
            $page = $course->render();

            $data = array_merge(["course" => $course, "page" => $page, "semester" => $semester, "teacher" => $teacher], $query);
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
            echo json_encode(array("msg" => "无此课程", "success" => false));
        }
    }


    public function upload()
    {
        $file = request()->file('file');
//        echo $_FILES["file"]['name'].'<br>';
//        echo $_FILES["file"]['type'].'<br>';
//        echo $_FILES["file"]['size'].'<br>';
//        echo $_FILES["file"]['tmp_name'].'<br>';
        if (empty($file)) {
            $this->error('请选择上传文件');
        }
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', $_FILES["file"]['name'], false);

//        foreach ($info as $keys => $values) {
//            echo $keys;
//            echo '<br>';
//            echo $values;
//            echo '<br>';
//            echo '<br>';
//            echo '<br>';
//            echo '<br>';
//
//        }

        echo $filename = 'G:\Users\HP\PhpstormProjects\thinkphp\public\uploads\\' . $_FILES["file"]['name'];
        $this->addFromFile($filename);


//        if ($this->addFromFile($filename)) {
//            $this->success('文件上传成功');
//        } else {
//            $this->error($file->getError());
//        }
    }

    public function addFromFile($filename = '')
    {
        $file = fopen($filename, 'r');
        //读取内容
        $count = 0;
        while (!feof($file)) {


            $line = fgetcsv($file);
            $cname = 2;

            $data['cid'] = null;
            $data['delflag'] = 0;
            $data['cname'] = $line[$cname++];
            $data['time'] = $line[$cname++];
            $data['date'] = $line[$cname++];
            $data['semester'] = (int)$line[$cname++];
            $data['campus'] = (int)$line[$cname++];
            $data['room'] = $line[$cname++];
            $data['price'] = (float)$line[$cname++];
            $data['unit'] = (int)$line[$cname++];
            $data['tid'] = (int)$line[$cname++];
            $data['fee'] = (float)$line[$cname++];
            $data['memo'] = $line[$cname];

            $count++;
            if ($count == 1) {
                continue;//去掉首行记录
            }
            $courses[$count - 2] = $data;
        }
        fclose($file);
        unset($courses[$count - 2]);//去掉尾行记录

        foreach ($courses as $key => $values) {
            echo $key;
            echo '<br>';
            print_r($values);
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo '<br>';
        }

//批量添加
        $Course = new CourseModel();
        $Log = new LogModel();
        $Course->saveAll($courses);
        $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("添加成功", "Course/index", null, 1);

    }
}
