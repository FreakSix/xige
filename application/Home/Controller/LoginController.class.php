<?php
	namespace Home\Controller;
	
	
	class LoginController extends BaseController{
		
		public function index(){
			
			$this -> display();
		}
		
		
		/**
		 * 获取登陆的信息
		 */
		public function checkedUser(){
			echo 1;
		}
	}