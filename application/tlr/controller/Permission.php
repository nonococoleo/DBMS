<?php

namespace app\tlr\controller;
use app\tlr\model\UserModel;
use app\tlr\model\RoleModel;
use app\tlr\model\UserRoleModel;
use app\tlr\model\PermissionModel;
use app\tlr\model\RolePermissionModel;
use app\tlr\model\ControllerModel;
use app\tlr\model\LogModel;
use think\Controller;
use think\Request;
use gmars\rbac\Rbac;

class Permission extends Controller
{
	//权限列表
	public function index(Request $request)
	{
		$rbacObj = new Rbac();
        if(!$rbacObj->can($request->path())) {
            $this->error("没有权限");
            exit();
        }
		$Permission = new PermissionModel;
		$permissions = $Permission->select();
		$Controller = new ControllerModel;
		$controllers = $Controller->select();
		foreach ($controllers as $key => $value) {
			$tmp =array();
			foreach ($permissions as $k => $v) {
				if($v['cid'] == $value['cid'])
					array_push($tmp, $v);
			}
			$controllers[$key]['permissions'] = $tmp;
		}
		$data = ["controllers" => $controllers];
        $this->assign($data);
        return $this->fetch('index');
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
		$Permission = new PermissionModel();
        $Log = new LogModel();
        $Log->save(["uid" => session('uid'), "action" => $Permission->getlastsql(), "time" => date("Y-m-d H:i:s")]);
		$this->success("操作成功");
	}

	// 添加控制器（权限控制的一个粒度）
	public function addController(Request $request)
	{
		$Controller = new ControllerModel();
		if($Controller->where('path', $_POST['path'])->find() == null){
	        $Controller->save($_POST);
	        $Log = new LogModel();
	        $Log->save(["uid" => session('uid'), "action" => $Controller->getlastsql(), "time" => date("Y-m-d H:i:s")]);
	        $this->success("添加成功");
		}
	    else{
	    	$this->error("控制器已存在，请勿重复添加");
	    }
	}
}