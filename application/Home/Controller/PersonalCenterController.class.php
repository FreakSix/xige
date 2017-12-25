<?php
	namespace Home\Controller;
	
	
	class PersonalCenterController extends BaseController{
		
		public function index(){
			$userInfo = $_SESSION['userInfo'];
			//获取部门
			if($userInfo['department_id'] != 0){
				$depeartModel = D("XgDepeartment");
				$depeart = $depeartModel->getDepeartmentById($userInfo['department_id']);
			}else{
				$depeart = "";
			}
			//获取职位
			$dutyModel = D("XgDuty");
			$duty = $dutyModel->getDutyById($userInfo['duty_id']);
			
			$this->assign("depeart",$depeart);
			$this->assign("duty",$duty);
			$this->assign("userInfo",$userInfo);
			$this -> display();
		}


		// 修改用户信息页面
		public function updateUserInfo(){
			$username = $_GET['username'];
			$email = $_GET['email'];
			$tel = $_GET['tel'];
			$id = $_GET['id'];
			$this->assign("username",$username);
			$this->assign("email",$email);
			$this->assign("tel",$tel);
			$this->assign("id",$id);
			$this->display("update_user_info");
		}

		// 修改密码页面
		public function updateUserPwd(){
			$userInfo = $_SESSION['userInfo'];
			$this->assign("userInfo",$userInfo);
			$this->display("update_password");
		}
		//保存新修改的密码
		public function saveUserPwdNew(){
			$userInfo = $_SESSION['userInfo'];
			$id = $_POST['id'];
			$old_pwd = $_POST['old_pwd'];
			$new_pwd = $_POST['new_pwd'];
			if($old_pwd == $userInfo['password']){
				$managerModel = D("XgManager");
				$res = $managerModel->updatePassword($id,$new_pwd);
				if($res){
					unset ($_SESSION['userInfo']);
					echo 0;
				}else{
					echo 1;
				}
			}else{
				echo 2;
			}
			
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
			$userInfo = $_SESSION['userInfo'];
			$select_user_id = isset($_POST['user_id'])?$_POST['user_id']:"";
			//根据职位给权限，要是老板的话可以查看所有的人的登录日志，要是其他人只能查看自己的(老板的duty_id为9)
			$loginLogsModel = D("XgLoginLogs");
			if($userInfo['department_id'] == 0){
				//获取其他员工
				$managerModel = D("XgManager");
				$mangerArr = $managerModel->getAllManager();
				if(!empty($mangerArr)){
					foreach($mangerArr as $k=>$v){
						if($v['id'] == $userInfo['id']){
							unset($mangerArr[$k]);
						}
					}
				}
				if($select_user_id == ''){
					$user_id = $userInfo['id'];
					$loginLogArr = $loginLogsModel->getLoginLogsByUserId($user_id);
				}else{
					$loginLogArr = $loginLogsModel->getLoginLogsByUserId($select_user_id);
				}
				$this->assign("mangerArr",$mangerArr);
			}else{
				$user_id = $userInfo['id'];
				$loginLogArr = $loginLogsModel->getLoginLogsByUserId($user_id);
			}
			
			//分页
// 			$totalCount
			
			$this->assign("userInfo",$userInfo);
			$this->assign("loginLogArr",$loginLogArr);
			$this->display("login_log");
		}
		//点击不同的人员获取不同的登陆日志
		public function getDifLoginlogs(){
			$id = $_POST['id'];
			$loginLogsModel = D("XgLoginLogs");
			if($id == "all"){
				$loginLogArr = $loginLogsModel->getLoginLogs();
			}else{
				$loginLogArr = $loginLogsModel->getLoginLogsByUserId($id);
			}
			$html = '';
			if(!empty($loginLogArr)){
				foreach($loginLogArr as $k=>$v){
					$html.="<tr><td>{$v['addtime']}</td><td>{$v['truename']}</td><td>{$v['operate_tool']}</td><td>{$v['operate_system']}</td><td>{$v['ip']}</td><td></td></tr>";
				}
			}
			echo $html;
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
			// dump($noticeInfo);
			$post = $_POST;
			$id = $post["id"];
			$data["notice_info"] = $post["notice_info"];
			$data["note_info"] = $post["note_info"];
			if($post){
				if($post["notice_info"] == $noticeInfo[0]["notice_info"] && $post["note_info"] == $noticeInfo[0]["note_info"]){
					echo 0;
				}else{
					$result = D("XgNotice")->updateNotice($id,$data);
					echo $result;
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