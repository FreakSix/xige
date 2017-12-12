<?php
	namespace Home\Controller;
	use Think\Controller;
	
	
	class LoginController extends Controller{
		
		public function index(){
			
			$this -> display();
		}
		
		
		/**
		 * 获取登陆的信息
		 */
		public function checkedUser(){
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			$code = $_POST['code'];
			$trueCode = $_SESSION['trueCode'];
			if($code != $trueCode){
				echo "3";exit();
			}
			//根据登喜的信息获取密码(登录的信息可能是登录用户名，也可能是电话号码)
			//现根据用户名获取信息
			$manageModel = D("XgManager");
			$userInfo = $manageModel->getManageInfoByUsername($user);
			//若$userinfo 为空的时候继续根据电话号码吗在获取一次
			if(!empty($userInfo)){
				if($userInfo['password'] == $pass){
					$loginType = "username";
					$this->addLoginLogs($userInfo,$loginType);
					echo 0;   //用户名登陆成功
				}else{
					echo 1;   //用户名存在，但是密码错误
				}
			}else{
				$userInfo = $manageModel->getManageInfoByTel($user);
				if(!empty($userInfo)){
					if($userInfo['password'] == $pass){
						$loginType = "tel";
						$this->addLoginLogs($userInfo,$loginType);
						echo 0;   //电话登录成功
					}else{
						echo 1;    //电话登录，信息存在，但是密码错误
					}
				}else{
					echo 2;   //输入的用户不存在
				}
			}
			
		}
		/**
		 * 将用户登录的信息记录到登陆日志表中
		 * @param unknown $userInfo
		 */
		public function addLoginLogs($userInfo,$loginType){
			//时间，姓名，使用设备，登录IP，IP归属地
			//此处暂时未处理IP的归属地，后期添加
			session_start();
			$_SESSION["userInfo"] = $userInfo;
			$data['user_id'] = $userInfo['id'];
			$data['truename'] = $userInfo['truename'];
			$data['addtime'] = date("Y-m-d H:i:s");
			$data['login_type'] = $loginType;
			$ip = get_client_ip();
			$data['ip'] = $ip;
			$loginLogsModel = D("XgLoginLogs");
			$re = $loginLogsModel->addLoginLogs($data);
		}
		
		/**
		 * 获取验证码
		 */
		public function getYanzhengma(){
			session_start();
			//产生四位的随机验证码
			$trueCode = "";
			$code = "";
			for($i=0;$i<4;$i++){
				$r = rand(0,9);
				$trueCode .= $r;//真实验证码
				$code .= $r." ";//显示验证码
			}
			$_SESSION["trueCode"] = $trueCode;
			$img = imagecreatetruecolor(70,25);//创建一张图片，并指定宽、高
			//画背景
			$bg = imagecolorallocate($img,rand(100,255),rand(100,255),rand(100,255));//设置字体色
			imagefilledrectangle($img,0,0,70,25,$bg);
			$bg = imagecolorallocate($img,rand(0,150),rand(0,150),rand(0,150));
			imagestring($img,5,5,5,$code,$bg);//把随机数画到图片
			//画线
			for($i=0;$i<2;$i++){
				$lineColor = imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
				imageline($img,rand(0,70),rand(0,25), rand(0,70),rand(0,25), $lineColor);
			}
			//画点
			for($i=0;$i<100;$i++){
				$pointColor = imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
				imagesetpixel($img,rand(0,70),rand(0,25),$pointColor);
			}
			imagejpeg($img);
		}
		
		//登录提示页面
		public function loginTishi(){
			$this -> display();
		}
	}