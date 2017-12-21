<?php
	namespace Home\Controller;
	
	
	class CountController extends BaseController{
		
		public function index(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);
			
			$this -> display();
		}

		//客户信息统计
		public function customerCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;
			if($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] != ''){
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
				 	// $data['x_date'] = $this->x_date_day($dayNum);

				}else{
					$yueTime=mktime(0,0,0,date('m'),1,date('Y'));
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($yue);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,date('m'),$i,date('Y'));

						$endTime=mktime(23,59,59,date('m'),$i,date('Y'))+1;

						// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
						if($get['customer_level'] == 0){
							$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
						}else{
							$condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."' and rank = ".$get['customer_level']." ";
						}
						$customerNum = D("XgCustomer")->getCustomerCount($condition);
						$data['num'][] = $customerNum;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);
				 	$get['search_date'] = date('Y')."-".date('m');
				}
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'years' && $get['search_date'] != ''){
				// var_dump($get['type']);
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
				//今年一月一日凌晨的时间戳
				 // $starTime=mktime(0,0,0,1,1,date('Y'));
				 for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,date('Y'));
				 	$yue = date("Y-m-d",$starTime);
				 	// dump($yue);

				 	$dayNum = date('t', strtotime($yue));
				 	// dump($dayNum);

					$endTime=mktime(23,59,59,$i,$dayNum,date('Y'))+1;

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

				 $get['search_date'] = date('Y');

			}

			//取统计数据中的最大值
			$maxNum = (int)max($data['num']);
			$yMax = (floor(($maxNum/10))+1)*10;
			$data['yMax'] = (int)$yMax;

			//客户等级信息
			$csutomerInfo = D("XgCustomerLevel")->getCustomerLevelInfo();

			$this->assign("data",$data);
			$this->assign("csutomerInfo",$csutomerInfo);
			$this->assign("get",$get);
			$this -> display();

		}


		//成本利润销售额统计
		public function costCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);
			// dump($where);
			if($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] != ''){
					$dateTimeArr = explode("-", $get['search_date']);
					$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($dayNum);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

						$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

						if( $where['where'] == null ){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						
						// $customerNum = D("XgOrder")->getOrderCount($condition);
						// $data['num'][] = $customerNum;
						$orderInfo = D("XgOrder")->orderInfo($condition);
						//
						$costMoney = 0;  //成本总额
						$money = 0;		//销售额
						$getMoney = 0;		//利润
						if(!empty($orderInfo)){
							foreach ($orderInfo as $k => $v) {
								$costMoney = $costMoney + (float)$v["cost_money"];
								$money = $money + (float)$v["end_money"];
								$getMoney = $getMoney + ((float)$v["end_money"] - (float)$v["cost_money"]);
							}
						}

						$data['costMoney'][] = $costMoney;
						$data['money'][] = $money;
						$data['getMoney'][] = $getMoney;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);

				}else{
					$yueTime=mktime(0,0,0,date('m'),1,date('Y'));
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($yue);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,date('m'),$i,date('Y'));

						$endTime=mktime(23,59,59,date('m'),$i,date('Y'))+1;

						// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
						if($where['where'] == null){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
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
								$costMoney = $costMoney + (float)$v["cost_money"];
								$money = $money + (float)$v["end_money"];
								$getMoney = $getMoney + ((float)$v["end_money"] - (float)$v["cost_money"]);
							}
						}

						$data['costMoney'][] = $costMoney;
						$data['money'][] = $money;
						$data['getMoney'][] = $getMoney;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);
				 	$get['search_date'] = date('Y')."-".date('m');
				}
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'years' && $get['search_date'] != ''){
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
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
							$costMoney = $costMoney + (float)$v["cost_money"];
							$money = $money + (float)$v["end_money"];
							$getMoney = $getMoney + ((float)$v["end_money"] - (float)$v["cost_money"]);
						}
					}

					$data['costMoney'][] = $costMoney;
					$data['money'][] = $money;
					$data['getMoney'][] = $getMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				
				 for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,date('Y'));
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,date('Y'))+1;

					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					// dump($condition);
					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$costMoney = 0;  //成本总额
					$money = 0;		//销售额
					$getMoney = 0;		//利润
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$costMoney = $costMoney + (float)$v["cost_money"];
							$money = $money + (float)$v["end_money"];
							$getMoney = $getMoney + ((float)$v["end_money"] - (float)$v["cost_money"]);
						}
					}

					$data['costMoney'][] = $costMoney;
					$data['money'][] = $money;
					$data['getMoney'][] = $getMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

				 $get['search_date'] = date('Y');

			}

			// //取统计数据中的最大值
			// $maxArr[] = (int)max($data['costMoney']);
			// $maxArr[] = (int)max($data['money']);
			// $maxArr[] = (int)max($data['getMoney']);

			// // $maxNum = (int)max($maxArr);
			// // // dump($maxNum);
			// // $yMax = (floor(($maxNum/10))+1)*10;
			// // $data['yMax'] = (int)$yMax;
			// $result = $this->get_y_value($maxArr);
			// $data['variance'] = $result['variance'];
			// $data['yMax'] = $result['yMax'];

			// dump($data);

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


		//客户欠款与回款统计
		public function customerMoneyCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);
			// dump($where);
			if($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] != ''){
					$dateTimeArr = explode("-", $get['search_date']);
					$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($dayNum);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

						$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

						if( $where['where'] == null ){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						
						// $customerNum = D("XgOrder")->getOrderCount($condition);
						// $data['num'][] = $customerNum;
						$orderInfo = D("XgOrder")->orderInfo($condition);
						//
						$customerMoney = 0;  //客户回款
						$debtMoney = 0;		//客户欠款
						if(!empty($orderInfo)){
							foreach ($orderInfo as $k => $v) {
								$customerMoney = $customerMoney + (float)$v["customer_money"];
								$debtMoney = $debtMoney + ((float)$v["end_money"] - (float)$v["customer_money"]);
							}
						}

						$data['customerMoney'][] = $customerMoney;
						$data['debtMoney'][] = $debtMoney;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);

				}else{
					$yueTime=mktime(0,0,0,date('m'),1,date('Y'));
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($yue);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,date('m'),$i,date('Y'));

						$endTime=mktime(23,59,59,date('m'),$i,date('Y'))+1;

						// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
						if($where['where'] == null){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						$orderInfo = D("XgOrder")->orderInfo($condition);
						//
						$customerMoney = 0;  //客户回款
						$debtMoney = 0;		//客户欠款
						if(!empty($orderInfo)){
							foreach ($orderInfo as $k => $v) {
								$customerMoney = $customerMoney + (float)$v["customer_money"];
								$debtMoney = $debtMoney + ((float)$v["end_money"] - (float)$v["customer_money"]);
							}
						}

						$data['customerMoney'][] = $customerMoney;
						$data['debtMoney'][] = $debtMoney; 
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);
				 	$get['search_date'] = date('Y')."-".date('m');
				}
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'years' && $get['search_date'] != ''){
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$customerMoney = 0;  //客户回款
					$debtMoney = 0;		//客户欠款
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$customerMoney = $customerMoney + (float)$v["customer_money"];
							$debtMoney = $debtMoney + ((float)$v["end_money"] - (float)$v["customer_money"]);
						}
					}

					$data['customerMoney'][] = $customerMoney;
					$data['debtMoney'][] = $debtMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				
				 for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,date('Y'));
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,date('Y'))+1;

					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					// dump($condition);
					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$customerMoney = 0;  //客户回款
					$debtMoney = 0;		//客户欠款
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$customerMoney = $customerMoney + (float)$v["customer_money"];
							$debtMoney = $debtMoney + ((float)$v["end_money"] - (float)$v["customer_money"]);
						}
					}

					$data['customerMoney'][] = $customerMoney;
					$data['debtMoney'][] = $debtMoney;
				 }
				 $data['x_date'] = $this->x_date_month();

				 $get['search_date'] = date('Y');

			}

			//全部客户信息
			$customerInfo = D("XgCustomer")->getCustomerInfos();
			
			$this->assign("data",$data);
			$this->assign("customerInfo",$customerInfo);
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
			// dump($where);
			if($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] != ''){
					$dateTimeArr = explode("-", $get['search_date']);
					$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($dayNum);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

						$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

						if( $where['where'] == null ){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						
						$orderCount = D("XgOrder")->getOrderCount($condition);
						$data['orderCount'][] = $orderCount;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);

				}else{
					$yueTime=mktime(0,0,0,date('m'),1,date('Y'));
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($yue);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,date('m'),$i,date('Y'));

						$endTime=mktime(23,59,59,date('m'),$i,date('Y'))+1;

						// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
						if($where['where'] == null){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						$orderCount = D("XgOrder")->getOrderCount($condition);
						$data['orderCount'][] = $orderCount;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);
				 	$get['search_date'] = date('Y')."-".date('m');
				}
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'years' && $get['search_date'] != ''){
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				
				 for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,date('Y'));
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,date('Y'))+1;

					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					// dump($condition);
					$orderCount = D("XgOrder")->getOrderCount($condition);
					$data['orderCount'][] = $orderCount;
				 }
				 $data['x_date'] = $this->x_date_month();

				 $get['search_date'] = date('Y');

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



		//成交产品总数统计
		public function productCount(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get=$_GET;

			$where = $this->getConditionInfo($get);
			// dump($where);
			if($get['type'] == 'months'){
				// var_dump($get['type']);
				if($get['search_date'] != ''){
					$dateTimeArr = explode("-", $get['search_date']);
					$yueTime=mktime(0,0,0,$dateTimeArr['1'],1,$dateTimeArr['0']);
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($dayNum);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,$dateTimeArr['1'],$i,$dateTimeArr['0']);

						$endTime=mktime(23,59,59,$dateTimeArr['1'],$i,$dateTimeArr['0'])+1;

						if( $where['where'] == null ){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						
						$orderInfo = D("XgOrder")->orderInfo($condition);
						//
						$cutomerNum = 0;  //产品数
						if(!empty($orderInfo)){
							foreach ($orderInfo as $k => $v) {
								$cutomerNum = $cutomerNum + (int)$v["num"];
							}
						}

						$data['cutomerNum'][] = $cutomerNum;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);

				}else{
					$yueTime=mktime(0,0,0,date('m'),1,date('Y'));
				 	$yue = date("Y-m-d",$yueTime);
				 	$dayNum = date('t', strtotime($yue));
				 	// dump($yue);
				 	for ($i=1; $i<= $dayNum ;$i++) {
				 		$starTime=mktime(0,0,0,date('m'),$i,date('Y'));

						$endTime=mktime(23,59,59,date('m'),$i,date('Y'))+1;

						// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
						if($where['where'] == null){
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
						}else{
							$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
						}
						$orderInfo = D("XgOrder")->orderInfo($condition);
						//
						$cutomerNum = 0;  //产品数
						if(!empty($orderInfo)){
							foreach ($orderInfo as $k => $v) {
								$cutomerNum = $cutomerNum + (int)$v["num"];
							}
						}

						$data['cutomerNum'][] = $cutomerNum;
				 	}
				 	// $data['x_date'] = $this->x_date_day($dayNum);
				 	$get['search_date'] = date('Y')."-".date('m');
				}
				$data['x_date'] = $this->x_date_day($dayNum);


			}elseif($get['type'] == 'years' && $get['search_date'] != ''){
				// var_dump($get['type']);
				for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,$get['search_date']);
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,$get['search_date'])+1;

					// $condition['where'] = "ctime >= '".$starTime."' and ctime < '".$endTime."'";
					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$cutomerNum = 0;  //产品数
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$cutomerNum = $cutomerNum + (int)$v["num"];
						}
					}

					$data['cutomerNum'][] = $cutomerNum;
				 }
				 $data['x_date'] = $this->x_date_month();

			}else{
				
				 for ($i=1; $i<13 ; $i++) { 
				 	$starTime=mktime(0,0,0,$i,1,date('Y'));
				 	$yue = date("Y-m-d",$starTime);

				 	$dayNum = date('t', strtotime($yue));

					$endTime=mktime(23,59,59,$i,$dayNum,date('Y'))+1;

					if($where['where'] == null){
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."'";
					}else{
						$condition['where'] = "add_time >= '".$starTime."' and add_time < '".$endTime."' and ".$where['where']." ";
					}
					// dump($condition);
					// $orderCount = D("XgOrder")->getOrderCount($condition);
					// $data['orderCount'][] = $orderCount;
					$orderInfo = D("XgOrder")->orderInfo($condition);
					//
					$cutomerNum = 0;  //产品数
					if(!empty($orderInfo)){
						foreach ($orderInfo as $k => $v) {
							$cutomerNum = $cutomerNum + (int)$v["num"];
						}
					}

					$data['cutomerNum'][] = $cutomerNum;
				 }
				 $data['x_date'] = $this->x_date_month();

				 $get['search_date'] = date('Y');

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



		//根据搜索条件拼写查询条件语句
		public function getConditionInfo($get){
			$condition = array();
			//如果有产品分类搜索
			if($get['product_type']){
				$whereArr[] = "product_type_id = ".$get['product_type']."";
			}
			//如果有产品名称搜索
			if($get['product_name']){
				$whereArr[] = "product_name_id = ".$get['product_name']."";
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
		//订单状态下拉框数组
		public function getOrderStatusArr(){
			$orderStatusArr = array(
					"生产中" => 1,
					"已发货" => 2,
					"已收货" => 3,
					"已作废" => 4,
				);
			return $orderStatusArr;
		}
		//付款状态下拉框数组
		public function getMoneyStatusArr(){
			$moneyStatusArr = array(
					"未付款" => 1,
					"部分付款" => 2,
					"已付款" => 3,
				);
			return $moneyStatusArr;
		}
		// public function get_y_value($maxArr){
		// 	$maxNum = (int)max($maxArr);
		// 	$maxNumLen = strlen($maxNum);
		// 	// dump($maxNum);
		// 	// dump($maxNumLen);

		// 	$firstNum = (substr($maxNum, 0, 1))+1;
		// 	// dump($firstNum);

		// 	$variance = "";
		// 	for ($i=1; $i < $maxNumLen-1; $i++) { 
		// 		$variance .= "0";
		// 	}
		// 	$variance = $firstNum.$variance;
		// 	// dump((int)$variance);
		// 	$variance = (int)$variance;

		// 	// if($maxNum <= 10){
		// 	// 	$variance = 2;
		// 	// }elseif ($maxNum <= 100) {
		// 	// 	$variance = 10;
		// 	// }else{
		// 	// 	for ($i=1; $i<10 ; $++) {				
		// 	// 		$topValue = (int)$i."000";
		// 	// 		if(){

		// 	// 		}
		// 	// 	}
		// 	// }
			

		// 	$yMax = (floor($maxNum/$variance)+1)*$variance;
		// 	$data['variance'] = $variance;
		// 	$data['yMax'] = (int)$yMax;

		// 	return $data;
		// }
		
	}
