<?php
	namespace Home\Controller;
	use Think\Controller;
	
	class BaseController extends Controller
	{
		//判断用户是否登录
		public function _initialize()
		{
			if(!isset($_SESSION['userInfo'])) {
				$this->redirect('Login/index');
			}
			// dump($_SESSION);
			$id = $_SESSION["userInfo"]["id"];
			// dump($id);
			$data = D("XgManager")->getSessionId($id);
			$sessionId = $data["0"]["session_id"];
			if($sessionId != session_id()){
				session_destroy();
				echo "<script>alert('您的帐号已在其他地方登录，您已被迫退出系统！');</script>";
				echo "<script>location.href='".U("Login/index")."'</script>";
			}
		}
		public function __construct(){
		    parent::__construct();
		    
		    $SESSION_OPTIONS = C("SESSION_OPTIONS");

		    //判断会话是否过期
		    if ((time() - $_SESSION['userInfo']['session_start_time']) > $SESSION_OPTIONS['expire'] ) {
		        session_unset();
		        session_destroy();//真正的销毁在这里！
		        $this->redirect('Login/index');
		    }
		}
		//查询省份信息
		public function getProvince($procode){
	 		$province = M("xg_province");

	 		if($procode == 0){
	 			$arr = $province->select();
	 		}else{
	 			$arr = $province->where("code = $procode")->select(); 
	 		}
	 		return $arr;
		}

		//查询城市信息
		public function getCity($citycode){
	 		$city = M("xg_city");

	 		if($citycode == 0){
	 			$arr = $city->select();
	 		}else{
	 			$arr = $city->where("code = $citycode")->select();
	 		}
	 		return $arr;
		}

		//查询地区信息
		public function getArea($areacode){
	 		$area = M("xg_area");

	 		if($areacode == 0){
	 			$arr = $area->select();
	 		}else{
	 			$arr = $area->where("code = $areacode")->select();
	 		}
	 		return $arr;
		}

		//联动查询城市信息
		public function getCitys(){
		
			$procode = $_GET['procode'];
			//echo $proid;
	 		$city = M("xg_city");
	 		if($procode == 0){
	 			$arr = $city->select();
	 		}else{
	 			$arr = $city->where("provincecode = $procode")->select();
	 		}
	 			echo json_encode($arr);
		}

		
		//联动查询地区信息
		public function getAreas(){
		
			$citycode = $_GET['citycode'];
	 		$area = M("xg_area");
	 		if($citycode == 0){
	 			$arr = $area->select();
	 		}else{
	 			$arr = $area->where("citycode = $citycode")->select();
	 		}
	 			echo json_encode($arr);
		}
		public function menu(){
			// 左侧菜单
			$productTypeModel = D("XgProductType");
	    	// 获取商品分类
	    	$productType = $productTypeModel->getProductType();
	    	// 获取商品分类下对应的产品
	    	foreach ($productType as $k => $v) {
	    		$pid = $v["id"];
	    		$productMenu = $productTypeModel->getProductTypeByPid($pid);
	    		// dump($productMenu);
	    		$productType[$k]["productMenu"] = $productMenu;
	    	}
	    	return $productType;
		}
		/**
		 * 将二维数组去重
		 * @param unknown $array2D
		 * @return multitype:
		 */
		function array_unique_fb($array2D){
			foreach ($array2D as $v)
			{
				$v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
				$temp[] = $v;
			}
			$temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
			foreach ($temp as $k => $v)
			{
				$temp[$k] = explode(",",$v); //再将拆开的数组重新组装
			}
			return $temp;
		}
		/**
		 * 判断是否有权限操作当前的方法
		 */
		public function isPower($powerName){
			if($powerName == ""){
				$powerName = $_POST['powerName'];
			}
			$powerStr = $_SESSION['userInfo']['dutyCode'];
			$powerArr = explode(",",$powerStr);
			if(in_array($powerName,$powerArr)){
				//有权限操作，不做任何事情继续执行下面的内容
				echo 1;
				return 1;
			}else{
				//没有权限操作，给出提示，之后停止程序继续执行
				echo 0;
				return 0;
// 				echo "<script>";
// 				echo "alert('您没有权限才做该功能！请联系管理员！')";
// 				echo "</script>";
// 				exit;
			}
			
		}
	}