<?php
	namespace Home\Controller;
	
	class CustomerController extends BaseController
	{
		
		public function index(){
			$get = $_GET;
			$post = $_POST;
			var_dump($get);var_dump($post);
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
					$linkmanInfo = D("XgCustomer")->getCustomerLinkInfo($v["id"]);
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
	 			// var_dump($_GET);exit;
	 			$condition['where'] = "rank = ".$get['level']."";
	 			// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);

	 			$search['rank'] = $get['level'];
	 		}else{
	 			if($post){
		 			// var_dump($_POST);
		 			// var_dump($condition);var_dump($_GET);exit;
		 			if($post['search_content']){
		 				if($post['customer_type'] == 1){
		 					//有等级，进行客户名称查询
			 				if($post['customer_level'] != 0){
			 					$condition['where'] = "rank = ".$post['customer_level']." and cname like '%".$post['search_content']."%'";
			 					// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
			 				}else{
			 					$condition['where'] = "cname like '%".$post['search_content']."%' ";
			 					// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
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
				 					// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
				 				}else{
				 					// 联系人信息不为空，不带客户等级的搜索
				 					$condition['where'] = "id = ".$customer_linkman['c_id']."";
				 					// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
				 				}
				 			}else{
				 				//搜索的联系人姓名或者电话为空值
				 				// $customerInfo = array();
				 				$condition['where'] = "id = ''";
		 						// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
				 			}
			 			}
		 			}else{
		 				//有等级，无查询内容
		 				if($post['customer_level'] != 0){
		 					$condition['where'] = "rank = ".$post['customer_level']."";
		 					// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
		 				}else{
		 					// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
		 				}
		 			}

		 			$search['rank'] = $post['customer_level'];
		 			$search['type'] = $post['customer_type'];
		 			$search['search_content'] = $post['search_content'];
		 			$search['customer_id'] = $customer_linkman['c_id'];
		 		}else{
		 			// $customerInfo = D("XgCustomer")->getCustomerInfos($condition);
		 		}
	 		}
	 		// var_dump($condition);

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
			$linkmanInfo = D("XgCustomer")->getCustomerLinkInfo($customerInfo["id"]);

			$this->assign("customerInfo",$customerInfo);
			$this->assign("linkmanInfo",$linkmanInfo);

			$this->display();
		}

		public function addCustomer(){
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
					$res_2 = D("XgCustomer")->addCustomerLinkInfo($post,$res_1);
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
		
		//修改客户信息
		public function update(){
			//查询客户公司信息
			$customerInfo = D("XgCustomer")->getCustomerInfo($_GET['customer_id']);
			//客户公司联系人信息 
			$linkmanInfo = D("XgCustomer")->getCustomerLinkInfo($customerInfo['id']);
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
					$result_2 = D("XgCustomer")->updateCustomerLinkInfo($post);
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

		
		
	}