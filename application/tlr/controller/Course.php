<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use app\tlr\model\LogModel;
use app\tlr\model\SemesterModel;
use app\tlr\model\TeacherModel;
use gmars\rbac\Rbac;
use think\Controller;
use think\Request;

class Course extends Controller
{
    //首页显示全部报名信息
    public function index(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "index/index", null, 3);
            exit();
        }
        $name = $request->param('name');
        $semester = $request->param('seme');

        $Course = new CourseModel();
        $course = $Course->alias("c")->where("c.delflag", "=", 0);
        if ($name)
            $course = $course->where('cname', 'like', "%$name%");
        else
            $name = null;
        if (!$semester)
            $semester = session("cur_semester");
        $query = ["name" => $name, "seme" => $semester];
        $course = $course->where('semester', '=', $semester)->join("teacher t", "c.tid=t.tid")->join("semester s", "c.semester=s.id")->field('cid,cname,time,date,semester,campus,room,c.price,unit,t.tid,fee,c.memo,tname,s.name')->order("campus")->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        if ($course->count() > 0) {
            $Semester = new SemesterModel();
            $semester = $Semester->where("id", ">", 0)->select();
            $Teacher = new TeacherModel();
            $teacher = $Teacher->where("delflag", "=", "0")->select();
            $page = $course->render();

            $data = array_merge(["course" => $course, "page" => $page, "semester" => $semester, "teacher" => $teacher], $query);
            $this->assign($data);
            $htmls = $this->fetch("index");
            return $htmls;
        } else {
            $this->error("无此课程", null, null, 1);
            return null;
        }
    }

    //修改课程信息
    public function mod(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Course/index", null, 1);
            exit();
        }
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
        return null;
    }


    //删除课程
    public function del(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Course/index", null, 1);
            exit();
        }
        $Course = new CourseModel();
        $Log = new LogModel();
        $Course->save(['delflag' => 1], ['cid' => $request->param("cid")]);
        $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("删除成功", null, null, 1);
        return null;
    }

//    新增课程
    public function add(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Course/index", null, 1);
            exit();
        }
        $Course = new CourseModel();
        $Log = new LogModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        $Course->save($_POST);
        $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("添加成功", null, null, 1);
        return null;
    }

//    查找课程接口
    public function ser(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "Course/index", null, 1);
            exit();
        }
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

//    上传csv文件批量导入课程
    public function upload(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限", "System/semester", null, 1);
            exit();
        }
        $upload = request()->file('file');
        if (empty($upload)) {
            $this->error('请选择上传文件');
        }
        $replacename = session('uid') . '_' . time() . '_' . $_FILES["file"]['name'];
        $file = $upload->move(ROOT_PATH . 'public' . DS . 'uploads', $replacename, false);

        $filename = $file->getPath() . "/" . $replacename;
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

//批量添加
        $Course = new CourseModel();
        $Log = new LogModel();
        $Course->saveAll($courses);
        $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("添加成功", null, null, 1);
    }

//    //    导出csv
//    public function csv()
//    {
//        $Course = new CourseModel();
//        $data = $Course->field('cid,delflag,cname,time,date,semester,campus,room,price,unit,tid,fee,memo')->select();
//
//        $str = 'cid' . ',' . 'delflag' . ',' . 'cname' . ',' . 'time' . ',' . 'date' . ',' . 'semester' . ',' . 'campus' . ',' . 'room' . ',' . 'price' . ',' . 'unit' . ',' . 'tid' . ',' . 'fee' . ',' . 'memo' . "\n";
//        foreach ($data as $key => $value) {
//            $str .= $value['cid'] . ',' . $value['delflag'] . ',' . $value['cname'] . ',' . $value['time'] . ',' . $value['date'] . ',' . $value['semester'] . ',' . $value['campus'] . ',' . $value['room'] . ',' . $value['price'] . ',' . $value['unit'] . ',' . $value['tid'] . ',' . $value['fee'] . ',' . $value['memo'] . "\n";
//        };
//        $filename = './output.csv';
//        header('Content-type:text/csv');
//        header("Content-Disposition:attachment;filename=" . $filename);
//        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
//        header('Expires:0');
//        header('Pragma:public');
//        echo $str;
//    }
}
