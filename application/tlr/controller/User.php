<?php

namespace app\tlr\controller;
use app\tlr\model\UserModel;
use app\tlr\model\RoleModel;
use app\tlr\model\UserRoleModel;
use app\tlr\model\PermissionModel;
use app\tlr\model\RolePermissionModel;
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
		$users = $User->select();
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
		$data = ["users" => $users, "roles" => $roles];
        $this->assign($data);
        return $this->fetch('user');
	}

	// 角色管理
	public function role(Request $request)
	{
		$rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
		$Role = new RoleModel;
		$roles = $Role->select();
		$data = ["roles" => $roles];
        $this->assign($data);
        return $this->fetch('role');
	}

	//添加角色
	public function addRole(Request $request)
	{
		$rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
		$rbacObj = new Rbac();
		$rbacObj->createRole($_POST);
		$this->success("操作成功");
	}

	//权限管理
	public function permission(Request $request)
	{
		$rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
		$Permission = new PermissionModel;
		$permissions = $Permission->select();
		$data = ["permissions" => $permissions];
        $this->assign($data);
        return $this->fetch('permission');
	}

	//添加权限
	public function addPermission(Request $request)
	{
		$rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
		$rbacObj = new Rbac();
		$_POST['create_time'] = time();
		$rbacObj->createPermission($_POST);
		$this->success("操作成功");
	}

	// 角色权限管理
	public function setPermission(Request $request)
	{
		$rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
		$Permission = new PermissionModel;
		// 所有权限
		$permissions = $Permission->select();
		$RolePermission = new RolePermissionModel;
		// 该角色所拥有的权限
		$rolePermissions = $RolePermission->where('role_id', $_GET['id'])->select();
		for($i = 0; $i < sizeof($permissions); $i++) {
			$permissions[$i]['have'] = 0;
			for($j = 0; $j < sizeof($rolePermissions); $j++) {
				if($permissions[$i]['id'] == $rolePermissions[$j]['permission_id']) {
					$permissions[$i]['have'] = 1;
					break;
				}
			}
		}
		$data = ["permissions" => $permissions, "role" => $_GET];
        $this->assign($data);
        return $this->fetch('setPermission');
	}

	// 为角色分配权限
	public function setPermissionHandle(Request $request)
	{
		$RolePermission = new RolePermissionModel;
		$rolePermissions = $RolePermission->where('role_id', $_POST['role_id'])->delete();
		$rbacObj = new Rbac();
		$rbacObj->assignRolePermission($_POST['role_id'], $_POST['permissions']);
		$this->success("操作成功");
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