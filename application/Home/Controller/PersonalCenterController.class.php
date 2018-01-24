<?php
	namespace Home\Controller;
	
	
	use Think\Model;
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
			$customerLevelModel = D("XgCustomerLevel");
			$levelArr = $customerLevelModel->getCustomerLevelInfo();
			$this->assign("levelArr",$levelArr);
			$this->display("customer_rank");
		}

		// 新增客户等级页面
		public function addCustomerRank(){
			$this->display("add_customer_rank");
		}
		// 修改客户等级页面
		public function updateCustomerRank(){
			$id = $_REQUEST['id'];
			$customerLevelModel = D("XgCustomerLevel");
			$levelInfo = $customerLevelModel->getLevelInfoById($id);
			$this->assign("levelInfo",$levelInfo);
			$this->display("update_customer_rank");
		}

		// 登录日志页面
		public function loginLog(){
			$userInfo = $_SESSION['userInfo'];
			$select_user_id = isset($_POST['user_id'])?$_POST['user_id']:"";
			//根据职位给权限，要是老板的话可以查看所有的人的登录日志，要是其他人只能查看自己的(老板的duty_id为9)
			$mangerArr = array();
			$loginLogsModel = D("XgLoginLogs");
			if($userInfo['department_id'] == 0){
				if($select_user_id == ''){
					$user_id = $userInfo['id'];
					$count = $loginLogsModel->getLoginLogsByUserIdCount($user_id);
				}else{
					$count = $loginLogsModel->getLoginLogsByUserIdCount($select_user_id);
				}
			}else{
				$user_id = $userInfo['id'];
				$count = $loginLogsModel->getLoginLogsByUserIdCount($user_id);
			}
			
			//分页
			//订单信息中符合条件的总记录数
			$pageSize = 15;
			//实例化分页类
			$page = new \Think\Page($count,$pageSize);
			$firstRow = $page->firstRow;
			// 设置显示页码个数
	 		$page->rollPage = 5;
			//获取分页结果
			$pageStr = $page->show();
			//总页数
			$totalPage = $page->totalPages;
			
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
					$loginLogArr = $loginLogsModel->getLoginLogsByUserId($user_id,$firstRow,$pageSize);
				}else{
					$loginLogArr = $loginLogsModel->getLoginLogsByUserId($select_user_id,$firstRow,$pageSize);
				}
			}else{
				$user_id = $userInfo['id'];
				$loginLogArr = $loginLogsModel->getLoginLogsByUserId($user_id,$firstRow,$pageSize);
			}
			
			$this->assign("mangerArr",$mangerArr);
			$this->assign("pageStr",$pageStr);
			$this->assign("userInfo",$userInfo);
			$this->assign("loginLogArr",$loginLogArr);
			$this->display("login_log");
		}
		//点击不同的人员获取不同的登陆日志
		public function getDifLoginlogs(){
			$id = $_POST['id'];
			$loginLogsModel = D("XgLoginLogs");
			if($id == "all"){
				$count = $loginLogsModel->getLoginLogsCount();
			}else{
				$count = $loginLogsModel->getLoginLogsByUserIdCount($id);
			}
			//订单信息中符合条件的总记录数
			$pageSize = 15;
			//实例化分页类
			$page = new \Think\Page($count,$pageSize);
			$firstRow = $page->firstRow;
			// 设置显示页码个数
	 		$page->rollPage = 5;
			//获取分页结果
			$pageStr = $page->show();
			//总页数
			$totalPage = $page->totalPages;
			
			if($id == "all"){
				$loginLogArr = $loginLogsModel->getLoginLogs($firstRow,$pageSize);
			}else{
				$loginLogArr = $loginLogsModel->getLoginLogsByUserId($id,$firstRow,$pageSize);
			}
			$this->assign("pageStr",$pageStr);
			$this->assign("loginLogArr",$loginLogArr);
			$this->display("getDifLoginlogs");
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
		    $depeatModel = D("XgDepeartment");
		    $depeatArr = $depeatModel->getDepeartAll();
		    $managerModel = D("XgManager");
		    if(!empty($depeatArr)){
		        foreach($depeatArr as $k=>$v){
		            //根据manager_id 获取部门经理的信息
		            $managerInfo = $managerModel->getManagerInfoById($v['manager_id']);
		            if(!empty($managerInfo)){
		            	$depeatArr[$k]['manager'] = $managerInfo;
		            }else{
		            	$depeatArr[$k]['manager'] = "";
		            }
		            //根据部门id在manager表中获取该部门有多少员工
		            $count = $managerModel->getDepartManagerCount($v['id']);
		            $depeatArr[$k]['managerCount'] = $count;
		        }
		    }
		    $this->assign("depeatArr",$depeatArr);
			$this->display("department");
		}
		// 添加部门页面
		public function addDepartment(){
			$this->display("add_department");
		}
		// 修改部门页面
		public function updateDepartment(){
			$id = $_REQUEST['id'];
			$depart_name = $_REQUEST['depart_name'];
			$this->assign("id",$id);
			$this->assign("depart_name",$depart_name);
			$this->display("update_department");
		}
		
		// 分配登录帐号页面
		public function createAccount(){
			$this->display("create_account"); 
		}
		/**
		 * 保存新添加的部门名称
		 */
		public function saveNewAddDepartment(){
		    $depart_name = $_REQUEST['depart_name'];
		    $departModel = D("XgDepeartment");
		    $res = $departModel->saveNewAddDepartment($depart_name);
		    if($res){
		        echo 1;
		    }else{
		        echo 0;
		    }
		}
		/**
		 * 修改部门名称
		 */
		public function saveUpdateDepartment(){
			$id = $_REQUEST['id'];
			$depart_name = $_REQUEST['depart_name'];
			$departModel = D("XgDepeartment");
			$res = $departModel->saveUpdateDepartName($id,$depart_name);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		/**
		 * 删除部门信息
		 */
		public function deleteDepartmentInfo(){
			$id = $_REQUEST['id'];
			$departModel = D("XgDepeartment");
			$res = $departModel->deleteDepartmentById($id);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		// 员工信息页面
		public function staff(){
			//读取员工的信息
			$managerModel = D("XgManager");
			$dutyModel = D("XgDuty");
			$departModel = D("XgDepeartment");
			$managerArr = $managerModel->getAllManager();
			if(!empty($managerArr)){
				foreach($managerArr as $k=>$v){
					//获取职位名称
					$dutyInfo = $dutyModel->getDutyById($v['duty_id']);
					$managerArr[$k]['duty_name'] = $dutyInfo['duty_name'];
					//获取部门名称
					$deprtInfo = $departModel->getDepeartmentById($v['department_id']);
					$managerArr[$k]['depart_name'] = $deprtInfo['depart_name'];
				}
			}
			$this->assign("managerArr",$managerArr);
			$this->display("staff");
		}
		// 新增员工信息页面
		public function addStaff(){
			$departModel = D("XgDepeartment");
			$dutyModel = D("XgDuty");
			$departArr = $departModel->getDepeartAll();
			$firstDepartDutyInfo = array();
			if(!empty($departArr)){
				$firstDepartId = $departArr[0]['id'];
				$firstDepartDutyInfo = $dutyModel->getDutyByDepartId($firstDepartId);
				
			}
			$this->assign("firstDepartDutyInfo",$firstDepartDutyInfo);
			$this->assign("departArr",$departArr);
			$this->display("add_staff");
		}
		// 修改员工信息页面
		public function updateStaff(){
			$manager_id = $_REQUEST['id'];
			$managerModel = D("XgManager");
			$departModel= D("XgDepeartment");
			$dutyModel = D("XgDuty");
			$managerInfo = $managerModel->getManagerInfoById($manager_id);
			//获取该员工的部门信息
			$departInfo = $departModel->getDepeartmentById($managerInfo['department_id']);
			//获取该员工的职位信息
			$dutyInfo = $dutyModel->getDutyById($managerInfo['duty_id']); 
			//获取所有的部门信息
			$departArr = $departModel->getDepeartAll();	
			//获取所有的职位信息
// 			$dutyArr = $dutyModel->getDutyAll();
			
			$this->assign("departArr",$departArr);
// 			$this->assign("dutyArr",$dutyArr);
			$this->assign("managerInfo",$managerInfo);
			$this->assign("departInfo",$departInfo);
			$this->assign("dutyInfo",$dutyInfo);
			$this->display("update_staff");
		}
		//当用户点击部门的时候湖区该部门对应的所哟职位
		public function GetDifDutyByDepartId(){
			$depart_id = $_REQUEST['depart_id'];
			$dutyModel = D("XgDuty");
			$dutyArr = $dutyModel->getDutyByDepartId($depart_id);
			$html = "";
			if(!empty($dutyArr)){
				foreach ($dutyArr as $k=>$v){
					$html .= "<option value='".$v['id']."'>".$v['duty_name']."</option>";
				}
			}
			echo $html;
		}
		
		/**
		 * 保存添加用户的信息
		 */
		public function saveNewAddManager(){
			$data['truename'] = $_REQUEST['truename'];
			$data['username'] = $_REQUEST['username'];
			$data['password'] = "123456";
			$data['tel'] = $_REQUEST['tel'];
			$data['email'] = $_REQUEST['email'];
			$data['department_id'] = $_REQUEST['depart_id'];
			$data['duty_id'] = $_REQUEST['duty_id'];
			$managerModel = D("XgManager");
			$res = $managerModel->addNewManager($data);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		/**
		 * 保存修改员工信息
		 */
		public function saveEditManager(){
			$id = $_REQUEST['id'];
			$data['truename'] = $_REQUEST['truename'];
			$data['username'] = $_REQUEST['username'];
			$data['password'] = "123456";
			$data['tel'] = $_REQUEST['tel'];
			$data['email'] = $_REQUEST['email'];
			$data['department_id'] = $_REQUEST['depart_id'];
			$data['duty_id'] = $_REQUEST['duty_id'];
			$managerModel = D("XgManager");
			$res = $managerModel->updateEditManager($id,$data);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		/**
		 * 删除员工的信息
		 */
		public function deleteManagerInfo(){
			$id = $_REQUEST['id'];
			$managerModel = D("XgManager");
			$res = $managerModel->delManagerInfo($id);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		/**
		 * 注销员工账号
		 */
		public function deleteAccount(){
			$id = $_REQUEST['id'];
			$managerModel = D("XgManager");
			$res = $managerModel->deleteAccount($id);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		/**
		 * 开通员工账号
		 */
		public function kaitongAccount(){
			$id = $_REQUEST['id'];
			$managerModel = D("XgManager");
			$res = $managerModel->kaitongAccount($id);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		/**
		 * 保存客户等级的添加
		 */
		public function saveNewAddKehudengji(){
			$dengji_name = $_REQUEST['dengji_name'];
			$dengji_xishu = $_REQUEST['dengji_xishu'];
			$data['name'] = $dengji_name;
			$data['level'] = $dengji_xishu;
			$data['ctime'] = date("Y-m-d H:i:s");
			$customerLevelModel = D("XgCustomerLevel");
			$res = $customerLevelModel->addLevel($data);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		/**
		 * 保存修改的客户等级
		 */
		public function saveEditLevel(){
			$id = $_REQUEST['id'];
			$level_name = $_REQUEST['level_name'];
			$level_level = $_REQUEST['level_level'];
			$customerLevelModel = D("XgCustomerLevel");
			$res = $customerLevelModel->updateCustomerLevel($id,$level_name,$level_level);
			if($res){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		/**
		 * 删除客户的信息
		 */
		public function deleteCustomerRank(){
			$id = $_REQUEST['id'];
			$customerLevelModel = D("XgCustomerLevel");
			$res = $customerLevelModel->deleteCustomerRank($id);
			if($res){
				echo 1;
			}else{
				echo 2;
			}
		}
	}