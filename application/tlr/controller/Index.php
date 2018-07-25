<?php

namespace app\tlr\controller;

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
        if($user->delflag != 0) {
            $this->error("帐号被锁定，请联系管理员", null, null, 1);
        }
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

    public function easterEgg(Request $request)
    {
        $this->success('Designed by Leo, Diamond, cx, qdw, and xy!');
    }

    public function todos(Request $request)
    {
        $Todo = new TodoModel;
        $id = $request->param("uid");
        $temp = $Todo->where("user_id", $id)->select();
        return $temp;
        return json_encode(array("data" => $temp));
    }

    public function mod(Request $request)
    {
        $Todo = new TodoModel();
        $id = $request->param("id");
        unset($_POST["id"]);
        if ($id)
            $Todo->save($_POST, ["id" => $id]);
        else
            $Todo->save($_POST);
        return $_POST;
    }

    public function del(Request $request)
    {
        $Todo = new TodoModel();
        $id = $request->param("id");
        $Todo->where("id", $id)->delete();
        $id = $request->param("uid");
        $temp = $Todo->where("user_id", $id)->select();
        return json_encode(array("data" => $temp));
    }
}
