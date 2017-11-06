<?php
	namespace Home\Controller;
	
	
	class SupplierController extends BaseController{
		// 供应商信息展示
		public function index(){

			//总记录数
	 		$totalCount = M("xg_supplier")->count();
	 		$pageSize = 5;
	 		//实例化分页类
	 		$page = new \Think\Page($totalCount,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		//获取分页结果
	 		$pageStr = $page->show();
	 		//总页数
	 		$totalPage = $page->totalPages;
	 		//查询客户公司信息
			$supplier = M("xg_supplier");
			$supplierInfo = $supplier->order("supplier_id desc")->limit("$firstRow,$pageSize")->select();
			
			foreach ($supplierInfo as $k => $v) {
				//得到省份名称
				$provinceInfo = $this->getProvince($v["supplier_pro_id"]);
				//得到城市名称
				$cityInfo = $this->getCity($v["supplier_city_id"]);
				//得到地区名称
				$areaInfo = $this->getArea($v["supplier_area_id"]);
				//拼接所在地区名
				if ($provinceInfo["0"]["name"] == $cityInfo["0"]["name"]) {
					$localName = $provinceInfo["0"]["name"]."-".$areaInfo["0"]["name"];
				} else {
					$localName = $provinceInfo["0"]["name"]."-".$cityInfo["0"]["name"];
				}
				$supplierInfo[$k]["local_name"] = $localName;
				// //获得联系人信息
				// $linkmanInfo = $linkman->where("c_id = ".$v["id"])->find();
				// $customerInfo[$k]["link_name"] = $linkmanInfo["name"];
				// $customerInfo[$k]["link_phone"] = $linkmanInfo["phone"];  

			}
			dump($supplierInfo);
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("pageStr",$pageStr);
		
			$this -> display();
		}

		//添加供应商信息
		public function addSupplier(){
			//获取省份信息
			$province = $this->getProvince("0");
			$this->assign("province",$province);
			//获取城市信息
			$city =M("xg_city")->select();
			$this->assign("city",$city);
			//获取地区信息
			$area =M("xg_area")->select();
			$this->assign("area",$area);
			//接收post传值
			$post = $_POST;

			dump($_POST);
			if(!empty($_POST)){
				$supplier['supplier_name'] = $post['supplier_name'];
				$supplier['supplier_tel'] = $post['supplier_tel'];
				$supplier['supplier_pro_id'] = $post['supplier_pro'];
				$supplier['supplier_city_id'] = $post['supplier_city'];
				$supplier['supplier_area_id'] = $post['supplier_area'];
				$supplier['supplier_street'] = $post['supplier_street'];
				$supplier['spare_address'] = $post['spare_address'];
				$supplier['info_create_time'] = time();
				$supplier['info_update_time'] = time();
				dump($supplier);
				$supplierModel = D("XgSupplier");
				$result = $supplierModel->addSupplierInfo($supplier);
				dump($result);
				$this -> display("index");	
			}else{
				$this -> display("add_supplier");
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

		

		
	}