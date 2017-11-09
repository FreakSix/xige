<?php
	namespace Home\Controller;
	
	
	class SupplierController extends BaseController{
		// 供应商基本信息展示
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
				//获得联系人信息
				$supplierId = $supplierInfo[$k]['supplier_id'];
				$linkmanInfo = D("XgSupplierLinkman")->findLinkman($supplierId);
				$supplierInfo[$k]["linkman_name"] = $linkmanInfo["linkman_name"];
				$supplierInfo[$k]["linkman_phone"] = $linkmanInfo["linkman_phone"];
			}
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
				$supplierModel = D("XgSupplier");
				$res_1 = $supplierModel->addSupplierInfo($supplier);
				if($res_1 > 0){
					var_dump($post);
					$select_num_hide = $post['select_num_hide'];
					for($i=0;$i<=$select_num_hide;$i++){
						$linkman['supplier_id']=$res_1;    //公司的id
						$linkman['linkman_name']=$post['link_name'.$i];
						$linkman['linkman_phone']=$post['link_phone'.$i];
						$linkman['create_time'] = time();
						$linkman['update_time'] = time();
						$model = D("XgSupplierLinkman");
						$res_2 = $model->addSupplierLinkman($linkman);
						dump($res_2);
					}
				}

				if($res_1&&$res_2){
					$this->redirect("Supplier/index");
				}else{
					echo "<script>javascript:history.back(-1);</script>";
				}
					
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

		// 供应商详细信息
		public function detail(){
			//查询客户公司信息
			$supplierInfo = D("XgSupplier")->getSupplierInfo($_GET['supplier_id']);

			if($supplierInfo["supplier_pro_id"] != 0){
				//得到省份名称
				$provinceInfo = $this->getProvince($supplierInfo["supplier_pro_id"]);
				$proName = 	$provinceInfo["0"]["name"];
			}else{
				$proName = "";
			}
			if($supplierInfo["supplier_city_id"] != 0){
				//得到城市名称
				$cityInfo = $this->getCity($customerInfo["supplier_city_id"]);
				$cityName = $cityInfo["0"]["name"];
			}else{
				$cityName = "";
			}
			if($supplierInfo["supplier_area_id"] != 0){
				//得到地区名称
				$areaInfo = $this->getArea($customerInfo["supplier_area_id"]);
				$areaName = $areaInfo["0"]["name"];
			}else{
				$areaName = "";
			}
			//拼接所在地区名
			if ($provinceInfo["0"]["name"] == $cityInfo["0"]["name"]) {
				$localName = $proName.$areaName;
				$address = $proName." ".$areaName." ".$supplierInfo["supplier_street"];
			} else {
				$localName = $proName." ".$cityName;
				$address = $proName." ".$cityName." ".$areaName." ".$supplierInfo["supplier_street"];
			}
			$supplierInfo["local_name"] = $localName;
			$supplierInfo["address"] = $address;
			//获得联系人信息
			$supplierId = $supplierInfo["supplier_id"];
			$linkmanInfo = D("XgSupplierLinkman")->findAllLinkman($supplierId);
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("supplierLinkmanInfo",$linkmanInfo);

			$this->display();
		}

		// 修改供应商信息页面
		public function update(){
			$supplierId = $_GET['supplier_id'];
			//查询客户公司信息
			$supplierInfo = D("XgSupplier")->getSupplierInfo($supplierId);
			//客户公司联系人信息 
			$linkmanInfo = D("XgSupplierLinkman")->findAllLinkman($supplierId);
			//获取省份信息
			$provinceInfo = $this->getProvince("0");
			//得到城市名称
			$cityInfo = $this->getCity("0");
			//得到地区名称
			$areaInfo = $this->getArea("0");
			dump($supplierInfo);
			dump($linkmanInfo);
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("linkmanInfo",$linkmanInfo);
			$this->assign("province",$provinceInfo);
			$this->assign("city",$cityInfo);
			$this->assign("area",$areaInfo);
	 		
			$this->display();

			
			
			
		}
		// 修改供应商信息处理
		public function updateHandle(){
			$supplierId = $_POST['supplier_id'];
			dump($supplierId);
			$post = $_POST;
			dump($post);
			if($post){
				$supplier['supplier_name'] = $post['supplier_name'];
				$supplier['supplier_tel'] = $post['supplier_tel'];
				$supplier['supplier_pro_id'] = $post['supplier_pro'];
				$supplier['supplier_city_id'] = $post['supplier_city'];
				$supplier['supplier_area_id'] = $post['supplier_area'];
				$supplier['supplier_street'] = $post['supplier_street'];
				$supplier['spare_address'] = $post['spare_address'];
				$supplier['info_update_time'] = time();
				//更新供应商信息
				$result_1 = D("XgSupplier")->updateSupplierInfo($supplierId,$supplier);

				if($post['link_man']){
					//修改客户联系人信息
					foreach ($post['link_man'] as $k => $v) {
						$supplierLinkman['supplier_id']=$supplierId;    //供应商id
						$supplierLinkman['linkman_name']=$v['linkman_name'];
						$supplierLinkman['linkman_phone']=$v['linkman_phone'];
						dump($supplierLinkman);
						$result_2 = D("XgSupplierLinkman")->updateSupplierLinkman($supplierId,$supplierLinkman);
					}
				}
				dump($result_2);
				// 根据数据添加的情况来判断页面跳转
				if($result_1 && $result_2){
					$this->redirect("Supplier/index");
				}else{
					$this->assign("customerInfo",$customerInfo);
					$this->assign("linkmanInfo",$linkmanInfo);
					$this->assign("rank",$levelInfo);
					$this->assign("province",$provinceInfo);
					$this->assign("city",$cityInfo);
					$this->assign("area",$areaInfo);

					$this -> display("update");
				}
			}
		}

		
	}