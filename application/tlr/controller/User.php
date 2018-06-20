<?php

namespace app\tlr\controller;
use app\tlr\model\UserModel;
use app\tlr\model\RoleModel;
use app\tlr\model\UserRoleModel;
use app\tlr\model\PermissionModel;
use app\tlr\model\RolePermissionModel;
use app\tlr\model\ControllerModel;
use think\Controller;
use think\Request;
use gmars\rbac\Rbac;

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
		$users = $User->where('status',1)->paginate(10, false, ['type' => 'bootstrap']);
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
		$data = ["users" => $users, "page" => $page, "roles" => $roles];
        $this->assign($data);
        return $this->fetch('index');
	}

	//添加用户
	public function registerHandle(Request $reques)
    {
        // $User = new UserModel();
        $data = array(
            'uname' => $_POST['uname'],
            'status' => 1,
            'passwd' => md5($_POST['passwd'])
        );
        $rbacObj = new Rbac();
        $rbacObj->createUser($data);
        $this->success("添加成功");
    }

    //删除用户
	public function deleteOne(Request $reques)
    {
    	// 假删除
        $User = new UserModel();
        $User->save([
		    'status'  => 2,
		],['uid' => $_POST['uid']]);
        $this->success("删除成功");
    }
	
	// 为用户分配角色
	public function setRole(Request $request)
	{		
		$rbacObj = new Rbac();
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
			$rbacObj->assignUserRole($key, $value); //添加新角色
		}

		$this->success("操作成功");
	}

}