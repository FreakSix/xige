<?php
	namespace Home\Controller;
	
	
	use Think\Model;
	class PersonalCenterController extends BaseController{
		
		public function index(){
			$userInfo2 = $_SESSION['userInfo'];
			//从数据库获取用户信息
			$userInfo = D("XgManager")->getManagerInfoById($userInfo2['id']);
			//获取部门
			if($userInfo2['department_id'] != 0){
				$depeartModel = D("XgDepeartment");
				$depeart = $depeartModel->getDepeartmentById($userInfo2['department_id']);
			}else{
				$depeart = "";
			}
			//获取职位
			$dutyModel = D("XgDuty");
			$duty = $dutyModel->getDutyById($userInfo2['duty_id']);
			
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
			$getimagename = $_GET['getimagename'];

			$post = $_POST;
			if(!empty($post)){
        		$imagename="";
				if($_FILES){
					$upload = new \Think\Upload();
					//设置	
					$upload->mimes=array("image/gif","image/png","image/jpeg");
					$upload->autoSub = false;
					$upload->rootPath = "./public/";
					$upload->savePath = "/images/personalPic/";
					//上传
					$imageRs = $upload->upload();
					// var_dump($imageRs);exit;
					$imagename = $imageRs['upload']['savename'];
				}

				if($imagename != ""){
					$personalData['imagename']=$imagename;
					//修改_SESSION 的值
					$_SESSION['userInfo']['imagename'] = $imagename;
				}
				$personalData['username']=$post['user_name'];
				$personalData['email']=$post['email'];
				$personalData['tel']=$post['tel'];

				$res = D("XgManager")->updateEditManager($_GET['id'],$personalData);

				//修改_SESSION 的值
				if($res == 1){
					if($post['email']){
						$_SESSION['userInfo']['email'] = $post['email'];
					}
					if($post['tel']){
						$_SESSION['userInfo']['tel'] = $post['tel'];
					}
				}

				if($post['user_name'] != $_SESSION['userInfo']['username']){
					if($res == 1){
						echo json_encode(3); //修改用户名后重新登录
					}else{
						echo json_encode(2); //修改失败
					}
				}else{
					if($res == 1){
						echo json_encode(1); //修改成功
					}else{
						echo json_encode(2); //修改失败
					}
				}
				
			}else{
				$userInfo = D("XgManager")->getManagerInfoById($id);

				$this->assign("userInfo",$userInfo);
				$this->display("update_user_info");
			}
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
			$old_pwd = md5($_POST['old_pwd']);
			$new_pwd = md5($_POST['new_pwd']);
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
			$count = D("XgPublicMemo")->getPublicMemoCount();
			$pageSize = 15;
	 		//实例化分页类
	 		$page = new \Think\Page($count,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		// 设置显示页码个数
	 		$page->rollPage = 5;
	 		//获取分页结果
	 		$pageStr = $page->show();
	 		//总页数
	 		$totalPage = $page->totalPages;
	 		
	 		//查询商品名称
	 		$condition['order'] = "id desc";
	 		$condition['limit']['firstRow'] = $firstRow;
	 		$condition['limit']['pageSize'] = $pageSize;

			$publicMemoInfo = D("XgPublicMemo")->publicMemoInfo($condition);
			if(!empty($publicMemoInfo)){
				foreach ($publicMemoInfo as $k => $v) {
					//事件时间处理
					$memoTime =  date("Y-m-d",$v['memo_time']);
					$publicMemoInfo[$k]['memo_time_value'] = $memoTime;
					//发布时间处理
					$editTime =  date("Y-m-d H:i:s",$v['edit_time']);
					$publicMemoInfo[$k]['edit_time_value'] = $editTime;
				}
			}

			$this->assign("pageStr",$pageStr);
			$this->assign("publicMemoInfo",$publicMemoInfo);
			$this->display("public_memo");
		}
		// 修改公共备忘录信息页面
		public function updatePublicMemo(){
			$get = $_GET;
			$post = $_POST;

			if(!empty($post)){
		    	$data["memo_time"] = strtotime($post["memo_time"]);
		    	$data["memo_event"] = $post["memo_event"];
		    	$data["memo_level"] = $post["memo_level"];

		    	$res = D("XgPublicMemo")->updatePublicMemo($post['id'],$data);
		    	echo $res;
			}else{
				$publicMemoInfo = D("XgPublicMemo")->getPublicMemoById($get['id']);
				//事件时间处理
				$memoTime =  date("Y-m-d",$publicMemoInfo['memo_time']);
				$publicMemoInfo['memo_time_value'] = $memoTime;

				$this->assign("publicMemoInfo",$publicMemoInfo);
				$this->display("update_public_memo");
			}
		}

		//删除公共备忘录信息
		public function deletePublicMemoInfo(){
			$post = $_POST;
			$res = D("XgPublicMemo")->deletePublicMemo($post['id']);

			if($res > 0){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);
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
			$dutyArr = $dutyModel->getDutyAll();
			
			$this->assign("departArr",$departArr);
			$this->assign("dutyArr",$dutyArr);
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
					$html .= "<option value='".$v['id']."' is_manager='".$v['is_manager']."'>".$v['duty_name']."</option>";
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
			$data['password'] = md5("123456");
			$data['tel'] = $_REQUEST['tel'];
			$data['email'] = $_REQUEST['email'];
			$data['department_id'] = $_REQUEST['depart_id'];
			$data['duty_id'] = $_REQUEST['duty_id'];
			$is_manager = $_POST['is_manager'];
			$managerModel = D("XgManager");
			$userId = $managerModel->addNewManager($data);
			if($is_manager == 1){
				$departModel = D("XgDepeartment");
				$departModel->updateDepartManagerId($_REQUEST['depart_id'],$userId);
			}
			if($userId){
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
			$duty_id = $_REQUEST['duty_id'];
			$data['truename'] = $_REQUEST['truename'];
			$data['username'] = $_REQUEST['username'];
			$data['tel'] = $_REQUEST['tel'];
			$data['email'] = $_REQUEST['email'];
			$data['department_id'] = $_REQUEST['depart_id'];
			$data['duty_id'] = $duty_id;
			
			$old_depart_id = $_REQUEST['old_depart_id'];
			$old_duty_id = $_REQUEST['old_duty_id'];
			$is_manager = $_REQUEST['is_manager'];
			$old_is_manager = $_REQUEST['old_is_manager'];
			if($old_duty_id != $duty_id){
				$depeartModel = D("XgDepeartment");
				if($is_manager == 1 && $old_is_manager == 0){
					//直接修改depeartment的manager_id
					$depeartModel->updateDepartManagerId($_REQUEST['depart_id'],$id);
				}elseif($is_manager == 0 && $old_is_manager == 1){
					//表明此时获取的职位不是管理员，但是之前该人所在的职位是当前部门的管理员
					$depeartModel->updateDepartManagerId($old_depart_id,0);
				}elseif($is_manager == 1 && $old_is_manager == 1){
					//表明此时获取到的职位是管理员，之前该人所在的职位是当前部门的管理员
					$depeartModel->updateDepartManagerId($_REQUEST['depart_id'],$id);
					$depeartModel->updateDepartManagerId($old_depart_id,0);
				}
			}
			
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
		/**
		 * 当添加联系人的时候，若选中的职位是当前部门的负责人时，进行判断提示当前部门是否已经有负责人
		 */
		public function checkIsManager($depart_id){
			$departModel = D("XgDepeartment");
			$res = $departModel->getDepeartmentById($depart_id);
			if(!empty($res)){
				$is_manager = $res['manager_id'];
				if($is_manager == '' || $is_manager == NUll){
					echo 0;
				}else{
					$managerModel = D("XgManager");
					$managerInfo = $managerModel->getManagerInfoById($is_manager);
					echo $managerInfo['truename'];
				}
			}else{
				echo 0;
			}
		}
	}