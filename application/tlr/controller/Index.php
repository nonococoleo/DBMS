<?php

namespace app\tlr\controller;

use app\tlr\model\LogModel;
use app\tlr\model\SemesterModel;
use app\tlr\model\TodoModel;
use app\tlr\model\UserModel;
use gmars\rbac\Rbac;
use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{
    public function index(Request $request)
    {
        if (session("uid")) {
            $Semester = new SemesterModel();
            $semester = $Semester->where("current", 1)->field('announce')->select();
            if ($semester[0]["announce"])
                $announce = $semester[0]["announce"];
            else
                $announce = "暂无";

            $data = ["announce" => $announce];
            $this->assign($data);
            return $this->fetch('index');
        } else
            return $this->fetch('login');
    }

    public function login(Request $request)
    {
        return $this->fetch('login');
    }

    public function loginHandle(Request $request)
    {
        $User = new UserModel();
        $Semester = new SemesterModel();
        $semester = $Semester->where("current", '=', 1)->order("id", "desc")->limit(1)->select();
        $user = $User->where('uname', $_POST['uname'])->find();
        if (!$user || $user->delflag != 0)
            $this->error("帐号或密码错误，请联系管理员", null, null, 3);

        if ($user['passwd'] == md5($_POST['passwd'])) {
            $rbacObj = new Rbac();
            $rbacObj->cachePermission($user['uid']);
            $rbacObj->cacheRole($user['uid']);
            Session::set('cur_semester', $semester[0]->id);
            Session::set('uid', $user['uid']);
            Session::set('name', $user['name']);
            $time = date("Y-m-d H:i:s", time());
            $ip = $request->ip();
            $local = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip);
            $local = json_decode($local, true)["data"];
            $temp = $local["country"] . $local["region"] . $local["city"] . " " . $local["isp"];
            $User->save(["logintime" => $time, "loginip" => $ip, "loginaddr" => $temp], ['uname' => $_POST['uname']]);
            $Log = new LogModel();
            $Log->save(["uid" => session('uid'), "action" => $User->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $this->success("登陆成功", "Index/index", null, 1);
        } else{
            $this->error("用户名或密码错误", "Index/login", null, 1);
        }
    }

    public function logOut(Request $request)
    {
        if(Session::get('uid')){
            session_destroy();
            $this->success('安全退出', 'Index/login', null, 1);
        }else{
            $this->error('操作无效', "Index/index", null, 1);
        }
    }

    public function todos(Request $request)
    {
        $Todo = new TodoModel;
        $id = $request->param("uid");
        $temp = $Todo->where("user_id", $id)->order("id")->select();
        return $temp;
    }

    public function mod(Request $request)
    {
        $Todo = new TodoModel();
        $id = $request->param("id");
        unset($_POST["id"]);
        if (strlen($id) > 0)
            $Todo->save($_POST, ["id" => $id]);
        else
            $Todo->save($_POST);
        return json_encode(array("msg" => "succ"));
    }

    public function del(Request $request)
    {
        $Todo = new TodoModel();
        $id = $request->param("id");
        if ($id) {
            $Todo->where("id", $id)->delete();
            return json_encode(array("msg" => "succ"));
        } else
            return json_encode(array("msg" => "fail"));
    }
}
