<?php
	namespace Home\Controller;
	
	
	class OrderController extends BaseController{
		// 订单首页
		public function index(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);
			
			$get = $_GET;

			$condition = array();
			if(!empty($get)){

				//如果有时间
				if($get['search_date_value'] ){
					$dateTimeArr = explode("-", $get['search_date_value']);
					foreach ($dateTimeArr as $k => $v) {
						$str = str_replace('年','-',$v);
						$str = str_replace('月','-',$str);
						$str = rtrim($str,"日");
						$dateTimeArr_2[] = $str;
					}
					$startTime = strtotime($dateTimeArr_2['0']);
					$endTime = (strtotime($dateTimeArr_2['1'])) + 86400;

					$whereArr[] = "add_time >= '".$startTime."' and add_time < '".$endTime."'";
				}else{
					$get['search_date_value'] = '';
				}
				//如果有订单状态
				if($get['order_status'] ){
					$whereArr[] = "order_status = '".$get['order_status']."'";
				}
				//如果有付款状态
				if($get['money_status'] ){
					$whereArr[] = "money_status = '".$get['money_status']."'";
				}
				//如果有输入值搜索
				if($get['search_value']){
					if($get['input_type_value'] == 'customer'){
						$whereArr[] = "customer_name like '%".$get['search_value']."%'";
					}else if($get['input_type_value'] == 'supplier'){
						$whereArr[] = "supplier_name like '%".$get['search_value']."%'";
					}else{
						$whereArr[] = "product_name like '%".$get['search_value']."%'";
					}
				}else{
					$get['search_value'] = '';
				}

				$where = implode(' and ',$whereArr);
				$condition['where'] = $where;
			}
			//排除已删除的订单
			if(empty($condition['where'])){
				$condition['where'] = "order_status != 9";
			}else{
				$condition['where'] = "order_status != 9 and  ".$condition['where'];
			}
			
			//订单信息中符合条件的总记录数
			$count = D("XgOrder")->getOrderCount($condition);
	 		$pageSize = 15;
	 		//实例化分页类
	 		$page = new \Think\Page($count,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		//获取分页结果
	 		$pageStr = $page->show();
	 		//总页数
	 		$totalPage = $page->totalPages;
	 		
	 		//查询商品名称
	 		$condition['order'] = "id desc";
	 		$condition['limit']['firstRow'] = $firstRow;
	 		$condition['limit']['pageSize'] = $pageSize;

			$productOrderInfo = D("XgOrder")->orderInfo($condition);
			// dump($productOrderInfo);

			if(!empty($productOrderInfo)){
				foreach ($productOrderInfo as $k => $v) {
					//订单状态处理
					if($v['order_status'] == 1){
						$productOrderInfo[$k]['order_status_value'] = "生产中";
					}
					if($v['order_status'] == 2){
						$productOrderInfo[$k]['order_status_value'] = "已发货";
					}
					if($v['order_status'] == 3){
						$productOrderInfo[$k]['order_status_value'] = "已收货";
					}
					if($v['order_status'] == 4){
						$productOrderInfo[$k]['order_status_value'] = "已作废";
					}
					//客户付款状态处理
					if($v['money_status'] == 1){
						$productOrderInfo[$k]['money_status_value'] = "未付款";
					}
					if($v['money_status'] == 2){
						$productOrderInfo[$k]['money_status_value'] = "部分付款";
					}
					if($v['money_status'] == 3){
						$productOrderInfo[$k]['money_status_value'] = "已付款";
					}
					//录入时间处理
					$insertTime =  date("Y-m-d H:i:s",$v['add_time']);
					$productOrderInfo[$k]['insert_time'] = $insertTime;
				}
			}

			//订单状态下拉框数组
			$orderStatusArr = array(
					"生产中" => 1,
					"已发货" => 2,
					"已收货" => 3,
					"已作废" => 4,
				);
			//付款状态下拉框数组
			$moneyStatusArr = array(
					"未付款" => 1,
					"部分付款" => 2,
					"已付款" => 3,
				);

			$this->assign("productOrderInfo",$productOrderInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("orderStatusArr",$orderStatusArr);
			$this->assign("moneyStatusArr",$moneyStatusArr);
			$this->assign("get",$get);

			$this -> display();
		}
		// 导出订货单（客户）
		public function exportOrderForC(){
			$post = $_POST;
			$order_id_str = rtrim($post["order_id_str"],",");   //去除字符串最后的","
			$order_id_arr = explode(",", $order_id_str);   //将字符串转化为数组
			// echo $order_id_str;
			// dump($order_id_arr);
			foreach ($order_id_arr as $k => $v) {
				$id = $v;
				$customer_id[] = D("XgOrder")->getCustomerIdById($id);
			}
			$customerId = $this->array_unique_fb($customer_id);
			$customerIdNum = count($customerId);
			if($customerIdNum > 1){
				echo 1;
			}else{
				foreach ($order_id_arr as $key => $val) {
					$id = $val;
					$data[] = D("XgOrder")->getCustomerIdById($id);
				}
				$exportData = $this->array_unique_fb($data);
				dump($exportData);
			}
		}
		
		// 导出订货单（供应商）
		public function exportOrderForS(){

		}
		// 新增订单页面
		public function addOrder(){
			$customerModel = D("XgCustomer");
			$customerList = $customerModel->getCustomerInfos('');
			$productTypeModel = D("XgProductType");
			$productTypeFirst = $productTypeModel->getProductTypeByPid(0);
			// $parameterStr = "";
			// if(!empty($productTypeFirst)){
			// 	//默认的商品名称信息
			// 	$productNameFirst = D("XgProductType")->getProductTypeByPid($productTypeFirst[0]['id']);
			// 	if(!empty($productNameFirst)){
			// 		//默认的商品型号信息
			// 		$productModelFirst = D("XgProduct")->getProductByPid($productNameFirst[0]['id']);
			// 		//默认的商品规格信息
			// 		$parameterId = explode(",", $productNameFirst[0]['parameter_id_str']);
			// 		if(!empty($parameterId)){
			// 			foreach ($parameterId as $k => $v) {
			// 				//获取商品规格名称信息
			// 				$productParameter = D("XgProductParameter")->getProductParameterById($v);
			// 				//获取该商品型号对应的商品规格信息
			// 				$condition['where'] = "parameter_id = '".$v."' and product_id = '".$productNameFirst[0]['id']."' and product_model_id = '".$productModelFirst['0']['id']."'";
			// 				$productSpec = D("XgProductSpec")->getProductSpecInfo($condition);
			// 				//拼接该商品下第一种商品型号对应的商品规格信息
			// 				if(!empty($productParameter['0'])){
			// 					$specInfo[$k]['parameter'] = $productParameter['0'];
			// 					$parameterStr .= $productParameter['0']['id']."-".$productParameter['0']['name'].",";
			// 					if(!empty($productSpec)){
			// 						$specInfo[$k]['spec'] = $productSpec;
			// 					}
			// 				}
			// 			}
			// 		}
			// 		//默认的供应商信息
			// 		$supplierId = explode(",", $productNameFirst[0]['supplier_id_str']);
			// 		if(!empty($supplierId)){
			// 			foreach ($supplierId as $kk => $vv) {
			// 				$productSupplierFirst[] = D("XgSupplier")->getSupplierInfo($vv);
			// 			}
			// 		}
			// 	}
				
			// }
			// $parameterStr = rtrim($parameterStr,",");
			//全部产品名称
			$condition['where'] = "pid > 0";
			$productNameFirst = D("XgProductType")->getProductInfo($condition);
			
			$this->assign("customerList",$customerList);
			$this->assign("productTypeFirst",$productTypeFirst);
			$this->assign("productNameFirst",$productNameFirst);
			// $this->assign("productModelFirst",$productModelFirst);
			// $this->assign("specInfo",$specInfo);
			// $this->assign("productSupplierFirst",$productSupplierFirst);
			// $this->assign("parameterStr",$parameterStr);

			$this->display("add_order");
		}
		
		//添加订单信息
		public function addOrderInfo(){
			$post=$_POST;
			// dump($post);
			$data = array();
			if(!empty($post)){
				//获取订单编号
				$today =  date("Y-m-d",time());
				
				for ($i=0; $i < 4; $i++) { 
					$randNum .= rand(0,9);
				}
				$todayDate = str_replace('-','',$today);
				$order_id = $todayDate.$randNum;
				//获取客户名称
				$customerInfo = D("XgCustomer")->getCustomerInfo($post['customer_id']);
				$customerName = $customerInfo['cname'];
				//对交货日期进行处理
				$trade_time = strtotime($post['trade_time']);
				//获取产品分类名称
				$productTypeInfo = D("XgProductType")->getProductName($post['product_type']);
				$productTypeName = $productTypeInfo['0']['type_name'];
				//获取产品名称
				$productInfo = D("XgProductType")->getProductName($post['product_name']);
				$productName = $productInfo['0']['type_name'];
				//获取产品型号名称
				$productModelInfo = D("XgProduct")->getProductById($post['product_model']);
				$productModelName = $productModelInfo['0']['name'];
				//产品规格字符串处理
				$spec_arr = array();
				$specStrInfo = rtrim($post['product_spec_id_str'],",");
				$product_spec_arr = explode(",", $specStrInfo);

				foreach ($product_spec_arr as $k => $v) {
					$spec_arr[] = (int)$v;
				}
				sort($spec_arr);
				$spec_str = implode(",",$spec_arr);


				$data['order_id']= $order_id;
				$data['customer_id'] = $post['customer_id'];
				$data['customer_name'] = $customerName;
				$data['trade_time'] = $trade_time;
				$data['linkman_name'] = $post['linkman_name'];
				$data['linkman_tel'] = $post['linkman_tel'];
				$data['link_address'] = $post['link_address'];
				$data['product_type_id'] = $post['product_type'];
				$data['product_type'] = $productTypeName;
				$data['product_name_id'] = $post['product_name'];
				$data['product_name'] = $productName;
				$data['product_model_id'] = $post['product_model'];
				$data['product_model'] = $productModelName;
				$data['product_spec_id_str'] = $spec_str;
				$data['special_technologyh_id_str'] = $post['special_technologyh_id_str'];
				$data['supplier_id'] = $post['supplier_id'];
				$data['supplier_name'] = $post['supplier_name'];
				$data['num'] = $post['num']; 
				$data['material_link'] = $post['product_material_link'];
				$data['cost_money'] = $post['cost_money'];
				$data['discount_money'] = $post['discount_money'];
				$data['end_price'] = $post['end_price'];
				$data['end_money'] = $post['end_money'];
				$data['cost_price'] = $post['cost_price'];
				$data['add_time'] = time();

				$res = D("XgOrder")->addOrderInfo($data);
				echo $res;

			}
		}

		// 订单详情页
		public function detail(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

	    	$orderInfo = D("XgOrder")->getOrderInfoById($_GET['id']);
	    	// dump($orderInfo);
	    	if(!empty($orderInfo)){
	    		//交货日期处理
	    		if(!empty($orderInfo['trade_time'])){
	    			$tradeTime =  date("Y-m-d",$orderInfo['trade_time']);
	    		}else{
	    			$tradeTime = '';
	    		}
				$orderInfo['trade_time_value'] = $tradeTime;
				//商品规格信息处理
				$productParameterSpecArr = explode(",", $orderInfo['product_spec_id_str']);
				if(!empty($productParameterSpecArr)){
					foreach ($productParameterSpecArr as $k => $v) {
						$specInfo = D("XgProductSpec")->getSpecById($v);
						//商品规格名称
						$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
						$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
						$orderInfo['parameter_spec_value'][] = $specInfo['0'];
					}
				}
				//商品特殊工艺信息处理
				$specialTechnologyhStr = '';
				if(!empty($orderInfo['special_technologyh_id_str'])){
					$productSpecialTechnologyhArr = explode(",", $orderInfo['special_technologyh_id_str']);
					foreach ($productSpecialTechnologyhArr as $k => $v) {
						$specialInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($v);
						$specialTechnologyhStr .= $specialInfo['name']."&nbsp;&nbsp;&nbsp;&nbsp;";
					}
				}
				$orderInfo['special_technology_value'] = $specialTechnologyhStr;
	    		//订单状态处理
				if($orderInfo['order_status'] == 1){
					$orderInfo['order_status_value'] = "生产中";
				}
				if($orderInfo['order_status'] == 2){
					$orderInfo['order_status_value'] = "已发货";
				}
				if($orderInfo['order_status'] == 3){
					$orderInfo['order_status_value'] = "已收货";
				}
				if($orderInfo['order_status'] == 4){
					$orderInfo['order_status_value'] = "已作废";
				}
				//客户付款状态处理
				if($orderInfo['money_status'] == 1){
					$orderInfo['money_status_value'] = "未付款";
				}
				if($orderInfo['money_status'] == 2){
					$orderInfo['money_status_value'] = "部分付款";
				}
				if($orderInfo['money_status'] == 3){
					$orderInfo['money_status_value'] = "已付款";
				}
				//录入时间处理
				$insertTime =  date("Y-m-d H:i:s",$orderInfo['add_time']);
				$orderInfo['insert_time'] = $insertTime;
	    	}
			
			$this->assign("orderInfo",$orderInfo);
			$this -> display();
		}
		// 修改订单信息页面
		public function update(){
			$get = $_GET;
			$post = $_POST;
			// dump($get);

			if(!empty($post)){
				// dump($post);

				$id = $post['order_id'];

				$data['trade_time'] = $post['trade_time'];
				$data['linkman_name'] = $post['linkman_name'];
				$data['linkman_tel'] = $post['linkman_phone'];
				$data['link_address'] = $post['link_address'];
				$data['cost_money'] = $post['cost_money'];
				$data['end_money'] = $post['end_money'];
				$data['material_link'] = $post['material_link'];
				$data['money_status'] = $post['money_status'];
				$data['customer_money'] = $post['customer_money'];
				$data['order_status'] = $post['order_status'];
				$data['supplier_money'] = $post['supplier_money'];

				$res = D("XgOrder")->updateOrderInfo($data,$id);

				echo json_encode($res);

			}else{
				$orderInfo = D("XgOrder")->getOrderInfoById($_GET['id']);
		    	// dump($orderInfo);
		    	if(!empty($orderInfo)){
		    		//交货日期处理
					if(!empty($orderInfo['trade_time'])){
		    			$tradeTime =  date("Y-m-d",$orderInfo['trade_time']);
		    		}else{
		    			$tradeTime = '';
		    		}
					$orderInfo['trade_time_value'] = $tradeTime;
					//根据客户信息查询出联系人信息
					$customerLinkManInfo = D("XgCustomerLinkman")->getCustomerLinkInfo($orderInfo['customer_id']);
					// dump($customerLinkManInfo);
					//商品规格信息处理
					$productParameterSpecArr = explode(",", $orderInfo['product_spec_id_str']);
					if(!empty($productParameterSpecArr)){
						// dump($productParameterSpecArr);
						foreach ($productParameterSpecArr as $k => $v) {
							$specInfo = D("XgProductSpec")->getSpecById($v);
							//商品规格名称
							$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
							$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
							$orderInfo['parameter_spec_value'][] = $specInfo['0'];
						}
					}
					//商品特殊工艺信息处理
					$specialTechnologyhStr = '';
					if(!empty($orderInfo['special_technologyh_id_str'])){
						$productSpecialTechnologyhArr = explode(",", $orderInfo['special_technologyh_id_str']);
						// dump($productParameterSpecArr);
						foreach ($productSpecialTechnologyhArr as $k => $v) {
							$specialInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($v);
							$specialTechnologyhStr .= $specialInfo['name']."&nbsp;&nbsp;&nbsp;&nbsp;";
						}
					}
					$orderInfo['special_technology_value'] = $specialTechnologyhStr;
					//客户付款状态处理
					if($orderInfo['money_status'] == 1){
						$orderInfo['money_status_value'] = "未付款";
					}
					if($orderInfo['money_status'] == 2){
						$orderInfo['money_status_value'] = "部分付款";
					}
					if($orderInfo['money_status'] == 3){
						$orderInfo['money_status_value'] = "已付款";
					}
					
		    	}

		    	//订单状态下拉框数组
				$orderStatusArr = array(
						"生产中" => 1,
						"已发货" => 2,
						"已收货" => 3,
						"已作废" => 4,
					);
				//付款状态下拉框数组
				$moneyStatusArr = array(
						"未付款" => 1,
						"部分付款" => 2,
						"已付款" => 3,
					);
				
				$this->assign("orderInfo",$orderInfo);
				$this->assign("customerLinkManInfo",$customerLinkManInfo);
				$this->assign("orderStatusArr",$orderStatusArr);
				$this->assign("moneyStatusArr",$moneyStatusArr);

				$this -> display();
			}
			
		}

		//删除订单信息（修改订单状态为4）
		public function deleteOrderInfo(){
			$post = $_POST;
			// $res = D("XgOrder")->deleteOrderInfoById($post['id']);

			$data['order_status'] = 4; 
			$res = D("XgOrder")->updateOrderInfo($data,$post['id']);

			if($res > 0){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);
		}





		// 报价记录首页
		public function quote(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get = $_GET;
			// dump($get);

			$condition = array();
			if(!empty($get)){
				//如果有时间
				if($get['search_date_value'] ){
					$dateTimeArr = explode("-", $get['search_date_value']);
					foreach ($dateTimeArr as $k => $v) {
						$str = str_replace('年','-',$v);
						$str = str_replace('月','-',$str);
						$str = rtrim($str,"日");
						$dateTimeArr_2[] = $str;
					}
					$startTime = strtotime($dateTimeArr_2['0']);
					$endTime = (strtotime($dateTimeArr_2['1'])) + 86400;

					$whereArr[] = "add_time >= '".$startTime."' and add_time < '".$endTime."'";
				}else{
					$get['search_date_value'] = '';
				}

				//如果有输入值搜索
				if($get['search_value']){
					if($get['input_type_value'] == 'customer'){
						$whereArr[] = "customer_name like '%".$get['search_value']."%'";
						// $condition['where'] = "rank = ".$post['customer_level']." and cname like '%".$post['search_content']."%'";
					}else if($get['input_type_value'] == 'supplier'){
						$whereArr[] = "supplier_name like '%".$get['search_value']."%'";
					}else{
						$whereArr[] = "product_name like '%".$get['search_value']."%'";
					}
				}else{
					$get['search_value'] = '';
				}

				$where = implode(' and ',$whereArr);
				// dump($where);
				$condition['where'] = $where;
			}
			
			//订单信息中符合条件的总记录数
			$count = D("XgQuote")->getQuoteCount($condition);
			// dump($count);
	 		$pageSize = 3;
	 		//实例化分页类
	 		$page = new \Think\Page($count,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		//获取分页结果
	 		$pageStr = $page->show();
	 		//总页数
	 		$totalPage = $page->totalPages;
	 		
	 		//查询商品名称
	 		$condition['order'] = "id desc";
	 		$condition['limit']['firstRow'] = $firstRow;
	 		$condition['limit']['pageSize'] = $pageSize;

			$productQuoteInfo = D("XgQuote")->quoteInfo($condition);
			// dump($productOrderInfo);

			if(!empty($productQuoteInfo)){
				foreach ($productQuoteInfo as $k => $v) {
					//客户等级处理
					$customerLevelInfo = D("XgCustomerLevel")->getLevelInfoById($v['customer_level_id']);
					$productQuoteInfo[$k]['level_name'] = $customerLevelInfo['name'];
					//录入时间处理
					$insertTime =  date("Y-m-d H:i:s",$v['add_time']);
					$productQuoteInfo[$k]['insert_time'] = $insertTime;
				}
			}
			
			$this->assign("productQuoteInfo",$productQuoteInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("get",$get);

			$this -> display("quote");
		}

		public function quoteDetail(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			//报价记录详情
			$quoteInfo = D("XgQuote")->getQuoteInfoById($_GET['id']);
			// dump($quoteInfo);
			if(!empty($quoteInfo)){
	    		//客户等级处理
	    		$customerLevelInfo = D("XgCustomerLevel")->getLevelInfoById($quoteInfo['customer_level_id']);
				$quoteInfo['customer_level_name'] = $customerLevelInfo['name'];
				//商品规格信息处理
				$productParameterSpecArr = explode(",", $quoteInfo['product_spec_id_str']);
				if(!empty($productParameterSpecArr)){
					// dump($productParameterSpecArr);
					foreach ($productParameterSpecArr as $k => $v) {
						$specInfo = D("XgProductSpec")->getSpecById($v);
						//商品规格名称
						$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
						$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
						$quoteInfo['parameter_spec_value'][] = $specInfo['0'];
					}
				}
				//商品特殊工艺信息处理
				$specialTechnologyhStr = '';
				if(!empty($quoteInfo['special_technologyh_id_str'])){
					$productSpecialTechnologyhArr = explode(",", $quoteInfo['special_technologyh_id_str']);
					// dump($productParameterSpecArr);
					foreach ($productSpecialTechnologyhArr as $k => $v) {
						$specialInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($v);
						$specialTechnologyhStr .= $specialInfo['name']."&nbsp;&nbsp;&nbsp;&nbsp;";
					}
				}
				$quoteInfo['special_technology_value'] = $specialTechnologyhStr;
	    		
				//录入时间处理
				$insertTime =  date("Y-m-d H:i:s",$quoteInfo['add_time']);
				$quoteInfo['insert_time'] = $insertTime;
	    	}
	    	// dump($quoteInfo);

			$this->assign("quoteInfo",$quoteInfo);
			$this->display("quote_detail");
		}


		// 新增报价记录页面
		public function addQuote(){
			//客户等级信息
			$customerLevelInfo = D("XgCustomer")->getCustomerLevelInfo();
			// dump($customerLevelInfo);
			//全部产品分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//全部产品名称
			$condition['where'] = "pid > 0";
			$productNameInfo = D("XgProductType")->getProductInfo($condition);

			$this->assign("customerLevelInfo",$customerLevelInfo);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productNameInfo",$productNameInfo);
			$this->display("add_quote");
		}
		//添加订单信息
		public function addQuoteInfo(){
			$post=$_POST;
			// dump($post);
			$data = array();
			if(!empty($post)){
				//获取产品分类名称
				$productTypeInfo = D("XgProductType")->getProductName($post['product_type_id']);
				$productTypeName = $productTypeInfo['0']['type_name'];
				//获取产品名称
				$productInfo = D("XgProductType")->getProductName($post['product_name_id']);
				$productName = $productInfo['0']['type_name'];
				//获取产品型号名称
				$productModelInfo = D("XgProduct")->getProductById($post['product_model_id']);
				$productModelName = $productModelInfo['0']['name'];
				//产品规格字符串处理
				$spec_arr = array();
				$specStrInfo = rtrim($post['product_spec_id_str'],",");
				$product_spec_arr = explode(",", $specStrInfo);

				foreach ($product_spec_arr as $k => $v) {
					$spec_arr[] = (int)$v;
				}
				sort($spec_arr);
				$spec_str = implode(",",$spec_arr);

				$data['customer_name'] = $post['customer_name'];
				$data['customer_level_id'] = $post['customer_level'];
				$data['linkman_name'] = $post['linkman_name'];
				$data['linkman_tel'] = $post['linkman_tel'];
				$data['remark'] = $post['remark'];
				$data['product_type_id'] = $post['product_type_id'];
				$data['product_type'] = $productTypeName;
				$data['product_name_id'] = $post['product_name_id'];
				$data['product_name'] = $productName;
				$data['product_model_id'] = $post['product_model_id'];
				$data['product_model'] = $productModelName;
				$data['product_spec_id_str'] = $spec_str;
				$data['special_technologyh_id_str'] = $post['special_technologyh_id_str'];
				$data['supplier_id'] = $post['supplier_id'];
				$data['supplier_name'] = $post['supplier_name'];
				$data['num'] = $post['num']; 
				$data['discount_money'] = $post['discount_money'];
				$data['end_price'] = $post['end_price'];
				$data['end_money'] = $post['end_money'];
				$data['cost_price'] = $post['cost_price'];
				$data['add_time'] = time();

				// dump($data);
				$res = D("XgQuote")->addQuoteInfo($data);
				echo $res;

			}
		}

		//修改报价记录信息
		public function updateQuote(){
			$get = $_GET;
			$post = $_POST;
			// dump($get);

			if(!empty($post)){
				// dump($post);
				$id = $post['quote_id'];

				$data['customer_name'] = $post['customer_name'];
				$data['customer_level_id'] = $post['customer_level'];
				$data['linkman_name'] = $post['linkman_name'];
				$data['linkman_tel'] = $post['linkman_phone'];
				$data['remark'] = $post['remark'];
				$data['end_money'] = $post['end_money'];

				$res = D("XgQuote")->updateQuoteInfo($data,$id);

				echo json_encode($res);

			}else{
				$quoteInfo = D("XgQuote")->getQuoteInfoById($_GET['id']);
		    	// dump($orderInfo);
		    	if(!empty($quoteInfo)){
					//客户等级信息
					$customerLevelInfo = D("XgCustomerLevel")->getCustomerLevelInfo();;
					//商品规格信息处理
					$productParameterSpecArr = explode(",", $quoteInfo['product_spec_id_str']);
					if(!empty($productParameterSpecArr)){
						// dump($productParameterSpecArr);
						foreach ($productParameterSpecArr as $k => $v) {
							$specInfo = D("XgProductSpec")->getSpecById($v);
							//商品规格名称
							$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
							$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
							$quoteInfo['parameter_spec_value'][] = $specInfo['0'];
						}
					}
					//商品特殊工艺信息处理
					$specialTechnologyhStr = '';
					if(!empty($quoteInfo['special_technologyh_id_str'])){
						$productSpecialTechnologyhArr = explode(",", $quoteInfo['special_technologyh_id_str']);
						// dump($productParameterSpecArr);
						foreach ($productSpecialTechnologyhArr as $k => $v) {
							$specialInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($v);
							$specialTechnologyhStr .= $specialInfo['name']."&nbsp;&nbsp;&nbsp;&nbsp;";
						}
					}
					$quoteInfo['special_technology_value'] = $specialTechnologyhStr;
					
		    	}
		    	// dump($orderInfo);
				
				$this->assign("quoteInfo",$quoteInfo);
				$this->assign("customerLevelInfo",$customerLevelInfo);

				$this -> display("update_quote");
			}
		}

		//删除报价记录信息
		public function deleteQuoteInfo(){
			$post = $_POST;
			$res = D("XgQuote")->deleteQuoteInfoById($post['id']);
			if($res > 0){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);
		}


		//获得全部的产品名称信息
		public function getProductNameInfoAll(){
			$condition['where'] = "pid > 0";
			$productNameInfo = D("XgProductType")->getProductInfo($condition);
			$html = '<option value="">请选择产品名称</option>';
			if(!empty($productNameInfo)){
				foreach($productNameInfo as $k=>$v){
					$html .= " <option value='".$v['id']."' onclick='getProductModel(".$v['id'].")'>".$v['type_name']."</option>";
				}
			}
			echo $html;
		}


		/**
		 * 根据客户的信息获取联系人的信息
		 * @param unknown $customerId
		 */
		public function getLxrByCustomerId($customer_id){
			$customerLinkManModel = D("XgCustomerLinkman");
			$customerLxrArr = $customerLinkManModel->getCustomerLinkInfo($customer_id);
			$html = "<option value=''>请选择联系人</option>";
			if(!empty($customerLxrArr)){
				foreach ($customerLxrArr as $k=>$v){
					$html.= "<option value='".$v['name']."' class='lxr_each' onclick='getLxrByCustomerId(".$v['id'].")'>".$v['name']."</option>";
				}
			}
			echo $html;
		}
		/**
		 * 根据联系人的id获取联系人的地址和电话
		 * @param unknown $lxr_id
		 */
		public function getTelByLxrId($lxr_id){
			$customerLinkManModel = D("XgCustomerLinkman");
			$linkManInfo = $customerLinkManModel->getCustomerLinkInfoByid($lxr_id);
			$data['phone'] = $linkManInfo['phone'];
			$data['address'] = $linkManInfo['address'];
			$data = json_encode($data);
			echo $data;
		}
		/**
		 * 根据产品的分类获取产品的名称
		 * @param unknown $id
		 */
		public function getProductNameByProductType($id){
			$productTypeModel = D("XgProductType");
			$productTypeFirst = $productTypeModel->getProductTypeByPid($id);
			$html = "<option value='' onclick='getProductModel(0)'>请选择产品名称</option>";
			if(!empty($productTypeFirst)){
				foreach($productTypeFirst as $k=>$v){
					$html .= " <option value='".$v['id']."' onclick='getProductModel(".$v['id'].")'>".$v['type_name']."</option>";
				}
			}
			echo $html;
		}
		/**
		 * 根据产品的名称获取产品的型号
		 * @param unknown $id
		 */
		public function getProductModelByProductName($id){
			$productTypeModel = D("XgProduct");
			$productModel = $productTypeModel->getProductByPid($id);
			$html = '';
			if(!empty($productModel)){
				foreach($productModel as $k=>$v){
					$html .= " <li class='xg-product-btn' name='product_model' id='product_model_".$v['id']."' value='".$v['id']."' onclick='getProductSpec(".$v['id'].")'>".$v['name']."</li>";
				}
			}else{
				$html = "<li class='xg-product-btn' value=''>暂无产品型号</li>";
			}
			echo $html;
		}

		/**
		 * 根据产品的名称获取产品的规格
		 * @param unknown $id
		 */
		public function getProductSpecByProductModel($id){
			$productModel = D("XgProduct");
			$productXinghao = $productModel->getProductById($id);
			$productTyprModel = D("XgProductType");
			$productInfo = $productTyprModel->getProduct($productXinghao['0']['pid']);
			$productParameterId = $productInfo[0]['parameter_id_str'];
			$productParameterIdArr = explode(",",$productParameterId);

			$XgProductParameterModel = D("XgProductParameter");
			$XgProductSpecModel = D("XgProductSpec");
			$parameterSpec = array();
			$parameterStr = "";
			if(!empty($productParameterIdArr)){
				foreach($productParameterIdArr as $k=>$v){
					//商品规格名称信息
					$parameter = $XgProductParameterModel->getProductParameterById($v);
					//商品规格数据信息
					$condition['where'] = "parameter_id = '".$v."' and product_id = '".$productInfo[0]['id']."' and product_model_id = '".$id."'";
					$spec = $XgProductSpecModel->getProductSpecInfo($condition);
					//拼接该商品下第一种商品型号对应的商品规格信息
					if(!empty($parameter['0'])){
						$parameterSpec[$k]['parameter'] = $parameter['0'];
						$parameterStr .= $parameter['0']['id']."-".$parameter['0']['name'].",";

						if(!empty($spec)){
							$parameterSpec[$k]['spec'] = $spec;
						}
					}
				}
			}
			$parameterStr = rtrim($parameterStr,",");
			
			$this->assign("parameterSpec",$parameterSpec);
			$this->assign("parameterStr",$parameterStr);
			$this->display("common_parameter");
		}


		/**
		 * 点击选择供应商的按钮，显示对应的供应商信息
		 * @param unknown $id
		 */
		public function getProductSupplierInfo(){
			$get = $_GET;
			$post = $_POST;
			
			//商品规格信息处理
			$spec_arr = array();
			$specStrInfo = rtrim($post['product_spec_str'],",");
			$product_spec_arr = explode(",", $specStrInfo);

			foreach ($product_spec_arr as $k => $v) { 
				$spec_arr[] = (int)$v;
			}
			sort($spec_arr);
			$spec_str = implode(",",$spec_arr);
			
			//查询供应商产品关系表的数据
			$condition['supplier'] = "product_id = '".$post['product_model_id']."' and spec_id_str = '".$spec_str."'";

			$conditionType = "nosc";

			if($post['type'] == 'asc'){
				$condition['supplier'] = "product_id = '".$post['product_model_id']."' and spec_id_str = '".$spec_str."' order by price asc";
				$conditionType = "asc";
			}
			if($post['type'] == 'desc'){
				$condition['supplier'] = "product_id = '".$post['product_model_id']."' and spec_id_str = '".$spec_str."' order by price desc";
				$conditionType = "desc";
			}
			$supplierProductInfo = D("XgSupplierProductRel")->getProductPriceInfo($condition['supplier']);

			$priceIdArr = array();
			$noPriceIdArr = array();
			if(!empty($supplierProductInfo)){
				foreach ($supplierProductInfo as $k => $v) {
					$priceIdArr[] = $v['supplier_id'];
				}
			}
			//查询商品拥有的供应商
			$productName = D("XgProductType")->getProduct($post['product_name_id']);
			//供应商id
			$supplierIdArr = explode(",", $productName['0']['supplier_id_str']);

			if(!empty($productName['0']['supplier_id_str'])){
				foreach ($supplierIdArr as $k => $v) {

					if(!empty($priceIdArr)){
						if(in_array($v, $priceIdArr)){

						}else{
							$noPriceIdArr[] = $v;
							$supplierProductInfo[]['supplier_id'] = $v;

						}
					}else{
						$noPriceIdArr[] = $v;
						$supplierProductInfo[]['supplier_id'] = $v;
					}
				}
			}

			$selectSupplierInfo = array();
			if(!empty($supplierProductInfo)){
				foreach ($supplierProductInfo as $k => $v) {
					//供应商对应的名字
					$supplierNameInfo = D("XgSupplier")->getSupplierInfo($v['supplier_id']);

					$selectSupplierInfo[$k]['supplier_id'] = $v['supplier_id'];
					$selectSupplierInfo[$k]['supplier_name'] = $supplierNameInfo['supplier_name'];
					$selectSupplierInfo[$k]['price'] = $v['price'];
				}
			}else{
				if(!empty($supplierIdArr)){
					foreach ($supplierIdArr as $k => $v) {
						//供应商对应的名字
						$supplierNameInfo = D("XgSupplier")->getSupplierInfo($v);

						$selectSupplierInfo[$k]['supplier_id'] = $v;
						$selectSupplierInfo[$k]['supplier_name'] = $supplierNameInfo['supplier_name'];
						$selectSupplierInfo[$k]['price'] = '';
					}
				}
			}

			$this->assign("selectSupplierInfo",$selectSupplierInfo);
			$this->assign("post",$post);
			$this->assign("conditionType",$conditionType);

			$this->display("common_select_supplier");
		}


		//
		public function getOrderReferInfo(){
			$post=$_POST;
			//查询历史订单信息
			//商品规格信息处理
			$spec_arr = array();
			$specStrInfo = rtrim($post['product_spec_str'],",");
			$product_spec_arr = explode(",", $specStrInfo);

			foreach ($product_spec_arr as $k => $v) {
				$spec_arr[] = (int)$v;
			}
			sort($spec_arr);
			$spec_str = implode(",",$spec_arr);

			$condition = "product_type_id = '".$post['product_type_id']."' and product_name_id = '".$post['product_name_id']."' and product_model_id = '".$post['product_model_id']."' and product_spec_id_str = '".$spec_str."' order by id desc limit 0,3";

			$productHistoryOrderInfo = D("XgOrder")->getProductOrderInfo($condition);
			//查询历史报价信息

			$productHistoryQuoteInfo = D("XgQuote")->getProductQuoteInfo($condition);

			//计算草考价格
			if(empty($post['customer_level'])){
				$customerInfo = D("XgCustomer")->getCustomerInfo($post['customer_id']);
				if(!empty($customerInfo)){
					$customerLevel = D("XgCustomer")->getLevelInfoByLevel($customerInfo['rank']);
					// dump($customerLevel);
					$levelDiscount = $customerLevel['level_discount'];
					
					$discountMoneyVal = $levelDiscount*($post['product_num']*$post['product_cost_price']);
					$discountMoney['money'] = $discountMoneyVal;
					$discountMoney['money_1'] = "￥ ";
					$discountMoney['money_2'] = " 元";
				}else{
					$discountMoney['money'] = "请输入该产品的成本单价！";
					$discountMoney['money_1'] = " ";
					$discountMoney['money_2'] = " ";
				}
			}else{
				//根据客户等级ID获取等级信息 
				$customerLevelInfo = D("XgCustomerLevel")->getLevelInfoById($post['customer_level']);
				$discountMoneyVal = $customerLevelInfo['level_discount']*($post['product_num']*$post['product_cost_price']);
				$discountMoney['money'] = $discountMoneyVal;
				$discountMoney['money_1'] = "￥ ";
				$discountMoney['money_2'] = " 元";
			}

			$this->assign("productHistoryOrderInfo",$productHistoryOrderInfo);
			$this->assign("productHistoryQuoteInfo",$productHistoryQuoteInfo);
			$this->assign("discountMoney",$discountMoney);
			$this->display("common_order_refer_info");
		}


		//根据分类ID判断该分类是否有特殊工艺
		public function getProductSpecialTechnology($id){
			$productTypeInfo = D("XgProductType")->getProduct($id);
			if($productTypeInfo['0']['have_price'] == 2 ){
				$specialTechnologyInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnology();
				// dump($specialTechnologyInfo);
				if(!empty($specialTechnologyInfo)){
					$this->assign("specialTechnologyInfo",$specialTechnologyInfo);
					$this->display("common_special_technology");
				}
			}

		}


		//新增订单中的客户回款弹窗页面
		public function addCustomerMoney(){
			//订单客户回款记录
			$get = $_GET;

			$this->assign("get",$get);
			$this->display("add_customer_money");
		}

		//新增订单中的客户回款信息
		public function addCustomerMoneyInfo(){
			$post = $_POST;
			//根据订单ID查询出订单信息，取需要的数据如表
			$orderInfo = D("XgOrder")->getOrderInfoById($post['order_id']);

			if(!empty($orderInfo)){
				$data['order_id'] = $post['order_id'];
				$data['order_num'] = $orderInfo['order_id'];
				$data['customer_id'] = $orderInfo['customer_id'];
				$data['customer_name'] = $orderInfo['customer_name'];
				$data['money'] = $post['money'];
				$data['remark'] = $post['remark'];
				$data['add_time'] = time();

				$res = D("XgCustomerAccount")->addCusromerAccountInfo($data);
				//如果数据添加成功，则对订单中的客户回款数据及回款状态进行修改
				if($res > 0){
					//
					$customerMoney = $orderInfo['customer_money'] + $post['money'];
					if($customerMoney >= $orderInfo['end_money']){
						$moneyStatus = 3;
					}elseif (($customerMoney < $orderInfo['end_money']) && $customerMoney > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderData['money_status'] = $moneyStatus;
					$orderData['customer_money'] = $customerMoney;

					$res2 = D("XgOrder")->updateOrderInfo($orderData,$post['order_id']);
				}
				echo $res;
			}
		}

		//订单中的客户回款详情 
		public function customerMoneyInfo(){
			//订单客户回款记录
			$get = $_GET;
			$customerMoneyInfo = D("XgCustomerAccount")->getCustomerAccountByOrderId($get['id']);

			if(!empty($customerMoneyInfo)){
				foreach ($customerMoneyInfo as $k => $v) {
					//录入时间处理
					$insertTime =  date("Y-m-d H:i:s",$v['add_time']);
					$customerMoneyInfo[$k]['insert_time'] = $insertTime;
				}
			}

			$this->assign("customerMoneyInfo",$customerMoneyInfo); 
			$this->assign("get",$get);
			$this->display("customer_money_info");
		}

		//修改客户回款记录 
		public function updateCustomerMoneyInfo(){
			//订单客户回款记录
			$get = $_GET;
			$post = $_POST;

			if(!empty($post)){
				//客户回款信息中的原信息
				$oldCustomerAccount = D("XgCustomerAccount")->getCustomerAccountById($post['id']);

				$data['money'] = $post['money'];
				$data['remark'] = $post['remark'];
				// dump($data);

				$res = D("XgCustomerAccount")->updateCustomerAccountInfo($data,$post['id']);
				//如果修改成功，则对订单中的客户回款数据及回款状态进行修改
				if($res > 0 ){
					//根据订单ID查询出订单信息，取需要的数据如表
					$orderInfo = D("XgOrder")->getOrderInfoById($oldCustomerAccount['order_id']);
					//订单中对应的客户的所有的回款金额
					$allCustomerAccount = D('XgCustomerAccount')->getCustomerAccountByOrderId($oldCustomerAccount['order_id']);
					$customerMoney = 0;
					if(!empty($allCustomerAccount)){
						foreach ($allCustomerAccount as $kk => $vv) {
							$customerMoney = $customerMoney + $vv['money'];
						}
					}

					if($customerMoney >= $orderInfo['end_money']){
						$moneyStatus = 3;
					}elseif (($customerMoney < $orderInfo['end_money']) && $customerMoney > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderData['money_status'] = $moneyStatus;
					$orderData['customer_money'] = $customerMoney;

					$res2 = D("XgOrder")->updateOrderInfo($orderData,$oldCustomerAccount['order_id']);
				}
				echo $res;

			}else{
				$customerMoneyInfo = D("XgCustomerAccount")->getCustomerAccountById($get['id']);

				$this->assign("customerMoneyInfo",$customerMoneyInfo); 
				$this->assign("get",$get);
				$this->display("update_customer_money_info");
			}
			
		}

		public function deleteCustomerAccountInfo(){
			$post = $_POST;
			//客户回款信息中的原信息
			$oldCustomerAccount = D("XgCustomerAccount")->getCustomerAccountById($post['id']);

			$res = D("XgCustomerAccount")->deleteCustomerAccountInfoById($post['id']);

			if($res > 0){
				//根据订单ID查询出订单信息，取需要的数据如表
				$orderInfo = D("XgOrder")->getOrderInfoById($oldCustomerAccount['order_id']);
				//订单中对应的客户的所有的回款金额
				$allCustomerAccount = D('XgCustomerAccount')->getCustomerAccountByOrderId($oldCustomerAccount['order_id']);
				$customerMoney = 0;
				if(!empty($allCustomerAccount)){
					foreach ($allCustomerAccount as $kk => $vv) {
						$customerMoney = $customerMoney + $vv['money'];
					}
				}

				if($customerMoney >= $orderInfo['end_money']){
					$moneyStatus = 3;
				}elseif (($customerMoney < $orderInfo['end_money']) && $customerMoney > 0 ) {
					$moneyStatus = 2;
				}else{
					$moneyStatus = 1;
				}

				$orderData['money_status'] = $moneyStatus;
				$orderData['customer_money'] = $customerMoney;

				$res2 = D("XgOrder")->updateOrderInfo($orderData,$oldCustomerAccount['order_id']);

				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);
		}





		



	}