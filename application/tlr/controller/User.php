<?php

namespace app\tlr\controller;

use app\tlr\model\LogModel;
use app\tlr\model\RoleModel;
use app\tlr\model\UserModel;
use app\tlr\model\UserRoleModel;
use gmars\rbac\Rbac;
use think\Controller;
use think\Request;

class User extends Controller
{
    // 用户管理
    public function Index(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
        $User = new UserModel;
        $users = $User->paginate(10, false, ['type' => 'bootstrap']);
        $page = $users->render();
        $Role = new RoleModel;
        $roles = $Role->select();
        $UserRole = new UserRoleModel;
        foreach ($users as $key => $user) {
            $uid = $user['uid'];
            $userRoles = $UserRole->where('user_id', $user['uid'])->select();
            $t = array();
            for($i = 0; $i < sizeof($userRoles); $i++) {
                $t[$i] = $userRoles[$i]['role_id'];
            }
            $users[$key]['userRoles'] = $t;
        }
        $data = ["users" => $users, "page" => $page, "roles" => $roles, "name" => ''];
        $this->assign($data);
        return $this->fetch('index');
    }

    //搜索员工
    public function search(Request $request)
    {
        $rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
        $name = $_GET['name'];
        $User = new UserModel;
        $users = $User->where('name', 'like', "%$name%")->paginate(10, false, ['type' => 'bootstrap']);
        $page = $users->render();
        $Role = new RoleModel;
        $roles = $Role->select();
        $UserRole = new UserRoleModel;
        foreach ($users as $key => $user) {
            $uid = $user['uid'];
            $userRoles = $UserRole->where('user_id', $user['uid'])->select();
            $t = array();
            for($i = 0; $i < sizeof($userRoles); $i++) {
                $t[$i] = $userRoles[$i]['role_id'];
            }
            $users[$key]['userRoles'] = $t;
        }
        $data = ["users" => $users, "page" => $page, "roles" => $roles, "name" => $name];
        $this->assign($data);
        return $this->fetch('index');
    }

    //添加用户
    public function registerHandle(Request $request)
    {
        $User = new UserModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        if($User->where('uname', $_POST['uname'])->find())
            $this->error('该用户名已被注册，请更换用户名重新添加');
        $data = array(
            'uname' => $_POST['uname'],
            'name' => $_POST['name'],
            'delflag' => 1,
            'passwd' => md5($_POST['passwd'])
        );
        $rbacObj = new Rbac();
        $rbacObj->createUser($data);
        $Log = new LogModel();
        $Log->save(["uid" => session('uid'), "action" => $User->getlastsql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("添加成功", null, null, 1);

    }

    //修改用户姓名
    public function changeName(Request $request)
    {
        $User = new UserModel();
        foreach ($_POST as $key => $value)
            if ($value == "")
                $_POST[$key] = null;
        $User->save(['name'  => $_POST['name']],['uid' => $_POST['uid']]);
        $Log = new LogModel();
        $Log->save(["uid" => session('uid'), "action" => $User->getlastsql(), "time" => date("Y-m-d H:i:s")]);
        echo json_encode(array('success'=>true));
    }

    //锁定和解锁用户
    public function lockOne(Request $request)
    {
        // 假删除
        $User = new UserModel();
        $user = $User->where('uid',$_GET['uid'])->find();
        $delflag = ($user['delflag'] == 1) ? 0 : 1;
        $User->save([
            'delflag'  => $delflag,
        ],['uid' => $_GET['uid']]);
        $Log = new LogModel();
        $Log->save(["uid" => session('uid'), "action" => $User->getlastsql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("修改成功", null, null, 1);
    }

    // 为用户分配角色
    public function setRole(Request $request)
    {
        $rbacObj = new Rbac();
        $Log = new LogModel();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
        $rbacObj = new Rbac();
        $UserRole = new UserRoleModel;
        $data = array();
        foreach ($_POST['roles'] as $key => $value) {
            $t = explode("_", $value);
            if(!isset($data[$t[0]])) {
                $data[$t[0]] = array();
            }
            array_push($data[$t[0]],$t[1]);
        }
        foreach ($data as $key => $value) {
            $UserRole->where('user_id', $key)->delete(); //删除旧角色
            $Log->save(["uid" => session('uid'), "action" => $UserRole->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            $rbacObj->assignUserRole($key, $value); //添加新角色
            $Log->save(["uid" => session('uid'), "action" => $UserRole->getlastsql(), "time" => date("Y-m-d H:i:s")]);
        }
        $this->success("操作成功", null, null, 1);
    }

}
