<?php
	namespace Home\Controller;
	use Think\Controller;
	
	class BaseController extends Controller
	{
		//判断用户是否登录
		public function _initialize()
		{
			if($_SESSION['userInfo'] == NULL) {
				$this->success("你还没有登录，暂无权限浏览此页面，请登录！",__APP__.'/Login/index');
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
		
	}