<?php
	namespace Home\Controller;
	
	class CountController extends BaseController{
		
		public function index(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);
			
			//本月新增客户数
			$dateTimeArr = explode("-", date('Y-m'));
			$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			$yue = date("Y-m-d",$yueTime);
		 	$dayNum = date('t', strtotime($yue));

		 	$starTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
		 	$endTime=mktime(23,59,59,$dateTimeArr['1'],$dayNum,$dateTimeArr['0'])+1;

		 	$condition['customer']['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
		 	$customerNum = D("XgCustomer")->getCustomerCount($condition['customer']);


		 	//截止目前客户欠款金额
		 	$debtMoneyEndTime = time();
		 	$condition['debtMoney']['where'] = "add_time <= '".$debtMoneyEndTime."' ";
		 	
			$orderInfo = D("XgOrder")->orderInfo($condition['debtMoney']);

			$debtMoney = 0;		//客户欠款
			if(!empty($orderInfo)){
				foreach ($orderInfo as $k => $v) {
					$debtMoney = $debtMoney + ((float)$v["order_money"] - (float)$v["customer_money"]);
				}
			}


		 	//本月销售额
		 	$condition['money']['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
		 	$orderInfo = D("XgOrder")->orderInfo($condition['money']);
			$money = 0;		//销售额
			if(!empty($orderInfo)){
				foreach ($orderInfo as $k => $v) {
					$money = $money + (float)$v["order_money"];
				}
			}




		 	$this->assign("customerNum",$customerNum);
		 	$this->assign("debtMoney",$debtMoney);
		 	$this->assign("money",$money);
			$this -> display();
		}


		//实际回款金额
		public function customerMoneyCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));

			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$customerAccountInfo = D("XgCustomerAccount")->customerAccountInfo($condition);
					//
					$customerMoney = 0;  //客户回款
					if(!empty($customerAccountInfo)){
						foreach ($customerAccountInfo as $k => $v) {
							$customerMoney = $customerMoney + (float)$v["money"];
						}
					}
					$data['customerMoney'][] = $customerMoney;
			 	}
				
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$customerAccountInfo = D("XgCustomerAccount")->customerAccountInfo($condition);
					//
					$customerMoney = 0;  //客户回款
					if(!empty($customerAccountInfo)){
						foreach ($customerAccountInfo as $k => $v) {
							$customerMoney = $customerMoney + (float)$v["money"];
						}
					}

					$data['customerMoney'][] = $customerMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstCustomerAccountInfo = D("XgCustomerAccount")->getCustomerAccountInfoByOrder("asc"); 
				$firstTime = date("Y",$firstCustomerAccountInfo['add_time']);	
				
				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$customerAccountInfo = D("XgCustomerAccount")->customerAccountInfo($condition);
					$customerMoney = 0;  //客户回款
					if(!empty($customerAccountInfo)){
						foreach ($customerAccountInfo as $k => $v) {
							$customerMoney = $customerMoney + (float)$v["money"];
						}
					}

					$data['customerMoney'][] = $customerMoney;
					$data['x_date'][] = $i."年";

				}
			}
			// dump($data);
			//全部客户信息
			$customerInfo = D("XgCustomer")->getCustomerInfos();
			
			$this->assign("data",$data);
			$this->assign("customerInfo",$customerInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//实际支付金额
		public function supplierMoneyCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			// $where = $this->getConditionInfo($get);
			//如果有供应商名称搜索
			if($get['supplier_id']){
				$where['where'] = "supplier_id = ".$get['supplier_id']."";
			}

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));

			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$supplierAccountInfo = D("XgSupplierAccount")->supplierAccountInfo($condition);
					$supplierMoney = 0;  
					if(!empty($supplierAccountInfo)){
						foreach ($supplierAccountInfo as $k => $v) {
							$supplierMoney = $supplierMoney + (float)$v["money"];
						}
					}

					$data['supplierMoney'][] = $supplierMoney;
			 	}
				
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					
					$supplierAccountInfo = D("XgSupplierAccount")->supplierAccountInfo($condition);
					$supplierMoney = 0;  
					if(!empty($supplierAccountInfo)){
						foreach ($supplierAccountInfo as $k => $v) {
							$supplierMoney = $supplierMoney + (float)$v["money"];
						}
					}

					$data['supplierMoney'][] = $supplierMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstSupplierAccountInfo = D("XgSupplierAccount")->getSupplierAccountInfoByOrder("asc"); 
				$firstTime = date("Y",$firstSupplierAccountInfo['add_time']);	
				
				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$supplierAccountInfo = D("XgSupplierAccount")->supplierAccountInfo($condition);
					$supplierMoney = 0;  //客户回款
					if(!empty($supplierAccountInfo)){
						foreach ($supplierAccountInfo as $k => $v) {
							$supplierMoney = $supplierMoney + (float)$v["money"];
						}
					}

					$data['supplierMoney'][] = $supplierMoney;
					$data['x_date'][] = $i."年";

				}
			}
			//全部供应商信息
			$supplierInfo = D("XgSupplier")->supplierInfo();

			$this->assign("data",$data);
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//订单回款金额
		public function orderCustomerMoneyCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$orderInfo = D("XgOrder")->orderInfo($condition);

					$orderMoney = 0;  //订单总额
					$customerMoney = 0;  //客户回款
					$debtMoney = 0;		//客户欠款
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$orderMoney = $orderMoney + (float)$v["order_money"];
							$customerMoney = $customerMoney + (float)$v["customer_money"];
							$debtMoney = $debtMoney + ((float)$v["order_money"] - (float)$v["customer_money"]);
						}
					}

					$data['orderMoney'][] = $orderMoney;
					$data['customerMoney'][] = $customerMoney;
					$data['debtMoney'][] = $debtMoney;
			 	}

				$data['x_date'] = $this->x_date_day($dayNum);

			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$orderInfo = D("XgOrder")->orderInfo($condition);

					$orderMoney = 0;  //订单总额
					$customerMoney = 0;  //客户回款
					$debtMoney = 0;		//客户欠款
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$orderMoney = $orderMoney + (float)$v["order_money"];
							$customerMoney = $customerMoney + (float)$v["customer_money"];
							$debtMoney = $debtMoney + ((float)$v["order_money"] - (float)$v["customer_money"]);
						}
					}

					$data['orderMoney'][] = $orderMoney;
					$data['customerMoney'][] = $customerMoney;
					$data['debtMoney'][] = $debtMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$orderInfo = D("XgOrder")->orderInfo($condition);

					$orderMoney = 0;  //订单总额
					$customerMoney = 0;  //客户回款
					$debtMoney = 0;		//客户欠款
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$orderMoney = $orderMoney + (float)$v["order_money"];
							$customerMoney = $customerMoney + (float)$v["customer_money"];
							$debtMoney = $debtMoney + ((float)$v["order_money"] - (float)$v["customer_money"]);
						}
					}

					$data['orderMoney'][] = $orderMoney;
					$data['customerMoney'][] = $customerMoney;
					$data['debtMoney'][] = $debtMoney;

					$data['x_date'][] = $i."年";

				}
			}
			//全部客户信息
			$customerInfo = D("XgCustomer")->getCustomerInfos();

			$this->assign("data",$data);
			$this->assign("customerInfo",$customerInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//订单支付金额
		public function orderSupplierMoneyCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			// $where = $this->getConditionInfo($get);
			//如果有供应商名称搜索
			if($get['supplier_id']){
				$where['where'] = "supplier_id = ".$get['supplier_id']."";
			}

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					$orderInfo = D("XgOrder")->orderInfo($condition);

					$supplierMoney = 0;  //向供应商付款
					$debtMoney = 0;		//欠供应商金额
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							//获取该订单的产品信息
							if($where['where'] == ''){
								$condition['orderProduct'] = "order_id = ".$v['id']."";
							}else{
								$condition['orderProduct'] = "order_id = ".$v['id']." and ".$where['where']." ";
							}
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$supplierMoney = $supplierMoney + (float)$vv["supplier_money"];
									$debtMoney = $debtMoney + ((float)$vv["end_money"] - (float)$vv["supplier_money"]);
								}
							}
						}
					}

					$data['supplierMoney'][] = $supplierMoney;
					$data['debtMoney'][] = $debtMoney;
			 	}

				$data['x_date'] = $this->x_date_day($dayNum);

			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					$orderInfo = D("XgOrder")->orderInfo($condition);

					$supplierMoney = 0;  //向供应商付款
					$debtMoney = 0;		//欠供应商金额
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							//获取该订单的产品信息
							if($where['where'] == ''){
								$condition['orderProduct'] = "order_id = ".$v['id']."";
							}else{
								$condition['orderProduct'] = "order_id = ".$v['id']." and ".$where['where']." ";
							}
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$supplierMoney = $supplierMoney + (float)$vv["supplier_money"];
									$debtMoney = $debtMoney + ((float)$vv["end_money"] - (float)$vv["supplier_money"]);
								}
							}
						}
					}

					$data['supplierMoney'][] = $supplierMoney;
					$data['debtMoney'][] = $debtMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					$orderInfo = D("XgOrder")->orderInfo($condition);

					$supplierMoney = 0;  //向供应商付款
					$debtMoney = 0;		//欠供应商金额
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							//获取该订单的产品信息
							if($where['where'] == ''){
								$condition['orderProduct'] = "order_id = ".$v['id']."";
							}else{
								$condition['orderProduct'] = "order_id = ".$v['id']." and ".$where['where']." ";
							}
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$supplierMoney = $supplierMoney + (float)$vv["supplier_money"];
									$debtMoney = $debtMoney + ((float)$vv["end_money"] - (float)$vv["supplier_money"]);
								}
							}
						}
					}

					$data['supplierMoney'][] = $supplierMoney;
					$data['debtMoney'][] = $debtMoney;

					$data['x_date'][] = $i."年";

				}
			}

			//全部供应商信息
			$supplierInfo = D("XgSupplier")->supplierInfo();

			$this->assign("data",$data);
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//成交单数统计
		public function orderCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);
			
			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					
					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
			 	}
				// 
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}

				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
					$data['x_date'][] = $i."年";

				}
				 
			}

			//商品全部分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//商品全部名称
			if(empty($get['product_type'])){
				$condition['product']['where'] = "pid > 0 ";
				$productNameInfo = D("XgProductType")->getProductInfo($condition['product']);
			}else{
				$productNameInfo = D("XgProductType")->getProductTypeByPid($get['product_type']);
			}
			
			$this->assign("data",$data);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productNameInfo",$productNameInfo);
			$this->assign("get",$get);
			$this -> display();

		}


		//成本利润销售额
		public function costCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$costMoney = 0;  //成本总额
					$money = 0;		//销售额
					$getMoney = 0;		//利润
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v["order_money"];
							
							$condition['orderProduct'] = "order_id = ".$v['id']."";
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$costMoney = $costMoney + (float)$vv["cost_money"];
								}
							}

							$getMoney = $money - $costMoney;
						}
					}

					$data['costMoney'][] = $costMoney;
					$data['money'][] = $money;
					$data['getMoney'][] = $getMoney;
			 	}

				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$costMoney = 0;  //成本总额
					$money = 0;		//销售额
					$getMoney = 0;		//利润
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v["order_money"];
							
							$condition['orderProduct'] = "order_id = ".$v['id']."";
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$costMoney = $costMoney + (float)$vv["cost_money"];
								}
							}

							$getMoney = $money - $costMoney;
						}
					}

					$data['costMoney'][] = $costMoney;
					$data['money'][] = $money;
					$data['getMoney'][] = $getMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$costMoney = 0;  //成本总额
					$money = 0;		//销售额
					$getMoney = 0;		//利润
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v["order_money"];
							
							$condition['orderProduct'] = "order_id = ".$v['id']."";
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$costMoney = $costMoney + (float)$vv["cost_money"];
								}
							}

							$getMoney = $money - $costMoney;
						}
					}

					$data['costMoney'][] = $costMoney;
					$data['money'][] = $money;
					$data['getMoney'][] = $getMoney;

					$data['x_date'][] = $i."年";

				}
			}

			//商品全部分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//商品全部名称
			if(empty($get['product_type'])){
				$condition['product']['where'] = "pid > 0 ";
				$productNameInfo = D("XgProductType")->getProductInfo($condition['product']);
			}else{
				$productNameInfo = D("XgProductType")->getProductTypeByPid($get['product_type']);
			}
			
			$this->assign("data",$data);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productNameInfo",$productNameInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//交易客户数
		public function transactionCustomerCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					
					$orderInfo = D("XgOrder")->orderInfo($condition);
					$orderCustomerInfo = "";
					$customerCount = 0;
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$orderCustomerInfo[$v['customer_id']] = $v['customer_name'];
						}
						$customerCount = count($orderCustomerInfo);
					}
					$data['customerCount'][] = $customerCount;
			 	}
				// 
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}

				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					$orderCustomerInfo = "";
					$customerCount = 0;
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$orderCustomerInfo[$v['customer_id']] = $v['customer_name'];
						}
						$customerCount = count($orderCustomerInfo);
					}
					$data['customerCount'][] = $customerCount;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					$orderCustomerInfo = "";
					$customerCount = 0;
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$orderCustomerInfo[$v['customer_id']] = $v['customer_name'];
						}
						$customerCount = count($orderCustomerInfo);
					}
					$data['customerCount'][] = $customerCount;
					$data['x_date'][] = $i."年";

				}
				 
			}

			$this->assign("data",$data);
			$this->assign("get",$get);
			$this -> display();
		}


		//新增客户数
		public function customerCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($get['customer_level'] == 0){
						$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					}else{
						$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."' and rank = ".$get['customer_level']." ";
					}
					
					$customerNum = D("XgCustomer")->getCustomerCount($condition);
					$data['num'][] = $customerNum;
			 	}
				
				$data['x_date'] = $this->x_date_day($dayNum);

			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}

				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					if($get['customer_level'] == 0){
						$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					}else{
						$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."' and rank = ".$get['customer_level']." ";
					}
					$customerNum = D("XgCustomer")->getCustomerCount($condition);
					$data['num'][] = $customerNum;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstCustomerInfo = D("XgCustomer")->getCustomerInfoByOrder("asc"); 
				$firstTime = date("Y",$firstCustomerInfo['ctime']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($get['customer_level'] == 0){
						$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					}else{
						$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."' and rank = ".$get['customer_level']." ";
					}
					$customerNum = D("XgCustomer")->getCustomerCount($condition);
					$data['num'][] = $customerNum;

					$data['x_date'][] = $i."年";

				}
			}
			//客户等级信息
			$csutomerInfo = D("XgCustomerLevel")->getCustomerLevelInfo();

			$this->assign("data",$data);
			$this->assign("csutomerInfo",$csutomerInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//客户订单数与交易额
		public function customerOrderAndTransactionCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);
			
			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					
					//客户订单数
					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
					//客户交易额
					$orderInfo = D("XgOrder")->orderInfo($condition);
					$money = 0;		//交易额
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v['order_money'];
						}
					}
					$data['money'][] = $money;
			 	}
				// 
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}

				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					//客户订单数
					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
					//客户交易额
					$orderInfo = D("XgOrder")->orderInfo($condition);
					$money = 0;		//交易额
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v['order_money'];
						}
					}
					$data['money'][] = $money;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					//客户订单数
					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
					//客户交易额
					$orderInfo = D("XgOrder")->orderInfo($condition);
					$money = 0;		//交易额
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v['order_money'];
						}
					}
					$data['money'][] = $money;

					$data['x_date'][] = $i."年";

				}
				 
			}

			//全部客户信息
			$customerInfo = D("XgCustomer")->getCustomerInfos();

			$this->assign("data",$data);
			$this->assign("customerInfo",$customerInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//客户订单数排行
		public function customerOrderCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			//所有客户信息
			$customerInfo = D("XgCustomer")->getCustomerInfos();
			// dump($customerInfo);

			if(!empty($customerInfo)){
				foreach ($customerInfo as $k => $v) {
					//
					if($v['cname'] != ''){
						$condition['where'] = "customer_id = ".$v['id']." ";
						$orderCount = D("XgOrder")->getOrderCount($condition);

						$newCustomerInfo[$v['cname']] = $orderCount;
					}
				}
			}
			asort($newCustomerInfo);
			$customerCount = count($newCustomerInfo);
			$webHeight = $customerCount*60;

			if(!empty($newCustomerInfo)){
				foreach ($newCustomerInfo as $kk => $vv) {
					$data['num'][] = $vv;
					$data['customerName'][] = $kk;
				}
			}

			$this->assign("data",$data);
			$this->assign("webHeight",$webHeight);
			$this -> display();
		}


		//客户交易额排行
		public function customerTransactionCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			//所有客户信息
			$customerInfo = D("XgCustomer")->getCustomerInfos();
			// dump($customerInfo);

			if(!empty($customerInfo)){
				foreach ($customerInfo as $k => $v) {
					//
					if($v['cname'] != ''){
						$condition['where'] = "customer_id = ".$v['id']." ";
						$orderInfo = D("XgOrder")->orderInfo($condition);
						$money = 0;		//交易额
						if(!empty($orderInfo)){
							foreach ($orderInfo as $kk => $vv) {
								$money = $money + (float)$vv['order_money'];
							}
						}

						$newCustomerInfo[$v['cname']] = $money;
					}
				}
			}
			asort($newCustomerInfo);
			$customerCount = count($newCustomerInfo);
			$webHeight = $customerCount*60;

			if(!empty($newCustomerInfo)){
				foreach ($newCustomerInfo as $kkk => $vvv) {
					$data['money'][] = $vvv;
					$data['customerName'][] = $kkk;
				}
			}

			$this->assign("data",$data);
			$this->assign("webHeight",$webHeight);
			$this -> display();
		}


		//成交产品总数
		public function productCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);

			if($get['type'] == 'days'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y-m');
				}

				$dateTimeArr = explode("-", $get['search_date']);
				$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
			 	$yue = date("Y-m-d",$yueTime);
			 	$dayNum = date('t', strtotime($yue));
			 	// dump($dayNum);
			 	for ($i=1; $i<= $dayNum ;$i++) {
			 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

					$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$cutomerNum = 0;  //产品数
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v["order_money"];
							
							$condition['orderProduct'] = "order_id = ".$v['id']."";
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$cutomerNum = $cutomerNum + (float)$vv["num"];
								}
							}
						}
					}

					$data['cutomerNum'][] = $cutomerNum;
			 	}
				
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'months'){
				if($get['search_date'] == ''){
					$get['search_date'] = date('Y');
				}

				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$cutomerNum = 0;  //产品数
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v["order_money"];
							
							$condition['orderProduct'] = "order_id = ".$v['id']."";
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$cutomerNum = $cutomerNum + (float)$vv["num"];
								}
							}
						}
					}

					$data['cutomerNum'][] = $cutomerNum;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				//第一条信息录入时间
				$firstOrderInfo = D("XgOrder")->getOrderInfoByOrder("asc"); 
				$firstTime = date("Y",$firstOrderInfo['add_time']);	

				for ($i=$firstTime; $i<=date('Y') ; $i++) {
				 	$starTime=mktime(0,0,0,1,1,$i);

					$endTime=mktime(23,59,59,12,31,$i)+1;

					if($where['where'] == ''){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' ";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}

					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$cutomerNum = 0;  //产品数
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$money = $money + (float)$v["order_money"];
							
							$condition['orderProduct'] = "order_id = ".$v['id']."";
							$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition['orderProduct']);
							if(!empty($orderProductInfo)){
								foreach ($orderProductInfo as $kk => $vv) {
									$cutomerNum = $cutomerNum + (float)$vv["num"];
								}
							}
						}
					}

					$data['cutomerNum'][] = $cutomerNum;

					$data['x_date'][] = $i."年";

				}
				
			}

			//商品全部分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//商品全部名称
			if(empty($get['product_type'])){
				$condition['product']['where'] = "pid > 0 ";
				$productNameInfo = D("XgProductType")->getProductInfo($condition['product']);
			}else{
				$productNameInfo = D("XgProductType")->getProductTypeByPid($get['product_type']);
			}
			
			$this->assign("data",$data);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productNameInfo",$productNameInfo);
			$this->assign("get",$get);
			$this -> display();
		}


		//产品销售额排行
		public function productMoneyCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			//所有产品信息
			$condition['where'] = "pid > 0";
			$productInfo = D("XgProductType")->getProductInfo($condition);
			// dump($productInfo);

			if(!empty($productInfo)){
				foreach ($productInfo as $k => $v) {
					//
					$condition['where'] = "product_name_id = ".$v['id']." and order_id != '' and order_num != '' ";
					$orderProductInfo = D("XgOrderProduct")->orderProductInfo($condition);
					$money = 0;		//交易额
					if(!empty($orderProductInfo)){
						foreach ($orderProductInfo as $kk => $vv) {
							$money = $money + (float)$vv['end_money'];
						}
					}

					$newProductInfo[$v['type_name']] = $money;
				}
			}
			asort($newProductInfo);
			$productCount = count($newProductInfo);
			$webHeight = $productCount*45;

			if(!empty($newProductInfo)){
				foreach ($newProductInfo as $kkk => $vvv) {
					$data['money'][] = $vvv;
					$data['productName'][] = $kkk;
				}
			}

			$this->assign("webHeight",$webHeight);
			$this->assign("data",$data);
			$this -> display();
		}


		//产品销量排行
		public function productNumCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			//所有产品信息
			$condition['where'] = "pid > 0";
			$productInfo = D("XgProductType")->getProductInfo($condition);
			// dump($productInfo);
			
			if(!empty($productInfo)){
				foreach ($productInfo as $k => $v) {
					//
					$condition['where'] = "product_name_id = ".$v['id']." and order_id != '' and order_num != '' ";
					$orderProductInfo = D("XgOrderProduct")->orderProductInfo($condition);
					$count = 0;		//交易额
					if(!empty($orderProductInfo)){
						foreach ($orderProductInfo as $kk => $vv) {
							$count = $count + (float)$vv['num'];
						}
					}

					$newProductInfo[$v['type_name']] = $count;
				}
			}
			asort($newProductInfo);
			$productCount = count($newProductInfo);
			$webHeight = $productCount*45;

			if(!empty($newProductInfo)){
				foreach ($newProductInfo as $kkk => $vvv) {
					$data['num'][] = $vvv;
					$data['productName'][] = $kkk;
				}
			}

			$this->assign("webHeight",$webHeight);
			$this->assign("data",$data);
			$this -> display();
		}


		// //交易金额统计
		// public function supplierTransactiontMoneyCount(){

		// 	$this -> display();
		// }


		//交易金额排行
		public function supplierTransactiontMoneyNum(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);
			
			//所有供应商信息
			$supplierInfo = D("XgSupplier")->supplierInfo();
			// dump($supplierInfo);
			
			if(!empty($supplierInfo)){
				foreach ($supplierInfo as $k => $v) {
					//
					$condition['where'] = "supplier_id = ".$v['supplier_id']." and order_id != '' and order_num != '' ";
					$orderProductInfo = D("XgOrderProduct")->orderProductInfo($condition);
					$money = 0;		//交易额
					if(!empty($orderProductInfo)){
						foreach ($orderProductInfo as $kk => $vv) {
							$money = $money + (float)$vv['end_money'];
						}
					}

					$newSupplierInfoo[$v['supplier_name']] = $money;
				}
			}
			asort($newSupplierInfoo);
			$supplierCount = count($newSupplierInfoo);
			$webHeight = $supplierCount*45;

			if(!empty($newSupplierInfoo)){
				foreach ($newSupplierInfoo as $kkk => $vvv) {
					$data['money'][] = $vvv;
					$data['supplierName'][] = $kkk;
				}
			}

			$this->assign("webHeight",$webHeight);
			$this->assign("data",$data);
			$this -> display();
		}


		//根据搜索条件拼写查询条件语句
		public function getConditionInfo($get){
			$condition = array();

			$order_id_str = '';
			//如果有产品分类搜索  id in (".$id.")"
			if($get['product_type']){
				$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo("product_type_id = ".$get['product_type']."");

				if(!empty($orderProductInfo)){
					foreach ($orderProductInfo as $k => $v) {
						if($v['order_id'] != ''){
							$new_orderProductInfo[$v['order_id']] = $v['order_id'];
						}
					}
					$order_id_str = implode(",", $new_orderProductInfo);
				}
				// dump($order_id_str);
				// $whereArr[] = "id in (".$order_id_str.")";
			}
			//如果有产品名称搜索
			if($get['product_name']){

				$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo("product_name_id = ".$get['product_name']."");

				if(!empty($orderProductInfo)){
					foreach ($orderProductInfo as $k => $v) {
						if($v['order_id'] != ''){
							$new_orderProductInfo[$v['order_id']] = $v['order_id'];
						}
					}
					$order_id_str = implode(",", $new_orderProductInfo);
				}else{
					$order_id_str = '0';
				}
				
			}
			

			if($order_id_str != ''){
				$whereArr[] = "id in (".$order_id_str.")";
			}
			//如果有客户名称搜索
			if($get['customer_id']){
				$whereArr[] = "customer_id = ".$get['customer_id']."";
			}

			$where = implode(' and ',$whereArr);
			$condition['where'] = $where;
			return $condition;
		}

		public function x_date_month(){
			$data = array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月');
			return $data;
		}
		public function x_date_day($dayNum){
			for($i=1;$i<=$dayNum;$i++){
				$data[] = $i;
			}
			return $data;
		}
		
		
	}
