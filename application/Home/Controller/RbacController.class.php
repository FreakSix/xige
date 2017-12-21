<?php
namespace Home\Controller;

// 权限管理控制器
class RbacController extends BaseController {
	// 添加职位信息页面
	public function addPosition(){
		$this->display("PersonalCenter/add_position");
	}
	// 修改职位信息页面
	public function updatePosition(){
		$this->display("PersonalCenter/update_position");
	}
	// 员工信息页面
	public function staff(){
		$this->display("PersonalCenter/staff");
	}
	// 新增员工信息页面
	public function addStaff(){
		$this->display("PersonalCenter/add_staff");
	}
	// 修改员工信息页面
	public function updateStaff(){
		$this->display("PersonalCenter/update_staff");
	}
	// 权限管理首页
	public function index(){
		$this->display("PersonalCenter/role");
	}
	// 分配权限页面
	public function access(){
		$this->display("PersonalCenter/set_access");
	}
	// 节点页面（指的控制器、方法）
	public function node(){
		$this->display("PersonalCenter/node");
	}
	// 添加节点
	public function addNode(){
		$this->display("PersonalCenter/add_node");
	}
	// 修改节点
	public function updateNode(){
		$this->display("PersonalCenter/update_node");
	}
}


?>