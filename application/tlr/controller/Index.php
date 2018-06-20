<?php

namespace app\tlr\controller;
use app\tlr\model\UserModel;
use think\Controller;
use think\Request;
use think\Session;
use gmars\rbac\Rbac;

class Index extends Controller
{
    public function index(Request $request)
    {
        $name = 'index';
        $stu = "./" . url("tlr/student");
        $this->assign(['name' => $name, 'stu' => $stu]);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    public function login(Request $request)
    {
    	return $this->fetch('login');
    }

    public function loginHandle(Request $request)
    {
        $User = new UserModel();
        $data = array(
            'uname' => $_POST['uname'],
            'passwd' => md5($_POST['passwd'])
        );
        $user = $User->where('uname', $_POST['uname'])->find();
        if($user->status != 1) {
            $this->error("帐号被锁定，请联系管理员");
        }
        if ($user['passwd'] == md5($_POST['passwd'])) {
            $rbacObj = new Rbac();
            $rbacObj->cachePermission($user['uid']);
            $rbacObj->cacheRole($user['uid']);
            Session::set('uid', $user['uid']);
            Session::set('uname', $user['uname']);
            $this->success("登录成功", "Index/index");
        } else{
            $this->error("用户名或密码错误");
        }
    }

    public function logOut(Request $request)
    {   
        if(Session::get('uid')){
            session_destroy();
            $this->success('安全退出',url('Index/login'));    
        }else{
            $this->error('操作无效');
        }
    }
}
