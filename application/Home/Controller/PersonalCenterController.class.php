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
		// 公告信息页面
		public function notice(){
			$noticeInfo = D("XgNotice")->getNotice();
	    	$this->assign("noticeInfo",$noticeInfo);
			$this->display("notice");
		}
		// 修改公告信息页面
		public function updateNotice(){
	    	$noticeInfo = D("XgNotice")->getNotice();
	    	$this->assign("noticeInfo",$noticeInfo);
			$this->display("update_notice");
		}
		// 修改公共信息处理
		public function updateNoticeHandle(){
			$noticeInfo = D("XgNotice")->getNotice();
			$post = $_POST;
			if($post){
				if($post["notice_info"] == $noticeInfo[0]["notice_info"] && $post["note_info"] == $noticeInfo[0]["note_info"]){
					return 1;
				}
			}
		}
		// 公共备忘录管理页面
		public function publicMemo(){
			$this->display("public_memo");
		}
		// 修改公共备忘录信息页面
		public function updatePublicMemo(){
			$this->display("update_public_memo");
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
		
		// 分配登录帐号页面
		public function createAccount(){
			$this->display("create_account"); 
		}
	}