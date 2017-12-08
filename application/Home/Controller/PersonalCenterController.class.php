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
		
	}