<?php
	namespace Home\Controller;
	
	
	class SupplierController extends BaseController{
		
		public function index(){

			//
			$province = $this->getProvince("110000");
			var_dump($province);
		
			$this -> display();
		}

		//添加供应商信息
		public function add_supplier(){
			//获取省份信息
			$province = $this->getProvince();
			$this->assign("province",$province);
			//获取城市信息
			$city =M("xg_city")->select();
			$this->assign("city",$city);
			//获取地区信息
			$area =M("xg_area")->select();
			$this->assign("area",$area);

			//接收post传值
			$post = $_POST;

			var_dump($_POST);
			if(!empty($_POST)){
				$this -> display("index");	
			}else{
				$this -> display();
			}
			
		}

		//验证供应商是否已存在
		function checkSname(){
			$sname = $_GET['sname'];
			//echo $proid;
	 		$supplier = M("xg_supplier");
			$arr = $supplier->where("sname = $sname")->select();
			// var_dump($arr);exit;
	 		if(!empty($arr)){
	 			$res = 1;
	 		}else{
	 			$res = 2;
	 		}
	 			echo json_encode($res);
		}

		//联动查询城市信息
		public function getCity(){
		
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
		public function getArea(){
		
			$citycode = $_GET['citycode'];
	 		$area = M("xg_area");
	 		if($citycode == 0){
	 			$arr = $area->select();
	 		}else{
	 			$arr = $area->where("citycode = $citycode")->select();
	 		}
	 			echo json_encode($arr);
		}

		//查询省份信息
		public function getProvince($procode){
	 		// $citycode = empty($_GET['procode'])?$procode:$_GET['procode'];
	 		$province = M("xg_province");
	 		// var_dump($procode);exit;

	 		if($procode == 0){
	 			$arr = $province->select();
	 		}else{
	 			$arr = $province->where("code = $procode")->select();
	 		}
	 		return $arr;
		}

		
	}