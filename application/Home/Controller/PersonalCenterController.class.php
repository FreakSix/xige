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

		// 登录日志页面
		public function loginLog(){
			$this->display("login_log");
		}
		
	}