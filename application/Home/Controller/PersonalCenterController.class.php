<?php
	namespace Home\Controller;
	
	
	class PersonalCenterController extends BaseController{
		
		public function index(){
			
			$this -> display();
		}


		// 修改用户信息页面
		public function updateUserInfo(){
			$this->display("update_user_info");
		}

		// 修改密码页面
		public function updateUserPwd(){
			$this->display("update_password");
		}

		// 客户等级管理页面
		public function customerRank(){
			$this->display("customer_rank");
		}

		// 新增客户等级页面
		public function addCustomerRank(){
			$this->display("add_customer_rank");
		}
		// 修改客户等级页面
		public function updateCustomerRank(){
			$this->display("update_customer_rank");
		}

		// 登录日志页面
		public function loginLog(){
			$this->display("login_log");
		}
		
		// 部门信息页面
		public function department(){
			$this->display("department");
		}
		// 添加部门页面
		public function addDepartment(){
			$this->display("add_department");
		}
		// 修改部门页面
		public function updateDepartment(){
			$this->display("update_department");
		}
		// 添加职位信息页面
		public function addPosition(){
			$this->display("add_position");
		}
		// 修改职位信息页面
		public function updatePosition(){
			$this->display("update_position");
		}
		// 员工信息页面
		public function staff(){
			$this->display("staff");
		}
		// 新增员工信息页面
		public function addStaff(){
			$this->display("add_staff");
		}
		// 修改员工信息页面
		public function updateStaff(){
			$this->display("update_staff");
		}
		// 分配登录帐号页面
		public function createAccount(){
			$this->display("create_account"); 
		}
	}