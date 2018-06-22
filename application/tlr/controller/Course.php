<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2018/6/22
 * Time: 12:52
 */


namespace app\tlr\controller;

use app\tlr\model\CourseModel;
//use app\tlr\model\TeacherModel;
use think\Controller;
use think\Request;

//课程Course
class Course extends Controller
{


    public function index(Request $request)
    {
        $Course = new CourseModel();
        $course = $Course->paginate(10, false, ['type' => 'bootstrap']);
        $page = $course->render();

        $data = ["course" => $course, "page" => $page, "name" => null];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }


    public function search(Request $request)
    {
        $Course = new CourseModel();
        $name = $request->param('name');
        $query = ["name" => $name];
        $course = $Course->where('cname', 'like', "%$name%")->order('cid', 'asc')->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        if ($course->count() > 0) {
            $page = $course->render();

            $data = array_merge(["course" => $course, "page" => $page], $query);
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
        $Course = new CourseModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        $Course->allowField(['cname', 'time', 'date', 'semester', 'campus', 'room', 'price', 'unit', 'tid', 'fee', 'memo'])->save($_POST, ['cid' => $request->param("cid")]);
        $this->success("修改成功", $_SERVER["HTTP_REFERER"], null, 1);
        return null;
    }

    public function del(Request $request)
    {
        $Course = new CourseModel();
//TODO 修改标志位
        $Course->destroy(['cid' => $request->param("cid")]);
//        TODO delflag
        $this->success("删除成功", $_SERVER["HTTP_REFERER"], null, 1);
//        TODO 日志
        return null;
    }

    public function add(Request $request)
    {
        $Course = new CourseModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        $Course->save($_POST);
        $this->success("添加成功", "Course/index", null, 1);
        return null;
    }
}
