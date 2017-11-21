<?php
	namespace Home\Controller;
	
	class CustomerController extends BaseController
	{
		
		public function index(){
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
	    	$this->assign("productType",$productType);


			$get = $_GET;
			$post = $_POST;
			// var_dump($get);var_dump($post);
			$result = $this->search($get,$post);
			// var_dump($result);
			$customerInfo = $result['customerInfo'];
			$pageStr = $result['pageStr'];
	 		
			$level = M("xg_customer_level");
			//客户等级信息
			$levelInfos = $level->order("level asc")->select();
			$linkman = M("xg_customer_linkman");
			if(!empty($customerInfo)){
				foreach ($customerInfo as $k => $v) {
					//得到客户等级名称
					$levelInfo = $level->where("level = ".$v["rank"])->find();
					$customerInfo[$k]["level_name"] = $levelInfo["name"];
					//得到省份名称
					if($v["local_procode"] != 0){
						$provinceInfo = $this->getProvince($v["local_procode"]);
						$proName = 	$provinceInfo["0"]["name"];
					}else{
						$proName = "";
					}
					//得到城市名称
					if($v["local_citycode"] != 0){
						$cityInfo = $this->getCity($v["local_citycode"]);
						$cityName = $cityInfo["0"]["name"];
					}else{
						$cityName = "";
					}
					//得到地区名称
					if($v["local_areacode"] != 0){
						$areaInfo = $this->getArea($v["local_areacode"]);
						$areaName = $areaInfo["0"]["name"];
					}else{
						$areaName = "";
					}
					//拼接所在地区名
					if ($provinceInfo["0"]["name"] == $cityInfo["0"]["name"]) {
						$localName = $proName.$areaName;
					} else {
						$localName = $proName.$cityName;
					}
					$customerInfo[$k]["local_name"] = $localName;
					//获得联系人信息
					$linkmanInfo = D("XgCustomerLinkman")->getCustomerLinkInfo($v["id"]);
					$customerInfo[$k]["link_name"] = $linkmanInfo["0"]["name"];
					$customerInfo[$k]["link_phone"] = $linkmanInfo["0"]["phone"];  
				}
			}
			$this->assign("customerInfo",$customerInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("rank",$levelInfos);
			$this->assign("search",$result['search']);

			$this->display();
		}

		public function search($get,$post){
		
	 		if($get['level']){
	 			$condition['where'] = "rank = ".$get['level']."";

	 			$search['rank'] = $get['level'];
	 		}else{
	 			if($post){
		 			if($post['search_content']){
		 				if($post['customer_type'] == 1){
		 					//有等级，进行客户名称查询
			 				if($post['customer_level'] != 0){
			 					$condition['where'] = "rank = ".$post['customer_level']." and cname like '%".$post['search_content']."%'";
			 				}else{
			 					$condition['where'] = "cname like '%".$post['search_content']."%' ";
			 				}
			 			}else{
			 				if($post['customer_type'] == 2){
				 				//查询联系人信息
				 				$customer_linkman = M("xg_customer_linkman")->where("name = '".$post["search_content"]."'")->find();
				 			}
				 			if($post['customer_type'] == 3){
				 				//查询联系人信息
				 				$customer_linkman = M("xg_customer_linkman")->where("phone = '".$post["search_content"]."'")->find();
				 			}
				 			
				 			if(!empty($customer_linkman)){
				 				//联系人信息不为空，带客户等级的搜索
				 				if($post['customer_level'] != 0){
				 					$condition['where'] = "rank = ".$post['customer_level']." and id = ".$customer_linkman['c_id']."";
				 				}else{
				 					// 联系人信息不为空，不带客户等级的搜索
				 					$condition['where'] = "id = ".$customer_linkman['c_id']."";
				 				}
				 			}else{
				 				//搜索的联系人姓名或者电话为空值
				 				$condition['where'] = "id = ''";
				 			}
			 			}
		 			}else{
		 				//有等级，无查询内容
		 				if($post['customer_level'] != 0){
		 					$condition['where'] = "rank = ".$post['customer_level']."";
		 				}
		 			}

		 			$search['rank'] = $post['customer_level'];
		 			$search['type'] = $post['customer_type'];
		 			$search['search_content'] = $post['search_content'];
		 			$search['customer_id'] = $customer_linkman['c_id'];
		 		}
	 		}

	 		//总记录数
	 		$totalCount = M("xg_customer")->where($condition['where'])->count();
	 		$pageSize = 3;
	 		//实例化分页类
	 		$page = new \Think\Page($totalCount,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		//获取分页结果
	 		$pageStr = $page->show();
	 		//总页数
	 		$totalPage = $page->totalPages;
	 		
	 		//查询客户公司信息
	 		$condition['order'] = "id desc";
	 		$condition['limit']['firstRow'] = $firstRow;
	 		$condition['limit']['pageSize'] = $pageSize;

	 		$customerInfo = D("XgCustomer")->getCustomerInfos($condition);

	 		return $data = [
	 					"customerInfo" => $customerInfo,
	 					"pageStr"      => $pageStr,
	 					"search"	   => $search,
	 			];

		}



		//客户详情页
		public function details(){
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
	    	$this->assign("productType",$productType);



	 		//查询客户公司信息
	 		// $condition['where'] = "id = ".$_GET['customer_id']."";
	 		// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
	 		// $customerInfo = $customerInfo['0'];
			$customerInfo = D("XgCustomer")->getCustomerInfo($_GET['customer_id']);
			//得到客户等级名称
			$levelInfo = D("XgCustomer")->getCustomerLevelInfo($customerInfo["rank"]);
			$customerInfo["level_name"] = $levelInfo["name"];

			if($customerInfo["local_procode"] != 0){
				//得到省份名称
				$provinceInfo = $this->getProvince($customerInfo["local_procode"]);
				$proName = 	$provinceInfo["0"]["name"];
			}else{
				$proName = "";
			}
			if($customerInfo["local_citycode"] != 0){
				//得到城市名称
				$cityInfo = $this->getCity($customerInfo["local_citycode"]);
				$cityName = $cityInfo["0"]["name"];
			}else{
				$cityName = "";
			}
			if($customerInfo["local_areacode"] != 0){
				//得到地区名称
				$areaInfo = $this->getArea($customerInfo["local_areacode"]);
				$areaName = $areaInfo["0"]["name"];
			}else{
				$areaName = "";
			}
			//拼接所在地区名
			if ($provinceInfo["0"]["name"] == $cityInfo["0"]["name"]) {
				$localName = $proName.$areaName;
				$address = $proName." ".$areaName." ".$customerInfo["local_address"];
			} else {
				$localName = $proName." ".$cityName;
				$address = $proName." ".$cityName." ".$areaName." ".$customerInfo["local_address"];
			}
			$customerInfo["local_name"] = $localName;
			$customerInfo["address"] = $address;
			//获得联系人信息
			$linkmanInfo = D("XgCustomerLinkman")->getCustomerLinkInfo($customerInfo["id"]);

			$this->assign("customerInfo",$customerInfo);
			$this->assign("linkmanInfo",$linkmanInfo);

			$this->display();
		}

		//联系人详细信息
		public function detailsLinkman(){
			//获得联系人信息
			if($_GET['customer_id']){
				$linkmanInfo = D("XgCustomerLinkman")->getCustomerLinkInfo($_GET["customer_id"]);
			}
			$this->assign("linkmanInfo",$linkmanInfo);

			$this->display();
		}

		//添加客户信息
		public function addCustomer(){
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
	    	$this->assign("productType",$productType);


			//客户等级信息
			$levelInfo = M("xg_customer_level")->order("level asc")->select();
			//获取省份信息
			$provinceInfo = $this->getProvince("0");
			//得到城市名称
			$cityInfo = $this->getCity("0");
			//得到地区名称
			$areaInfo = $this->getArea("0");

			$post = $_POST;
			if ($post) {
				//将表单中提交过来的数据添加到 xg_customer 表中
				$res_1 = D("XgCustomer")->dellCustomerInfo($post,"add");
				// 将表单提交过来的数据添加到 xg_customer_linkman 表中
				if($res_1 > 0){
					$res_2 = D("XgCustomerLinkman")->addCustomerLinkInfo($post,$res_1);
				}
				//根据数据添加的情况来判断页面跳转
				if($res_1 ){
					$this->redirect("Customer/index");
				}else{
					$this->assign("post",$post);
					$this->assign("rank",$levelInfo);
					$this->assign("province",$provinceInfo);
					$this->assign("city",$cityInfo);
					$this->assign("area",$areaInfo);

					$this -> display("addCustomer");
				}
			}
			$this->assign("rank",$levelInfo);
			$this->assign("province",$provinceInfo);
			$this->assign("city",$cityInfo);
			$this->assign("area",$areaInfo);
	 		
			$this->display();
		}

		//已知客户信息，添加联系人信息
		public function addCustomerLinkman(){
			//客户信息ID
			$customer_id = $_GET['customer_id'];
			
			if ($_POST['customer_id'] && $_POST['link_name'] ) {
				//添加联系人信息
				$result = D("XgCustomerLinkman")->addCustomerLinkman($_POST);
				if($result > 0){
					$res = 1;
				}else{
					$res = 2;
				}
				echo $res;	
			}else{
				$this->assign("customer_id",$customer_id);
	 		
				$this->display();
			}
		}
		
		//修改客户信息
		public function update(){
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
	    	$this->assign("productType",$productType);

			
			//查询客户公司信息
			$customerInfo = D("XgCustomer")->getCustomerInfo($_GET['customer_id']);
			//客户公司联系人信息 
			$linkmanInfo = D("XgCustomerLinkman")->getCustomerLinkInfo($customerInfo['id']);
			//客户等级信息 
			$levelInfo = D("XgCustomer")->getCustomerLevelInfo();
			//获取省份信息
			$provinceInfo = $this->getProvince("0");
			//得到城市名称
			$cityInfo = $this->getCity("0");
			//得到地区名称
			$areaInfo = $this->getArea("0");

			$post = $_POST;
			if($post){
				//保存客户信息
				$result_1 = D("XgCustomer")->dellCustomerInfo($post,"update");

				if($post['link_man']){
					//修改客户联系人信息
					$result_2 = D("XgCustomerLinkman")->updateCustomerLinkInfo($post);
				}
				//根据数据添加的情况来判断页面跳转
				if($result_1 && $result_2){
					$this->redirect("Customer/index");
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
			$this->assign("customerInfo",$customerInfo);
			$this->assign("linkmanInfo",$linkmanInfo);
			$this->assign("rank",$levelInfo);
			$this->assign("province",$provinceInfo);
			$this->assign("city",$cityInfo);
			$this->assign("area",$areaInfo);
	 		
			$this->display();
		}

		//删除客户及联系人信息
		public function deletes(){
			$customer_id = $_GET['customer_id'];
			//删除联系人信息
			$result_1 = D("XgCustomerLinkman")->delete($customer_id);
			//删除客户信息
			$result_2 = D("XgCustomer")->delete($customer_id);
			$res_str = " ";
			if($result_1 && $result_2){
				$res_str = "删除成功";
			}else{
				if($result_1){
					$res_str = "联系人信息删除成功，客户信息删除不成功！";
				}
				if($result_2){
					$res_str = "客户信息删除成功，联系人信息删除不成功！";
				}else{
					$res_str = "删除失败！";
				}
			}
			// $res_str = "删除失败！";

			echo json_encode($res_str);
		}

		//删除联系人信息
		public function deletesLinkman(){
			$linkman_id = $_GET['linkman_id'];
			//删除联系人信息
			$result = D("XgCustomerLinkman")->deletesByLinkmanId($linkman_id);
			
			$res_str = " ";
			if($result > 0){
				$res_str = "删除成功";
			}else{
				$res_str = "删除失败！";
			}

			echo json_encode($res_str);
		}
		
		
	}