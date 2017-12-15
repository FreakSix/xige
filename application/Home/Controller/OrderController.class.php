<?php
	namespace Home\Controller;
	
	
	class OrderController extends BaseController{
		// 订单首页
		public function index(){
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
						// $condition['where'] = "rank = ".$post['customer_level']." and cname like '%".$post['search_content']."%'";
					}else{
						$whereArr[] = "supplier_name = '".$get['search_value']."'";
					}
				}else{
					$get['search_value'] = '';
				}

				$where = implode(' and ',$whereArr);
				// dump($where);
				$condition['where'] = $where;
			}
			
			//订单信息中符合条件的总记录数
			$count = D("XgOrder")->getOrderCount($condition);
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

		// 新增订单页面
		public function addOrder(){
			$customerModel = D("XgCustomer");
			$customerList = $customerModel->getCustomerInfos('');
			$productTypeModel = D("XgProductType");
			$productTypeFirst = $productTypeModel->getProductTypeByPid(0);
			$parameterStr = "";
			if(!empty($productTypeFirst)){
				//默认的商品名称信息
				$productNameFirst = D("XgProductType")->getProductTypeByPid($productTypeFirst[0]['id']);
				if(!empty($productNameFirst)){
					//默认的商品型号信息
					$productModelFirst = D("XgProduct")->getProductByPid($productNameFirst[0]['id']);
					//默认的商品规格信息
					$parameterId = explode(",", $productNameFirst[0]['parameter_id_str']);
					if(!empty($parameterId)){
						foreach ($parameterId as $k => $v) {
							//获取商品规格名称信息
							$productParameter = D("XgProductParameter")->getProductParameterById($v);
							//获取该商品型号对应的商品规格信息
							$condition['where'] = "parameter_id = '".$v."' and product_id = '".$productNameFirst[0]['id']."' and product_model_id = '".$productModelFirst['0']['id']."'";
							$productSpec = D("XgProductSpec")->getProductSpecInfo($condition);
							//拼接该商品下第一种商品型号对应的商品规格信息
							if(!empty($productParameter['0'])){
								$specInfo[$k]['parameter'] = $productParameter['0'];
								$parameterStr .= $productParameter['0']['id']."-".$productParameter['0']['name'].",";
								if(!empty($productSpec)){
									$specInfo[$k]['spec'] = $productSpec;
								}
							}
						}
					}
					//默认的供应商信息
					$supplierId = explode(",", $productNameFirst[0]['supplier_id_str']);
					if(!empty($supplierId)){
						foreach ($supplierId as $kk => $vv) {
							$productSupplierFirst[] = D("XgSupplier")->getSupplierInfo($vv);
						}
					}
					// dump($specInfo);
				}
				
			}
			$parameterStr = rtrim($parameterStr,",");
			// dump($parameterStr);
			
			$this->assign("customerList",$customerList);
			$this->assign("productTypeFirst",$productTypeFirst);
			$this->assign("productNameFirst",$productNameFirst);
			$this->assign("productModelFirst",$productModelFirst);
			$this->assign("specInfo",$specInfo);
			$this->assign("productSupplierFirst",$productSupplierFirst);
			$this->assign("parameterStr",$parameterStr);

			$this->assign("qqq","aaaa");
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
				// dump($aa);
				// dump($randNum);
				$order_id = $todayDate.$randNum;
				// dump($order_id);
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
				//
				// dump($customerName);
				// dump($trade_time);
				// dump($productTypeName);
				// dump($productName);
				// dump($productModelName); 
				// dump($spec_str);
				// dump($post['end_money']);

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
				$data['cost_money'] = $post['cost_money'];
				$data['discount_money'] = $post['discount_money'];
				$data['end_money'] = $post['end_money'];
				$data['cost_price'] = $post['cost_price'];
				$data['add_time'] = time();

				// dump($data);
				$res = D("XgOrder")->addOrderInfo($data);
				// dump($res);
				echo $res;

			}
		}

		// 订单详情页
		public function detail(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);
	    	$this->assign("productType",$productType);

	    	$orderInfo = D("XgOrder")->getOrderInfoById($_GET['id']);
	    	// dump($orderInfo);
			
			$this->assign("orderInfo",$orderInfo);
			$this -> display();
		}
		// 修改订单信息页面
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
			
			$this -> display();
		}





		// 报价记录首页
		public function quote(){
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
			
			$this -> display("quote");
		}
		// 新增报价记录页面
		public function addQuote(){
			$this->display("add_quote");
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
			$html = '<option value="">请选择产品名称</option>';
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
		 * 根据产品的名称获取产品的供应商信息
		 * @param unknown $id
		 */
		// public function getProductSupplierByProductName($id){
		// 	// $productTypeModel = D("XgProductType");
		// 	$productName = D("XgProductType")->getProduct($id);
		// 	$supplierId = explode(",", $productName['0']['supplier_id_str']);
		// 	$html = "<option value=''>请选择供应商</option>";
		// 	$supplierModel = D("XgSupplier");
		// 	if(!empty($supplierId)){
		// 		foreach ($supplierId as $k => $v) {
		// 			$productSupplierInfo = $supplierModel->getSupplierInfo($v);
		// 			if(!empty($productSupplierInfo)){
		// 				$html .= " <option value='".$productSupplierInfo['supplier_id']."' onclick='getProductPrice()'>".$productSupplierInfo['supplier_name']."</option>";
		// 			}
		// 		}
		// 	}
		// 	echo $html;
		// }
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
			// dump($parameterSpec);
			// $this->assign("productXinghao",$productXinghao);
			$this->assign("parameterSpec",$parameterSpec);
			$this->assign("parameterStr",$parameterStr);
			$this->display("common_parameter");
		}
		/**
		 * 根据选择的产品信息查询产品成本价格
		 * @param unknown $id
		 */
		// public function getProductCostPrice(){
		// 	$post = $_POST;
		// 	$res = array();
		// 	if(!empty($post)){
		// 		//商品规格信息处理
		// 		$spec_arr = array();
		// 		$specStrInfo = rtrim($post['product_spec_str'],",");
		// 		$product_spec_arr = explode(",", $specStrInfo);

		// 		foreach ($product_spec_arr as $k => $v) {
		// 			$spec_arr[] = (int)$v;
		// 		}
		// 		sort($spec_arr);
		// 		$spec_str = implode(",",$spec_arr);
		// 		$condition = "supplier_id = '".$post['product_supplier_id']."' and product_id = '".$post['product_model_id']."' and spec_id_str = '".$spec_str."'";

		// 		$res = D("XgSupplierProductRel")->getProductPriceInfo($condition);
		// 		// dump($res);
		// 		if(!empty($res)){
		// 			$html = "<b style='color: #f00;'>￥ ".$res['0']['price']." 元</b> <input type='hidden' value='".$res['0']['price']."' id='product_cost_price' />";
		// 		}else{
		// 			$html = "<input type='text' name='product_cost_price' class='product_cost_price' id='product_cost_price' value=''>";
		// 		}
		// 		// echo json_encode($res);
		// 		// return $res; 
		// 	}else{
		// 		$html = "<b style='color: #f00;'>请选择产品信息！</b> <input type='hidden' value='' id='product_cost_price' />";
		// 	}
		// 	echo $html;
		// }
		/**
		 * 根据选择的产品信息查询历史订单
		 * @param unknown $id
		 */
		// public function getProductHistoryOrder(){
		// 	$post = $_POST;
		// 	$res = array();
		// 	if(!empty($post)){
		// 		//商品规格信息处理
		// 		$spec_arr = array();
		// 		$specStrInfo = rtrim($post['product_spec_str'],",");
		// 		$product_spec_arr = explode(",", $specStrInfo);

		// 		foreach ($product_spec_arr as $k => $v) {
		// 			$spec_arr[] = (int)$v;
		// 		}
		// 		sort($spec_arr);
		// 		$spec_str = implode(",",$spec_arr);
		// 		$condition = "product_type = '".$post['product_type_id']."' and product_name = '".$post['product_name_id']."' and product_model = '".$post['product_model_id']."' and product_spec_id_str = '".$spec_str."' order by id desc limit 0,3";

		// 		$res = D("XgOrder")->getProductOrderInfo($condition);
		// 		// dump($res);
		// 		if(!empty($res)){
		// 			foreach ($res as $k => $v) {
		// 				$html .= "<div class='am-u-sm-10'> 供应商：<span>".$v['supplier_name']."</span>&nbsp;&nbsp; 客户：<span>".$v['customer_name']."</span>&nbsp;&nbsp; 
		// 						数量：<b style='color: #f00;'>".$v['num']."</b>&nbsp;&nbsp; 成本总价：<b style='color: #f00;'>".$v['cost_money']."元</b>&nbsp;&nbsp; 
		// 						成交总价：<b style='color: #f00;'>".$v['end_money']."元</b> ";
		// 			}
		// 		}else{
		// 			$html = "<div class='am-u-sm-10'><b style='color: #f00;'>该产品暂无历史订单！</b> </div>";
		// 		}
		// 		// echo json_encode($res);
		// 		// return $res; 
		// 	}else{
		// 		$html = "<div class='am-u-sm-10'><b style='color: #f00;'>请选择产品信息！</b> </div>";
		// 	}
		// 	echo $html;
		// }
		/**
		 * 根据产品规格信息查询供应商信息
		 * @param unknown $id
		 */
		// public function selectSupplier(){
		// 	$get = $_GET;
		// 	$post = $_POST;
		// 	// dump($get);
		// 	// if(!empty($get)){
		// 		//商品规格信息处理
		// 		$spec_arr = array();
		// 		$specStrInfo = rtrim($get['product_spec_str'],",");
		// 		$product_spec_arr = explode(",", $specStrInfo);

		// 		foreach ($product_spec_arr as $k => $v) { 
		// 			$spec_arr[] = (int)$v;
		// 		}
		// 		sort($spec_arr);
		// 		$spec_str = implode(",",$spec_arr);
				
		// 		//查询供应商产品关系表的数据
		// 		$condition['supplier'] = "product_id = '".$get['product_model_id']."' and spec_id_str = '".$spec_str."'";

		// 		$conditionType = "nosc";

		// 		if($get['type'] == 'asc'){
		// 			$condition['supplier'] = "product_id = '".$get['product_model_id']."' and spec_id_str = '".$spec_str."' order by price asc";
		// 			$conditionType = "asc";
		// 		}
		// 		if($get['type'] == 'desc'){
		// 			$condition['supplier'] = "product_id = '".$get['product_model_id']."' and spec_id_str = '".$spec_str."' order by price desc";
		// 			$conditionType = "desc";
		// 		}
		// 		$supplierProductInfo = D("XgSupplierProductRel")->getProductPriceInfo($condition['supplier']);
		// 		// dump($supplierProductInfo);

		// 		$priceIdArr = array();
		// 		$noPriceIdArr = array();
		// 		if(!empty($supplierProductInfo)){
		// 			foreach ($supplierProductInfo as $k => $v) {
		// 				$priceIdArr[] = $v['supplier_id'];
		// 			}
		// 		}
		// 		//查询商品拥有的供应商
		// 		$productName = D("XgProductType")->getProduct($get['product_name_id']);
		// 		//供应商id
		// 		$supplierIdArr = explode(",", $productName['0']['supplier_id_str']);

		// 		if(!empty($supplierIdArr)){
		// 			foreach ($supplierIdArr as $k => $v) {
		// 				//供应商对应的名字
		// 				// $supplierNameInfo = D("XgSupplier")->getSupplierInfo($v);
		// 				if(!empty($priceIdArr)){
		// 					if(in_array($v, $priceIdArr)){

		// 					}else{
		// 						$noPriceIdArr[] = $v;
		// 						$supplierProductInfo[]['supplier_id'] = $v;

		// 					}
		// 				}else{
		// 					$noPriceIdArr[] = $v;
		// 					$supplierProductInfo[]['supplier_id'] = $v;
		// 				}
		// 			}
		// 		}
		// 		// dump($priceIdArr);
		// 		// dump($noPriceIdArr);
		// 		// dump($supplierProductInfo);

		// 		$selectSupplierInfo = array();
		// 		if(!empty($supplierProductInfo)){
		// 			foreach ($supplierProductInfo as $k => $v) {
		// 				//供应商对应的名字
		// 				$supplierNameInfo = D("XgSupplier")->getSupplierInfo($v['supplier_id']);

		// 				$selectSupplierInfo[$k]['supplier_id'] = $v['supplier_id'];
		// 				$selectSupplierInfo[$k]['supplier_name'] = $supplierNameInfo['supplier_name'];
		// 				$selectSupplierInfo[$k]['price'] = $v['price'];

		// 			}
		// 		}else{
		// 			if(!empty($supplierIdArr)){
		// 				foreach ($supplierIdArr as $k => $v) {
		// 					//供应商对应的名字
		// 					$supplierNameInfo = D("XgSupplier")->getSupplierInfo($v);

		// 					$selectSupplierInfo[$k]['supplier_id'] = $v;
		// 					$selectSupplierInfo[$k]['supplier_name'] = $supplierNameInfo['supplier_name'];
		// 					$selectSupplierInfo[$k]['price'] = '';
		// 				}
		// 			}
		// 		}
		// 		// dump($selectSupplierInfo);

				


		// 		$this->assign("selectSupplierInfo",$selectSupplierInfo);
		// 		$this->assign("get",$get);
		// 		$this->assign("conditionType",$conditionType);

		// 		$this->display();
		// 	// }
			

			
		// }

		/**
		 * 点击选择供应商的按钮，显示对应的供应商信息
		 * @param unknown $id
		 */
		public function getProductSupplierInfo(){
			$get = $_GET;
			$post = $_POST;
			// dump($get);
			// dump($post);
			
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
			// dump($supplierProductInfo);

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
			// dump($supplierIdArr);

			if(!empty($supplierIdArr)){
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
			// dump($priceIdArr);
			// dump($noPriceIdArr);
			// dump($supplierProductInfo);

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
			// dump($selectSupplierInfo);

			$this->assign("selectSupplierInfo",$selectSupplierInfo);
			$this->assign("post",$post);
			$this->assign("conditionType",$conditionType);

			$this->display("common_select_supplier");
		}

		// public function getProductMemberMoney(){
		// 	$post = $_POST;
		// 	// dump($post);

		// 	if(!empty($post)){
		// 		$customerInfo = D("XgCustomer")->getCustomerInfo($post['customer_id']);
		// 		// dump($customerInfo);

		// 		if(!empty($customerInfo)){
		// 			$customerLevel = D("XgCustomer")->getLevelInfoById($customerInfo['level_id']);
		// 			// dump($customerLevel);
		// 			$levelDiscount = $customerLevel['level_discount'];
					
		// 			$discountMoney = $levelDiscount*$post['product_cost_money'];

		// 			$res = "<b style='color: #f00;'>".$discountMoney."元</b>";
		// 		}else{
		// 			$res = "<b style='color: #f00;'>请选择客户信息！</b>";					
		// 		}
		// 	}else{
		// 		$res = "<b style='color: #f00;'>请选择产品信息！</b>";
		// 	}

		// 	echo $res;
		// }

		public function getOrderReferInfo(){
			$post=$_POST;
			// dump($post);
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
			// dump($condition);

			$productHistoryOrderInfo = D("XgOrder")->getProductOrderInfo($condition);
			// dump($productHistoryOrderInfo);
			//查询历史报价信息

			//计算草考价格
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
			// dump($customerInfo);
			// dump($discountMoney);


			$this->assign("productHistoryOrderInfo",$productHistoryOrderInfo);
			$this->assign("discountMoney",$discountMoney);
			$this->display("common_order_refer_info");

		}

		//根据分类ID判断该分类是否有特殊工艺
		public function getProductSpecialTechnology($id){
			// $post = $_POST;
			$productTypeInfo = D("XgProductType")->getProduct($id);
			if($productTypeInfo['0']['have_price'] == 2 ){
				$specialTechnologyInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnology();
				// dump($specialTechnologyInfo);
				if(!empty($specialTechnologyInfo)){
					// $html = "<div class='am-form-group'> <label for='contact-wechat' class='am-u-sm-2 am-form-label'>特殊工艺：</label> 
					// 		<div class='am-u-sm-10 am-u-end'> <ul class='product_special_technology'> ";
					// foreach ($specialTechnologyInfo as $k => $v) {
					// 	$html .= " <li class='xg-product-btn' id="product_model_{$item.id}" style="margin-right:10px;" value="{$item.id}" onclick="getProductSpec({$item.id})">{$item.name}</li>  ";
					// }
					$this->assign("specialTechnologyInfo",$specialTechnologyInfo);
					$this->display("common_special_technology");
				}
			}
			// echo $html;

		}



		



	}