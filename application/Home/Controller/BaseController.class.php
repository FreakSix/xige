<?php
	namespace Home\Controller;
	use Think\Controller;
	
	class BaseController extends Controller
	{
		function _initialize(){
			
			
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
	}