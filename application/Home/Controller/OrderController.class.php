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
					$dateTimeArr = explode("To", $get['search_date_value']);
					$startTime = strtotime($dateTimeArr['0']);
					$endTime = (strtotime($dateTimeArr['1'])) + 86400;

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
					$whereArr[] = "customer_money_status = '".$get['money_status']."'";
				}
				//如果有输入值搜索
				if($get['search_value']){
					// if($get['input_type_value'] == 'customer'){
					// 	$whereArr[] = "customer_name like '%".$get['search_value']."%'";
					// }else if($get['input_type_value'] == 'supplier'){
					// 	$whereArr[] = "supplier_name like '%".$get['search_value']."%'";
					// }else{
					// 	$whereArr[] = "product_name like '%".$get['search_value']."%'";
					// }
					$whereArr[] = "customer_name like '%".$get['search_value']."%'";
				}else{
					$get['search_value'] = '';
				}

				$where = implode(' and ',$whereArr);
				$condition['where'] = $where;
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
					//订单总成本计算处理
					$order_cost_money = 0;
					$order_product_id_arr = explode(",", $v['order_product_id']);
					foreach ($order_product_id_arr as $kk => $vv) {
						$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($vv);
						$order_cost_money = $order_cost_money+$orderProductInfo['cost_money'];
					}
					$productOrderInfo[$k]['cost_money'] = $order_cost_money;
					//订单状态处理
		    		$orderStatusInfo = D("XgOrderStatus")->getOrderStatusInfoById($v['order_status']);
		    		$productOrderInfo[$k]['order_status_value'] = $orderStatusInfo['status_name'];
					//客户付款状态处理
					if($v['customer_money_status'] == 1){
						$productOrderInfo[$k]['money_status_value'] = "未回款";
					}
					if($v['customer_money_status'] == 2){
						$productOrderInfo[$k]['money_status_value'] = "部分回款";
					}
					if($v['customer_money_status'] == 3){
						$productOrderInfo[$k]['money_status_value'] = "全额回款";
					}
					
					//录入时间处理
					$insertTime =  date("Y-m-d H:i:s",$v['add_time']);
					$productOrderInfo[$k]['insert_time'] = $insertTime;
				}
			}

			//订单状态下拉框数组
			$orderStatusArr = D("XgOrderStatus")->getOrderStatusInfo();
			//付款状态下拉框数组
			$moneyStatusArr = array(
					"未回款" => 1,
					"部分回款" => 2,
					"全额回款" => 3,
				);
			// dump($orderStatusArr);

			$this->assign("productOrderInfo",$productOrderInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("orderStatusArr",$orderStatusArr);
			$this->assign("moneyStatusArr",$moneyStatusArr);
			$this->assign("get",$get);

			$this -> display();
		}

		// 获取对账单（客户）信息
		public function exportOrder(){
			$get = $_GET;
			$type = $get["type"];
			$orderStatus = $get["order_status"];
			$moneyStatus = $get["money_status"];
			$searchValue = $get["search_value"];
			$dateValue = $get["search_date_value"];
			if(!empty($get)){
				// 判断是导出全部还是导出选中
				if($type=="all"){
					// 如果条件全为空，查询全部信息
					if($orderStatus==0 && $moneyStatus==0 &&  $searchValue==0 && $dateValue==0){
						$orderInfo = D("XgOrder")->getAllOrder();
						// dump($orderInfo);exit;
					}else{
						//如果有时间
						if($dateValue){
							$dateTimeArr = explode("To", $get['search_date_value']);
							$startTime = strtotime($dateTimeArr['0']);
							$endTime = (strtotime($dateTimeArr['1'])) + 86400;
							$whereArr[] = "add_time >= '".$startTime."' and add_time < '".$endTime."'";
						}
						//如果有订单状态
						if($orderStatus){
							$whereArr[] = "order_status = '".$get['order_status']."'";
						}
						//如果有付款状态
						if($moneyStatus){
							$whereArr[] = "customer_money_status = '".$get['money_status']."'";
						}
						//如果有输入值搜索
						if($searchValue){
							$whereArr[] = "customer_name like '%".$get['search_value']."%'";
						}
						$where = implode(' and ',$whereArr);
						$condition['where'] = $where;
						$orderInfo = D("XgOrder")->orderInfo($condition);
						// dump($orderInfo);
					}
				}else if($type=="selected"){
					$orderIdStr = rtrim($get["order_id_str"],",");
					$orderIdArr = explode(",", $orderIdStr);
					sort($orderIdArr);   // 讲数组变成从小到大排列
					for ($i=0; $i < sizeof($orderIdArr); $i++) { 
						$orderDate = D("XgOrder")->getOrderInfoById($orderIdArr[$i]);
						$orderInfo[] = $orderDate;
					}
				}
				
				// dump($orderInfo);exit;
				if($orderInfo){
					// 根据订单编号获取产品信息
					foreach ($orderInfo as $key => $value) {
						$orderCustomerInfo[$value['customer_name']] = $value['customer_name'];
						$productInfo = D("XgOrderProduct")->getProductInfoByOrderNum($value['order_id']);
						// dump($productInfo);
						// dump(sizeof($productInfo));
						
						for ($i=0; $i < sizeof($productInfo); $i++) { 
							// 获取和整理规格信息
							$specIdArr = explode(",", $productInfo[$i]["product_spec_id_str"]);
							// dump($specIdArr);
							$specDetail = "";
							foreach ($specIdArr as $keySpec => $sia) {
								// 规格数据
								$specInfo = D("XgProductSpec")->getSpecById($sia);
								// dump($specInfo);
								// 规格数据对应的规格名称
								$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
								// dump($parameterName);
								$specDetail[] = $parameterName['0']['name'].":".$specInfo['0']['spec_value'];
							}
							$specDetailStr = implode(" / ", $specDetail);
							// dump($specDetailStr);
							// $productInfo[$i]["product_spec_info"] = $specDetailStr;
							// 获取和整理特殊工艺信息
							$specialTecIdArr = explode(",", $productInfo[$i]["special_technologyh_id_str"]);
							// dump($specialTecIdArr);
							$specialTecDetail = "";
							foreach ($specialTecIdArr as $keyTec => $stia) {
								// 特殊工艺数据
								$specialTecInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($stia);
								// dump($specialTecInfo);
								$specialTecDetail[] = $specialTecInfo["name"];
							}
							// dump($specialTecDetail);
							$specialTecDetailStr = implode(",", $specialTecDetail);
							// dump($specialTecDetailStr);
							// $productInfo[$i]["special_tec_info"] = $specialTecDetailStr;
							// 获取产品单位
							$unitInfo = D("XgProductType")->getProduct($productInfo[$i]['product_name_id']);
							$productUnit = $unitInfo["0"]["product_unit"];
							$productInfo[$i]['product_unit'] = $productUnit;
							// 整和产品名称及规格
							if($specialTecDetailStr == ""){
								$productInfo[$i]["product_detail"] = $productInfo[$i]["product_name"]." / ".$productInfo[$i]["product_model"]." / ".$specDetailStr;
							}else{
								$productInfo[$i]["product_detail"] = $productInfo[$i]["product_name"]." / ".$productInfo[$i]["product_model"]." / ".$specDetailStr." / 特殊工艺:".$specialTecDetailStr;
							}
							

						}
						// 每个订单的欠款
						$customerArrear = (float)$value["order_money"]-(float)$value["customer_money"];
						// 计算全部订单总额，总回款，总欠款
						$allMoney = $allMoney+(float)$value["order_money"];
						$allPayment = $allPayment+(float)$value["customer_money"];
						$allArrear = $allArrear+(float)$customerArrear;
						// 整理需要导出的数据
						// dump($customerArrear);
						$exportInfo[$key]["order_id"] = $value["order_id"];
						$exportInfo[$key]["add_time"] = date("m-d H:i",$value["add_time"]);
						$exportInfo[$key]["customer_name"] = $value["customer_name"];
						$exportInfo[$key]["order_money"] = $value["order_money"];
						$exportInfo[$key]["customer_money"] = $value["customer_money"];
						$exportInfo[$key]["customer_arrear"] = $customerArrear;
						$exportInfo[$key]["remark"] = $value["order_remarks"];
						$exportInfo[$key]["product_info"] = $productInfo;
					}
					// dump($productInfo);
					// dump($exportInfo);exit;
					// dump($orderCustomerInfo);
					// dump(count($orderCustomerInfo));
					// 共多少客户
					$num = count($orderCustomerInfo);
					// dump($orderInfo); exit;
					$this->exportOrderHandle($exportInfo,$num,$allMoney,$allPayment,$allArrear);
				}
				
			}

		}

		// 客户对账单导出处理
		public function exportOrderHandle($exportInfo,$num,$allMoney,$allPayment,$allArrear){
			vendor("PHPExcel.PHPExcel");
			$objPHPExcel = new \PHPExcel();
			$objSheet = $objPHPExcel->getActiveSheet();
			$objSheet->setTitle("对账单");
			$objSheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   // 设置excel文件默认水平垂直方向居中
			$objSheet->getDefaultStyle()->getFont()->setName("微软雅黑")->setSize(10);   // 设置默认字体和大小
			$objSheet->mergeCells("A1:J1");
			$objSheet->getStyle("A1")->getFont()->setSize(18)->setBold(True);   // 设置字体大小并加粗
			$objSheet->setCellValue("A1","对账单");

			$objSheet->getStyle("2")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objSheet->mergeCells("A2:J2");
			$objSheet->setCellValue("A2","制作单位： 北京细格广告传媒有限公司");
			$objSheet->getStyle("3")->getAlignment()->setWrapText(true);   // 设置自动换行，需要在输出数据中加换行符"\n"
			$objSheet->getColumnDimension('A')->setWidth("12");   // 设置宽度自适应
			$objSheet->getColumnDimension('B')->setAutoSize(true);
			$objSheet->getColumnDimension('C')->setAutoSize(true);
			$objSheet->getColumnDimension('D')->setAutoSize(true);
			$objSheet->getColumnDimension('E')->setAutoSize(true);
			$objSheet->getColumnDimension('F')->setAutoSize(true);
			$objSheet->getColumnDimension('G')->setAutoSize(true);
			$objSheet->getColumnDimension('H')->setAutoSize(true);
			$objSheet->getColumnDimension('I')->setAutoSize(true);
			$objSheet->getColumnDimension('J')->setWidth("40");
			$objSheet->getStyle('I')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$objSheet->getStyle("A3:J3")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
			$objSheet->setCellValue("A3","订单编号\nId")
					 ->setCellValue("B3","项目说明Content")
					 ->setCellValue("C3","单位\nUnit")
					 ->setCellValue("D3","数量\nNumber")
					 ->setCellValue("E3","单价（元）\nPrice")
					 ->setCellValue("F3","产品总价（元）\nProduct Total")
					 ->setCellValue("G3","订单总价（元）\nOrder Total")
					 ->setCellValue("H3","回款（元）\nPayment")
					 ->setCellValue("I3","欠款（元）\nArrears")
					 ->setCellValue("J3","备注Remark");
			$i = 4;
			$j = 5;
			$orderEnd = 0;
			if($num>1){
				// 多客户导出
				foreach($exportInfo as $key => $value){
					$productNum = sizeof($value["product_info"]);
					$orderEnd = $i+$productNum;
					// dump($productNum);exit;
					$objSheet->getStyle($i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objSheet->getStyle($i)->getFont()->setSize(10)->setBold(True);
					$objSheet->getStyle("A".$i.":J".$i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6B8B7');
					$objSheet->mergeCells("A".$i.":J".$i);
					$objSheet->setCellValue("A".$i,"客户名称：".$value["customer_name"]);
					$objSheet->mergeCells("A".$j.":A".$orderEnd);
					$objSheet->mergeCells("G".$j.":G".$orderEnd);
					$objSheet->mergeCells("H".$j.":H".$orderEnd);
					$objSheet->mergeCells("I".$j.":I".$orderEnd);
					$objSheet->mergeCells("J".$j.":J".$orderEnd);
					// 订单信息
					$objSheet->setCellValue("A".$j,$value["order_id"])
							 ->setCellValue("G".$j,$value["order_money"])
							 ->setCellValue("H".$j,$value["customer_money"])
							 ->setCellValue("I".$j,$value["customer_arrear"])
							 ->setCellValue("J".$j,$value["remark"]);
					// 订单内产品信息
					foreach ($value["product_info"] as $k => $v){
						$objSheet->setCellValue("B".$j,$v["product_detail"])
								 ->setCellValue("C".$j,$v["product_unit"])
								 ->setCellValue("D".$j,$v["num"])
								 ->setCellValue("E".$j,$v["end_price"])
								 ->setCellValue("F".$j,$v["end_money"]);
						$j++;
					}
					$i = $j;
					$j = $i+1;
				}
			}else{
				// 单客户导出
				foreach($exportInfo as $key => $value){
					$productNum = sizeof($value["product_info"]);
					$orderEnd = $j+$productNum-1;
					// dump($productNum);exit;
					$objSheet->getStyle("4")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objSheet->getStyle("4")->getFont()->setSize(10)->setBold(True);
					$objSheet->getStyle("A4:J4")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6B8B7');
					$objSheet->mergeCells("A4:J4");
					$objSheet->setCellValue("A4","客户名称：".$value["customer_name"]);
					$objSheet->mergeCells("A".$j.":A".$orderEnd);
					$objSheet->mergeCells("G".$j.":G".$orderEnd);
					$objSheet->mergeCells("H".$j.":H".$orderEnd);
					$objSheet->mergeCells("I".$j.":I".$orderEnd);
					$objSheet->mergeCells("J".$j.":J".$orderEnd);
					// 订单信息
					$objSheet->setCellValue("A".$j,$value["order_id"])
							 ->setCellValue("G".$j,$value["order_money"])
							 ->setCellValue("H".$j,$value["customer_money"])
							 ->setCellValue("I".$j,$value["customer_arrear"])
							 ->setCellValue("J".$j,$value["remark"]);
					// 订单内产品信息
					foreach ($value["product_info"] as $k => $v){
						$objSheet->setCellValue("B".$j,$v["product_detail"])
								 ->setCellValue("C".$j,$v["product_unit"])
								 ->setCellValue("D".$j,$v["num"])
								 ->setCellValue("E".$j,$v["end_price"])
								 ->setCellValue("F".$j,$v["end_money"]);
						$j++;
					}
				}
				$i = $j;
				$j = $i+1;
			}
			$objSheet->mergeCells("A".$i.":F".$i);
			$objSheet->getStyle("A".$i)->getFont()->setSize(14)->setBold(True);
			$objSheet->getStyle("G".$i.":J".$i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
			$objSheet->setCellValue("A".$i,"合计")
					 ->setCellValue("G".$i,$allMoney)
					 ->setCellValue("H".$i,$allPayment)
					 ->setCellValue("I".$i,$allArrear);
			$objSheet->mergeCells("A".$j.":J".$j);
			$objSheet->getRowDimension($j)->setRowHeight("30");
			$objSheet->getStyle("A".$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objSheet->getStyle("A".$j)->getFont()->setSize(10)->setBold(True);
			$objSheet->setCellValue("A".$j,"备注：");

			// exit;
			// 边框样式
			$borderStyle = array(
				'borders'=>array(
					'allborders'=>array(
						'style' => \PHPExcel_Style_Border::BORDER_THIN,   //系边框
						'color' => array('rgb' => '#000'),
					),
				),
			);
			$objSheet->getStyle("A1:J".$j)->applyFromArray($borderStyle);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="对账单.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}

		public function exportProduct(){

			$this->display("export_product");
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
				$todayDate = str_replace('-','',$today);
				$condition['where'] = "order_id like '".$todayDate."%'";
				$condition['order'] = "id desc";
				$condition['limit']['firstRow'] = 0;
				$condition['limit']['pageSize'] = 1;

				$lastTodayOrderInfo = D("XgOrder")->orderInfo($condition);
				if(!empty($lastTodayOrderInfo)){
					$order_id = $lastTodayOrderInfo['0']['order_id'] + 1;
				}else{
					$todayDate = (int)$todayDate*1000;
					$order_id = $todayDate+1;
				}
				
				//获取客户名称
				$customerInfo = D("XgCustomer")->getCustomerInfo($post['customer_id']);
				$customerName = $customerInfo['cname'];
				//对交货日期进行处理
				$trade_time = strtotime($post['trade_time']);
				//获取联系人姓名
				$customerLinkmanInfo = D("XgCustomerLinkman")->getCustomerLinkInfoByid($post['linkman_name']);
				$linkmanName = $customerLinkmanInfo['name'];

				$data['order_id']= $order_id;
				$data['customer_id'] = $post['customer_id'];
				$data['customer_name'] = $customerName;
				$data['trade_time'] = $trade_time;
				$data['linkman_name'] = $linkmanName;
				$data['linkman_tel'] = $post['linkman_tel'];
				$data['link_address'] = $post['link_address'];
				$data['order_product_id'] = $post['order_product_id'];
				$data['order_money'] = $post['order_money'];
				$data['order_remarks'] = $post['order_remarks'];
				$data['add_time'] = time();
				$data['add_manager_name'] = $_SESSION['userInfo']['truename'];

				$res = D("XgOrder")->addOrderInfo($data);
				//如果新增订单成功，则把该订单的ID 和订单编号存入对应的订单商品信息内
				if($res > 0){
					$order_product_id_arr = explode(",", $post['order_product_id']);
					foreach ($order_product_id_arr as $k => $v) {
						$orderProductData['order_id'] = $res;
						$orderProductData['order_num'] = $order_id;

						$res_2 = D("XgOrderProduct")->updateOrderProductInfo($orderProductData,$v);
					}
				}
				echo $res;

			}
		}
		//新增订单时添加订单商品信息
		public function addOrderProductInfo(){
			$post=$_POST;
			// dump($post);
			$data = array();
			if(!empty($post)){
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
				$data['product_remarks'] = $post['product_remark'];
				$data['add_time'] = time();
				$res = D("XgOrderProduct")->addOrderProductInfo($data);
				echo $res;

			}
		}

		//新增订单时添加订单商品信息
		public function addOrderProductInfo_update(){
			$post=$_POST;
			// dump($post);
			$data = array();
			if(!empty($post)){
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

				$data['order_id'] = $post['order_id'];
				$data['order_num'] = $post['order_num'];
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
				$data['product_remarks'] = $post['product_remark'];
				$data['add_time'] = time();

				// dump($data);

				$res = D("XgOrderProduct")->addOrderProductInfo($data);

				//如果订单产品信息添加成功，则修改订单中的相应信息
				if($res > 0){
					//订单信息处理
					$orderInfo = D("XgOrder")->getOrderInfoById($post['order_id']);

					//订单产品信息
					$condition = "order_id = ".$post['order_id'];
					$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition);

					//订单中的产品id串处理
					if(!empty($orderProductInfo)){
						// $order_product_id_arr = explode(",", $orderInfo['order_product_id']);
						foreach ($orderProductInfo as $k => $v) {
							// if($v != $post['order_product_id'] && $v != ''){
							// 	$order_product_id_arr[] = $v;
							// }
							$order_product_id_arr[] = $v['id'];
						}
						$order_product_id_str = implode(",", $order_product_id_arr);
					}

					//订单的总价格处理
					
					if(!empty($orderProductInfo)){
						foreach ($orderProductInfo as $kk => $vv) {
							$orderMoney = $orderMoney + $vv['end_money'];
						}
					}else{
						$orderMoney = 0;
					}

					//客户付款状态处理
					if($orderInfo['customer_money'] >= $orderMoney){
						$moneyStatus = 3;
					}elseif (($orderInfo['customer_money'] < $orderMoney) && $orderMoney > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderData['order_product_id'] = $order_product_id_str;
					$orderData['order_money'] = $orderMoney;
					$orderData['customer_money_status'] = $moneyStatus;
					$orderData['update_manager_name'] = $_SESSION['userInfo']['truename'];

					$res_2 = D("XgOrder")->updateOrderInfo($orderData,$post['order_id']);
				}
				echo $res;

			}
		}

		// 订单详情页
		public function detail(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

	    	$orderInfo = D("XgOrder")->getOrderInfoById($_GET['id']);
	    	if(!empty($orderInfo)){
	    		//交货日期处理
	    		if(!empty($orderInfo['trade_time'])){
	    			$tradeTime =  date("Y-m-d",$orderInfo['trade_time']);
	    		}else{
	    			$tradeTime = '';
	    		}
				$orderInfo['trade_time_value'] = $tradeTime;

				// //订单产品信息查询
				// if(!empty($orderInfo['order_product_id'])){
				// 	$order_product_id_arr = explode(",", $orderInfo['order_product_id']);
				// 	foreach ($order_product_id_arr as $k => $v) {
				// 		$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($v);
				// 		// dump($orderProductInfo);
				// 		//供应商付款状态处理
				// 		if($orderProductInfo['supplier_money_status'] == 1){
				// 			$orderProductInfo['supplier_money_status_value'] = "未付款";
				// 		}
				// 		if($orderProductInfo['supplier_money_status'] == 2){
				// 			$orderProductInfo['supplier_money_status_value'] = "部分付款";
				// 		}
				// 		if($orderProductInfo['supplier_money_status'] == 3){
				// 			$orderProductInfo['supplier_money_status_value'] = "全额付款";
				// 		}
				// 		//商品规格信息处理
				// 		$productParameterSpecArr = explode(",", $orderProductInfo['product_spec_id_str']);
				// 		if(!empty($productParameterSpecArr)){
				// 			foreach ($productParameterSpecArr as $kk => $vv) {
				// 				$newSpecInfo[] = '';
				// 				$specInfo = D("XgProductSpec")->getSpecById($vv);
				// 				//商品规格名称
				// 				$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
				// 				$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
				// 				$newSpecInfo[$kk][$parameterName['0']['name']] = $specInfo['0'];

				// 				$orderProductInfo['parameter_spec_value'][] = $specInfo['0'];
				// 			}
				// 		}
				// 		$orderInfo['orderProductInfo'][$k] = $orderProductInfo;
				// 		//商品特殊工艺信息处理
				// 		$specialTechnologyhStr = '';
				// 		if(!empty($orderProductInfo['special_technologyh_id_str'])){
				// 			$productSpecialTechnologyhArr = explode(",", $orderProductInfo['special_technologyh_id_str']);
				// 			foreach ($productSpecialTechnologyhArr as $kk => $vv) {
				// 				$specialInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($vv);
				// 				// dump($specialInfo);
				// 				$specialTechnologyhStr .= $specialInfo['name']."&nbsp;&nbsp;&nbsp;&nbsp;";
				// 			}
				// 		}
				// 		$orderInfo['orderProductInfo'][$k]['special_technology_value'] = $specialTechnologyhStr;
				// 	}
				// }

				//
				$orderInfo = $this->dellOrderProductInfo($orderInfo);
				
	    		//订单状态处理
	    		$orderStatusInfo = D("XgOrderStatus")->getOrderStatusInfoById($orderInfo['order_status']);
	    		$orderInfo['order_status_value'] = $orderStatusInfo['status_name'];
				//客户付款状态处理
				if($orderInfo['customer_money_status'] == 1){
					$orderInfo['money_status_value'] = "未回款";
				}
				if($orderInfo['customer_money_status'] == 2){
					$orderInfo['money_status_value'] = "部分回款";
				}
				if($orderInfo['customer_money_status'] == 3){
					$orderInfo['money_status_value'] = "全额回款";
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
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);
			
			$get = $_GET;
			$post = $_POST;
			// dump($get);

			if(!empty($post)){
				// dump($post);

				$id = $post['order_id'];
				//客户回款状态处理
				$orderInfo = D("XgOrder")->getOrderInfoById($post['order_id']);
				if($orderInfo['customer_money'] >= $post['order_money'] ){
					$moneyStatus = 3;
				}elseif (($orderInfo['customer_money'] < $post['order_money'] ) && $orderInfo['customer_money'] > 0 ) {
					$moneyStatus = 2;
				}else{
					$moneyStatus = 1;
				}
				//对交货日期进行处理
				$trade_time = strtotime($post['trade_time']);
				//获取联系人姓名
				$customerLinkmanInfo = D("XgCustomerLinkman")->getCustomerLinkInfoByid($post['linkman_name']);
				$linkmanName = $customerLinkmanInfo['name'];

				$data['trade_time'] = $trade_time;
				$data['linkman_name'] = $linkmanName;
				$data['linkman_tel'] = $post['linkman_tel'];
				$data['link_address'] = $post['link_address'];
				$data['order_money'] = $post['order_money'];
				$data['customer_money_status'] = $moneyStatus;
				$data['order_status'] = $post['order_status'];
				$data['order_remarks'] = $post['order_remarks'];
				$data['update_manager_name'] = $_SESSION['userInfo']['truename'];

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
					//订单产品信息处理
					$orderInfo = $this->dellOrderProductInfo($orderInfo);

					//客户付款状态处理
					if($orderInfo['customer_money_status'] == 1){
						$orderInfo['money_status_value'] = "未回款";
					}
					if($orderInfo['customer_money_status'] == 2){
						$orderInfo['money_status_value'] = "部分回款";
					}
					if($orderInfo['customer_money_status'] == 3){
						$orderInfo['money_status_value'] = "全额回款"; 
					}
					//录入时间处理
					$insertTime =  date("Y-m-d H:i:s",$orderInfo['add_time']);
					$orderInfo['insert_time'] = $insertTime;
					
		    	}

		    	//订单状态下拉框数组
				$orderStatusArr = D("XgOrderStatus")->getOrderStatusInfo();
				//付款状态下拉框数组
				$moneyStatusArr = array(
						"未回款" => 1,
						"部分回款" => 2,
						"全额回款" => 3,
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
			$res = D("XgOrder")->deleteOrderInfoById($post['id']);

			// $data['order_status'] = 4; 
			// $res = D("XgOrder")->updateOrderInfo($data,$post['id']);

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
			$html = '<option value="0">请选择产品名称</option>';
			if(!empty($productNameInfo)){
				foreach($productNameInfo as $k=>$v){
					// $html .= " <option value='".$v['id']."' onclick='getProductModel(".$v['id'].")'>".$v['type_name']."</option>";
					$html .= " <option value='".$v['id']."' >".$v['type_name']."</option>";
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
			$html = "<option value='0'>请选择联系人</option>";
			if(!empty($customerLxrArr)){
				foreach ($customerLxrArr as $k=>$v){
					$html.= "<option value='".$v['id']."' class='lxr_each'>".$v['name']."</option>";
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
			$html = "<option value='0' >请选择产品名称</option>";
			if(!empty($productTypeFirst)){
				foreach($productTypeFirst as $k=>$v){
					$html .= " <option value='".$v['id']."' >".$v['type_name']."</option>";
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

			//根据ID查询 产品名称信息
			$productNameInfo = D("XgProductType")->getProduct($id);
			if($productNameInfo['0']['special_tec_str'] != ''){
				$special_tec_value = 2;
			}else{
				$special_tec_value = 1;
			}

			$html .= "<input type='hidden' value='".$special_tec_value."' id='hide_have_price_value' />";
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
			// dump($selectSupplierInfo);
			if(!empty($selectSupplierInfo)){
				foreach ($selectSupplierInfo as $kk => $vv) {
					if($vv['price'] == ''){
						$selectSupplierInfo[$kk]['price'] = 0;
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

			$productHistoryOrderInfo = D("XgOrderProduct")->getOrderProductInfo($condition);
			if(!empty($productHistoryOrderInfo)){
				foreach ($productHistoryOrderInfo as $kk => $vv) {
					$thisOrderInfo = D("XgOrder")->getOrderInfoById($vv['order_id']); 
					$productHistoryOrderInfo[$kk]['customer_name'] = $thisOrderInfo['customer_name'];
				}
			}
			//查询历史报价信息
			$productHistoryQuoteInfo = D("XgQuote")->getProductQuoteInfo($condition);

			// //计算草考价格
			// if(empty($post['customer_level'])){
			// 	$customerInfo = D("XgCustomer")->getCustomerInfo($post['customer_id']);
			// 	if(!empty($customerInfo)){
			// 		$customerLevel = D("XgCustomer")->getLevelInfoByLevel($customerInfo['rank']);
			// 		$levelDiscount = $customerLevel['level_discount'];
					
			// 		$discountMoneyVal = $levelDiscount*($post['product_num']*$post['product_cost_price']);
			// 		$discountMoney['money'] = $discountMoneyVal;
			// 		$discountMoney['money_1'] = "￥ ";
			// 		$discountMoney['money_2'] = " 元";
			// 	}else{
			// 		$discountMoney['money'] = "请输入该产品的成本单价！";
			// 		$discountMoney['money_1'] = " ";
			// 		$discountMoney['money_2'] = " ";
			// 	}
			// }else{
			// 	//根据客户等级ID获取等级信息 
			// 	$customerLevelInfo = D("XgCustomerLevel")->getLevelInfoById($post['customer_level']);
			// 	$discountMoneyVal = $customerLevelInfo['level_discount']*($post['product_num']*$post['product_cost_price']);
			// 	$discountMoney['money'] = $discountMoneyVal;
			// 	$discountMoney['money_1'] = "￥ ";
			// 	$discountMoney['money_2'] = " 元";
			// }

			$this->assign("productHistoryOrderInfo",$productHistoryOrderInfo);
			$this->assign("productHistoryQuoteInfo",$productHistoryQuoteInfo);
			// $this->assign("discountMoney",$discountMoney);
			$this->display("common_order_refer_info");
		}

		//商品参考报价信息
		public function getOrderReferInfoMoney(){
			$post=$_POST;
			//计算草考价格
			if(empty($post['customer_level'])){
				$customerInfo = D("XgCustomer")->getCustomerInfo($post['customer_id']);
				// dump($customerInfo);
				if(!empty($customerInfo)){
					$customerLevel = D("XgCustomerLevel")->getLevelInfoById($customerInfo['level_id']);
					// dump($customerLevel);
					$levelDiscount = $customerLevel['level_price'];
					
					$discountMoneyVal = $levelDiscount*($post['product_num']*$post['product_cost_price']);
					$discountMoney['money'] = $discountMoneyVal;
					$discountMoney['money_1'] = "￥ ";
					$discountMoney['money_2'] = " 元";
				}else{
					$discountMoney['money'] = "请选择客户信息！";
					$discountMoney['money_1'] = " ";
					$discountMoney['money_2'] = " ";
				}
			}else{
				//根据客户等级ID获取等级信息 
				$customerLevelInfo = D("XgCustomerLevel")->getLevelInfoById($post['customer_level']);
				$discountMoneyVal = $customerLevelInfo['level_price']*($post['product_num']*$post['product_cost_price']);
				$discountMoney['money'] = $discountMoneyVal;
				$discountMoney['money_1'] = "￥ ";
				$discountMoney['money_2'] = " 元";
			}

			$this->assign("discountMoney",$discountMoney);
			$this->display("common_order_refer_info_money");
		}
		//根据分类ID判断该分类是否有特殊工艺
		public function getProductSpecialTechnology($id){
			$productTypeInfo = D("XgProductType")->getProduct($id);
			if($productTypeInfo['0']['special_tec_str'] != '' ){
				$special_tec_arr = explode(",", $productTypeInfo['0']['special_tec_str']);
				foreach ($special_tec_arr as $k => $v) {
					$specialTechnologyInfo[] = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($v);
				}
				// $specialTechnologyInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnology();
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
				$data['manager_name'] = $_SESSION['userInfo']['truename'];
				$data['add_time'] = time();

				$res = D("XgCustomerAccount")->addCusromerAccountInfo($data);
				//如果数据添加成功，则对订单中的客户回款数据及回款状态进行修改
				if($res > 0){
					//
					$customerMoney = $orderInfo['customer_money'] + $post['money'];
					if($customerMoney >= $orderInfo['order_money']){
						$moneyStatus = 3;
					}elseif (($customerMoney < $orderInfo['order_money']) && $customerMoney > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderData['customer_money_status'] = $moneyStatus;
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

				if($customerMoney >= $orderInfo['order_money']){
					$moneyStatus = 3;
				}elseif (($customerMoney < $orderInfo['order_money']) && $customerMoney > 0 ) {
					$moneyStatus = 2;
				}else{
					$moneyStatus = 1;
				}

				$orderData['customer_money_status'] = $moneyStatus;
				$orderData['customer_money'] = $customerMoney;

				$res2 = D("XgOrder")->updateOrderInfo($orderData,$oldCustomerAccount['order_id']);

				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);
		}
		//新增订单时添加商品信息弹窗页面
		public function addProductInfo(){
			$productTypeModel = D("XgProductType");
			$productTypeFirst = $productTypeModel->getProductTypeByPid(0);
			//全部产品名称
			$condition['where'] = "pid > 0";
			$productNameFirst = D("XgProductType")->getProductInfo($condition);
			
			$this->assign("productTypeFirst",$productTypeFirst);
			$this->assign("productNameFirst",$productNameFirst);
			$this->assign("get",$_GET);
			$this->display("add_product_info");
		}
		//修改订单时添加商品信息弹窗页面
		public function addProductInfo_update(){
			$productTypeModel = D("XgProductType");
			$productTypeFirst = $productTypeModel->getProductTypeByPid(0);
			//全部产品名称
			$condition['where'] = "pid > 0";
			$productNameFirst = D("XgProductType")->getProductInfo($condition);
			
			$this->assign("productTypeFirst",$productTypeFirst);
			$this->assign("productNameFirst",$productNameFirst);
			$this->assign("get",$_GET);
			$this->display("add_product_info_update");
		}
		//订单页面计算订单总价
		public function getOrderAllMoney(){
			$post = $_POST;
			$allMoney = 0;
			if(!empty($post)){
				$order_product_id_arr = explode(",", $post['order_product_id']);
				foreach ($order_product_id_arr as $k => $v) {
					$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($v);
					$allMoney = $allMoney+$orderProductInfo['end_money'];

				}
			}

			echo $allMoney;
		}
		//搜索获取客户信息
		public function searchCustomerInfo(){
			$post = $_POST;
			$condition['where'] = "cname like '%".$post['search_value']."%'";
			$customerInfo = D("XgCustomer")->getCustomerInfos($condition);
			// dump($customerInfo);

			$html = "";
			if(!empty($customerInfo)){
				foreach($customerInfo as $k=>$v){
					$html .= "<li onclick=searchCustomerName('".$v['cname']."')>".$v['cname']."</li>";
				}
			}
			echo $html;

		}
		//订单产品信息处理
		public function dellOrderProductInfo($orderInfo){
			//订单产品信息查询
			if(!empty($orderInfo['order_product_id'])){
				$order_product_id_arr = explode(",", $orderInfo['order_product_id']);
				foreach ($order_product_id_arr as $k => $v) {
					if($v != ''){
						$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($v);
						//产品单位名称处理
						$prductNameInfo = D("XgProductType")->getProduct($orderProductInfo['product_name_id']);
						$orderProductInfo['product_unit'] = $prductNameInfo['0']['product_unit'];
						//供应商付款状态处理
						if($orderProductInfo['supplier_money_status'] == 1){
							$orderProductInfo['supplier_money_status_value'] = "未付款";
						}
						if($orderProductInfo['supplier_money_status'] == 2){
							$orderProductInfo['supplier_money_status_value'] = "部分付款";
						}
						if($orderProductInfo['supplier_money_status'] == 3){
							$orderProductInfo['supplier_money_status_value'] = "全额付款";
						}
						//商品规格信息处理
						$productParameterSpecArr = explode(",", $orderProductInfo['product_spec_id_str']);
						if(!empty($productParameterSpecArr)){
							foreach ($productParameterSpecArr as $kk => $vv) {
								$newSpecInfo[] = '';
								$specInfo = D("XgProductSpec")->getSpecById($vv);
								//商品规格名称
								$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
								$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
								$newSpecInfo[$kk][$parameterName['0']['name']] = $specInfo['0'];

								$orderProductInfo['parameter_spec_value'][] = $specInfo['0'];
							}
						}
						$orderInfo['orderProductInfo'][$k] = $orderProductInfo;
					
						
						//商品特殊工艺信息处理
						$specialTechnologyhStr = '';
						if(!empty($orderProductInfo['special_technologyh_id_str'])){
							$productSpecialTechnologyhArr = explode(",", $orderProductInfo['special_technologyh_id_str']);
							foreach ($productSpecialTechnologyhArr as $kk => $vv) {
								$specialInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($vv);
								// dump($specialInfo);
								$specialTechnologyhStr .= $specialInfo['name']."&nbsp;&nbsp;&nbsp;&nbsp;";
							}
						}
						$orderInfo['orderProductInfo'][$k]['special_technology_value'] = $specialTechnologyhStr;
					}
				}
			}
			// dump($orderInfo);

			return $orderInfo;

		}
		//修改订单产品信息
		public function updateOrderProductInfo(){
			$post = $_POST;
			if(!empty($post)){
				$data['cost_money'] = $post['cost_money'];
				$data['end_price'] = $post['end_price'];
				$data['end_money'] = $post['end_money'];
				$data['material_link'] = $post['material_link'];
				$data['product_remarks'] = $post['product_remarks'];

				$res = D("XgOrderProduct")->updateOrderProductInfo($data,$post['id']);
				if($res > 0){
					//订单信息处理
					$orderInfo = D("XgOrder")->getOrderInfoById($post['order_id']);

					//订单的总价格处理
					//订单产品信息
					$condition = "order_id = ".$post['order_id'];
					$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition);
					if(!empty($orderProductInfo)){
						foreach ($orderProductInfo as $kk => $vv) {
							$orderMoney = $orderMoney + $vv['end_money'];
						}
					}else{
						$orderMoney = 0;
					}

					//客户付款状态处理
					if($orderInfo['customer_money']>= $orderMoney ){
						$moneyStatus = 3;
					}elseif (($orderInfo['customer_money'] < $orderMoney ) && $orderInfo['customer_money'] > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderData['order_money'] = $orderMoney;
					$orderData['customer_money_status'] = $moneyStatus;
					$orderData['update_manager_name'] = $_SESSION['userInfo']['truename'];

					$res_2 = D("XgOrder")->updateOrderInfo($orderData,$post['order_id']);
				}
				echo $res;
			}
		}

		//订单产品中向供应商付款信息记录
		public function supplierMoneyInfo(){
			$get = $_GET;
			$supplierMoneyInfo = D("XgSupplierAccount")->getSupplierAccountInfo($get['id']);

			if(!empty($supplierMoneyInfo)){
				foreach ($supplierMoneyInfo as $k => $v) {
					//录入时间处理
					$insertTime =  date("Y-m-d H:i:s",$v['add_time']);
					$supplierMoneyInfo[$k]['insert_time'] = $insertTime;
				}
			}

			$this->assign("supplierMoneyInfo",$supplierMoneyInfo); 
			$this->assign("get",$get);
			$this->display("supplier_money_info");
		}

		//新增订单产品的 向供应商付款 记录
		public function addSupplierMoney(){
			
			//订单产品 向供应商付款记录
			$get = $_GET;

			$this->assign("get",$get);
			$this->display("add_supplier_money");
		}
		
		public function addSupplierMoneyInfo(){
			$post = $_POST;
			//根据订单ID查询出订单信息，取需要的数据如表
			$orderInfo = D("XgOrder")->getOrderInfoById($post['order_id']);
			//根据订单产品ID查询出订单产品信息，取需要的数据如表
			$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($post['order_product_id']);

			if(!empty($orderInfo)){
				$data['order_id'] = $post['order_id'];
				$data['order_num'] = $orderInfo['order_id'];
				$data['order_product_id'] = $post['order_product_id'];
				$data['order_product_name'] = $orderProductInfo['product_name'];
				$data['supplier_id'] = $orderProductInfo['supplier_id'];
				$data['supplier_name'] = $orderProductInfo['supplier_name'];
				$data['money'] = $post['money'];
				$data['remark'] = $post['remark'];
				$data['manager_name'] = $_SESSION['userInfo']['truename'];
				$data['add_time'] = time();

				$res = D("XgSupplierAccount")->addSupplierAccountInfo($data);
				//如果数据添加成功，则对订单产品的 向供应商付款数据及回款状态进行修改
				if($res > 0){
					//
					$supplierMoney = $orderProductInfo['supplier_money'] + $post['money'];
					if($supplierMoney >= $orderProductInfo['end_money']){
						$moneyStatus = 3;
					}elseif (($supplierMoney < $orderProductInfo['end_money']) && $supplierMoney > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderProductData['supplier_money_status'] = $moneyStatus;
					$orderProductData['supplier_money'] = $supplierMoney;

					$res2 = D("XgOrderProduct")->updateOrderProductInfo($orderProductData,$post['order_product_id']);
				}
				echo $res;
			}
		}

		//修改向供应商付款信息
		public function updateSupplierMoneyInfo(){
			$get = $_GET;
			$post = $_POST;

			if(!empty($post)){
				//向供应商付款信息中的原信息
				$oldSupplierAccount = D("XgSupplierAccount")->getSupplierAccountById($post['id']);

				$data['money'] = $post['money'];
				$data['remark'] = $post['remark'];
				// dump($data);

				$res = D("XgSupplierAccount")->updateSupplierAccountInfo($data,$post['id']);
				//如果修改成功，则对订单产品中的 向供应商付款 数据及回款状态进行修改
				if($res > 0 ){
					//根据订单产品ID查询出订单产品信息，取需要的数据如表
					$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($oldSupplierAccount['order_product_id']);
					//订单产品中对应的 向供应商的所有的付款金额
					$allSupplierAccount = D('XgSupplierAccount')->getSupplierAccountInfo($oldSupplierAccount['order_product_id']);
					// dump($allSupplierAccount); 
					$supplierMoney = 0;
					if(!empty($allSupplierAccount)){
						foreach ($allSupplierAccount as $kk => $vv) {
							$supplierMoney = $supplierMoney + $vv['money'];
						}
					}

					if($supplierMoney >= $orderProductInfo['end_money']){
						$moneyStatus = 3;
					}elseif (($supplierMoney < $orderProductInfo['end_money']) && $supplierMoney > 0 ) {
						$moneyStatus = 2;
					}else{
						$moneyStatus = 1;
					}

					$orderProductData['supplier_money_status'] = $moneyStatus;
					$orderProductData['supplier_money'] = $supplierMoney;

					$res2 = D("XgOrderProduct")->updateOrderProductInfo($orderProductData,$oldSupplierAccount['order_product_id']);
				}
				echo $res;

			}else{
				$supplierMoneyInfo = D("XgSupplierAccount")->getSupplierAccountById($get['id']);

				$this->assign("supplierMoneyInfo",$supplierMoneyInfo); 
				$this->assign("get",$get);
				$this->display("update_supplier_money_info");
			}
		}

		//删除向 供应商付款 的记录
		public function deleteSupplierAccountInfo(){
			$post = $_POST;
			//向供应商付款信息中的原信息
			$oldSupplierAccount = D("XgSupplierAccount")->getSupplierAccountById($post['id']);

			$res = D("XgSupplierAccount")->deleteSupplierAccountInfoById($post['id']);

			if($res > 0){
				//根据订单产品ID查询出订单产品信息，取需要的数据如表
				$orderProductInfo = D("XgOrderProduct")->getOrderProductInfoById($oldSupplierAccount['order_product_id']);
				//订单产品中对应的 向供应商的所有的付款金额
				$allSupplierAccount = D('XgSupplierAccount')->getSupplierAccountInfo($oldSupplierAccount['order_product_id']);
				$supplierMoney = 0;
				if(!empty($allSupplierAccount)){
					foreach ($allSupplierAccount as $kk => $vv) {
						$supplierMoney = $supplierMoney + $vv['money'];
					}
				}

				if($supplierMoney >= $orderProductInfo['end_money']){
					$moneyStatus = 3;
				}elseif (($supplierMoney < $orderProductInfo['end_money']) && $supplierMoney > 0 ) {
					$moneyStatus = 2;
				}else{
					$moneyStatus = 1;
				}

				$orderProductData['supplier_money_status'] = $moneyStatus;
				$orderProductData['supplier_money'] = $supplierMoney;

				$res2 = D("XgOrderProduct")->updateOrderProductInfo($orderProductData,$oldSupplierAccount['order_product_id']);

				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);
		}

		//删除订单中的产品信息
		public function deleteOrderProduct(){

			$post = $_POST;
			$res = D("XgOrderProduct")->deleteOrderProductInfoById($post['order_product_id']);
			// dump($res);
			
			//如果删除成功，则
			if($res > 0){
				//删除对应商品的向供应商付款记录
				$res_1 = D("XgSupplierAccount")->deleteSupplierAccountInfoByOrderProductId($post['order_product_id']);
				// dump($res_1);


				//订单信息处理
				$orderInfo = D("XgOrder")->getOrderInfoById($post['order_id']);
				//订单中的产品id串处理
				if(!empty($orderInfo['order_product_id'])){
					$order_product_id_arr = explode(",", $orderInfo['order_product_id']);
					// $new_order_product_id_arr[] = '';
					foreach ($order_product_id_arr as $k => $v) {
						if($v != $post['order_product_id']){
							$new_order_product_id_arr[] = $v;
						}
					}
					$order_product_id_str = implode(",", $new_order_product_id_arr);
				}

				//订单的总价格处理
				//订单产品信息
				$condition = "order_id = ".$post['order_id'];
				$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition);
				if(!empty($orderProductInfo)){
					foreach ($orderProductInfo as $kk => $vv) {
						$orderMoney = $orderMoney + $vv['end_money'];
					}
				}else{
					$orderMoney = 0;
				}

				//客户付款状态处理
				if($orderInfo['customer_money']>= $orderMoney ){
					$moneyStatus = 3;
				}elseif (($orderInfo['customer_money'] < $orderMoney ) && $orderInfo['customer_money'] > 0 ) {
					$moneyStatus = 2;
				}else{
					$moneyStatus = 1;
				}

				$orderData['order_product_id'] = $order_product_id_str;
				$orderData['order_money'] = $orderMoney;
				$orderData['customer_money_status'] = $moneyStatus;
				$orderData['update_manager_name'] = $_SESSION['userInfo']['truename'];

				$res_2 = D("XgOrder")->updateOrderInfo($orderData,$post['order_id']);
				// dump($res_2);

				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str);

		}
		//修改订单时计算订单总价格
		public function getOrderAllMoneyUpdate(){
			$condition = "order_id = ".$_POST['order_id'];
			$orderProductInfo = D("XgOrderProduct")->getOrderProductInfo($condition);
			$orderMoney = 0;
			if(!empty($orderProductInfo)){
				foreach ($orderProductInfo as $k => $v) {
					$orderMoney = $orderMoney + $v['end_money'];
				}
			}

			echo $orderMoney;
		}
		//修改订单信息
		public function updateOrder(){

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
					// dump($get);
					$dateTimeArr = explode("To", $get['search_date_value']);
					$startTime = strtotime($dateTimeArr['0']);
					$endTime = (strtotime($dateTimeArr['1'])) + 86400;

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
					}else if($get['input_type_value'] == 'product'){
						$whereArr[] = "product_name like '%".$get['search_value']."%'";
					}else{
						$whereArr[] = "manager_name like '%".$get['search_value']."%'";
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
	 		$pageSize = 15;
	 		//实例化分页类
	 		$page = new \Think\Page($count,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		// 设置显示页码个数
	 		$page->rollPage = 5;
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
		// 报价详情
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
				//产品单位处理 
				$productInfo = D("XgProductType")->getProduct($quoteInfo['product_name_id']);
				$quoteInfo['product_unit'] = $productInfo['0']['product_unit'];
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
		//添加报价记录信息处理
		public function addQuoteInfo(){
			$post=$_POST;
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
				$data['cost_price'] = $post['cost_price'];
				$data['discount_money'] = $post['discount_money'];
				$data['end_price'] = $post['end_price'];
				$data['end_money'] = $post['end_money'];
				$data['add_time'] = time();
				$data['manager_name'] = $_SESSION['userInfo']['truename'];
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
				$data['end_price'] = $post['end_price'];
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
					//产品单位处理 
					$productInfo = D("XgProductType")->getProduct($quoteInfo['product_name_id']);
					$quoteInfo['product_unit'] = $productInfo['0']['product_unit'];
					
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
		// 获取需要导出的数据
		public function exportQuote(){
			$get = $_GET;
			$date = $get['search_date_value'];
			$searchCondition = $get['input_type_value'];
			$searchValue = $get['search_value'];
			if(!empty($get)){
				if($date=='' && $searchCondition=='' && $searchValue==''){
					$quoteInfo = D("XgQuote")->getAllQuote();
				}else{
					//如果有时间
					if($get['search_date_value'] ){
						// dump($get);
						$dateTimeArr = explode("To", $get['search_date_value']);
						$startTime = strtotime($dateTimeArr['0']);
						$endTime = (strtotime($dateTimeArr['1'])) + 86400;

						$whereArr[] = "add_time >= '".$startTime."' and add_time < '".$endTime."'";
					}else{
						$get['search_date_value'] = '';
					}
					//如果有输入值搜索
					if($get['search_value']){
						if($get['input_type_value'] == 'customer'){
							$whereArr[] = "customer_name like '%".$get['search_value']."%'";
						}else if($get['input_type_value'] == 'supplier'){
							$whereArr[] = "supplier_name like '%".$get['search_value']."%'";
						}else if($get['input_type_value'] == 'product'){
							$whereArr[] = "product_name like '%".$get['search_value']."%'";
						}else{
							$whereArr[] = "manager_name like '%".$get['search_value']."%'";
						}
					}else{
						$get['search_value'] = '';
					}
					$where = implode(' and ',$whereArr);
					// dump($where);
					$condition['where'] = $where;
					$quoteInfo = D("XgQuote")->quoteInfo($condition);
					// dump($quoteInfo);exit;
				}
				if($quoteInfo){
					foreach ($quoteInfo as $k => $v) {
						// 获取产品规格信息
						$specIdArr = explode(",", $v["product_spec_id_str"]);
						if(!empty($specIdArr)){
							$specDetail = '';
							foreach ($specIdArr as $key => $value) {
								$specInfo = D("XgProductSpec")->getSpecById($value);
								//商品规格名称
								$parameterName = D("XgProductParameter")->getProductParameterById($specInfo['0']['parameter_id']);
								$specInfo['0']['parameter_name'] = $parameterName['0']['name'];
								$specDetail[] = $parameterName['0']['name'].":".$specInfo['0']['spec_value'];
							}
							$specDetailStr = implode(" / ", $specDetail);
							$quoteInfo[$k]["specDetail"] = $specDetailStr;
						}
						// 获取产品单位
						$productInfo = D("XgProductType")->getProduct($v['product_name_id']);
						$productUnit = $productInfo["0"]["product_unit"];
						// 获取产品特殊工艺名称
						$specialTecIdArr = explode(",", $v['special_technologyh_id_str']);
						$ArrLength = sizeof($specialTecIdArr);
						$specialTecName = "";
						for ($i=0; $i < $ArrLength; $i++) { 
							$specialTecArr = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($specialTecIdArr[$i]);
							$specialTecName[] = $specialTecArr['name'];
						}
						$specialTecInfo = implode(",", $specialTecName);
						// 整理需要导出的数据
						$month = date("Y-m",$v['add_time']);
						$exportInfo[$month][$k]['time'] = date("m-d H:i",$v['add_time']);
						$exportInfo[$month][$k]['customer_name'] = $v['customer_name'];
						if($specialTecInfo == ""){
							$exportInfo[$month][$k]['product_info'] = $v['product_name']." / ".$v['product_model']." / ".$specDetailStr;
						}else{
							$exportInfo[$month][$k]['product_info'] = $v['product_name']." / ".$v['product_model']." / ".$specDetailStr." / 特殊工艺:".$specialTecInfo;
						}
						$exportInfo[$month][$k]['product_unit'] = $productUnit;
						$exportInfo[$month][$k]['num'] = $v['num'];
						$exportInfo[$month][$k]['end_price'] = $v['end_price'];
						$exportInfo[$month][$k]['end_money'] = $v['end_money'];
						$exportInfo[$month][$k]['linkman_name'] = $v['linkman_name'];
						$exportInfo[$month][$k]['linkman_tel'] = $v['linkman_tel'];
						$exportInfo[$month][$k]['manager_name'] = $v['manager_name'];
					}
					$this->exportHandle($exportInfo);
				}
			}
		}
		// 导出处理
		public function exportHandle($exportInfo){
			// ob_end_clean();
			// ob_start();
			// import("Org.Util.PHPExcel");
			// import("Org.Util.PHPExcel.IOFactory",'','.php');
	  		// import("Org.Util.PHPExcel.Writer.Excel5",'','.php');
	  		// import("Org.Util.PHPExcel.Writer.Excel2007",'','.php');
			vendor("PHPExcel.PHPExcel");
			// vendor("PHPExcel.PHPExcel.Writer.Excel5");
			// vendor("PHPExcel.PHPExcel.IOFactory.php");
			$objPHPExcel = new \PHPExcel();
			$objSheet = $objPHPExcel->getActiveSheet();
			$objSheet->setTitle("报价记录信息");
			$objSheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   // 设置excel文件默认水平垂直方向居中
			$objSheet->getDefaultStyle()->getFont()->setName("微软雅黑")->setSize(10);   // 设置默认字体和大小
			$objSheet->mergeCells("A1:K1");
			$objSheet->getStyle("A1")->getFont()->setSize(18)->setBold(True);   // 设置字体大小并加粗
			$objSheet->setCellValue("A1","报价记录信息汇总");

			$objSheet->getStyle("2")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objSheet->mergeCells("A2:K2");
			$objSheet->setCellValue("A2","制作单位： 北京细格广告传媒有限公司");
			$objSheet->getStyle("3")->getAlignment()->setWrapText(true);   // 设置自动换行，需要在输出数据中加换行符"\n"
			$objSheet->getColumnDimension('A')->setAutoSize(true);   // 设置宽度自适应
			$objSheet->getColumnDimension('B')->setWidth("30");
			$objSheet->getColumnDimension('C')->setAutoSize(true);
			$objSheet->getColumnDimension('D')->setAutoSize(true);
			$objSheet->getColumnDimension('E')->setAutoSize(true);
			$objSheet->getColumnDimension('F')->setAutoSize(true);
			$objSheet->getColumnDimension('G')->setAutoSize(true);
			$objSheet->getColumnDimension('H')->setAutoSize(true);
			$objSheet->getColumnDimension('I')->setAutoSize(true);
			$objSheet->getColumnDimension('J')->setAutoSize(true);
			$objSheet->getColumnDimension('K')->setWidth("40");
			$objSheet->getStyle('I')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$objSheet->getStyle("A3:K3")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
			$objSheet->setCellValue("A3","日期Date")
					 ->setCellValue("B3","客户名称Name")
					 ->setCellValue("C3","项目说明Content")
					 ->setCellValue("D3","单位\nUnit")
					 ->setCellValue("E3","数量\nNumber")
					 ->setCellValue("F3","单价（元）\nPrice")
					 ->setCellValue("G3","总价（元）\nTotal")
					 ->setCellValue("H3","联系人\nContact")
					 ->setCellValue("I3","联系方式\nTel")
					 ->setCellValue("J3","录入人\nEditor")
					 ->setCellValue("K3","备注Remark");
			$i = 4;
			$j = 5;
			foreach($exportInfo as $key => $value){
				$n = sizeof($exportInfo[$key]);
				// dump($value);
				$objSheet->getStyle($i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objSheet->getStyle($i)->getFont()->setSize(10)->setBold(True);
				$objSheet->getStyle("A".$i.":K".$i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6B8B7');
				$objSheet->mergeCells("A".$i.":K".$i);
				$objSheet->setCellValue("A".$i,$key);
				foreach ($value as $k => $v) {
					$objSheet->setCellValue("A".$j,$v["time"])
							 ->setCellValue("B".$j,$v["customer_name"])
							 ->setCellValue("C".$j,$v["product_info"])
							 ->setCellValue("D".$j,$v["product_unit"])
							 ->setCellValue("E".$j,$v["num"])
							 ->setCellValue("F".$j,$v["end_price"])
							 ->setCellValue("G".$j,$v["end_money"])
							 ->setCellValue("H".$j,$v["linkman_name"])
							 ->setCellValue("I".$j,$v["linkman_tel"])
							 ->setCellValue("J".$j,$v["manager_name"])
							 ->setCellValue("K".$j," ");
					$j++;
				}
				$i = $j;
				$j = $i+1;
			}
			$end = $i-1;  //最后一行
			// 边框样式
			$borderStyle = array(
				'borders'=>array(
					'allborders'=>array(
						'style' => \PHPExcel_Style_Border::BORDER_THIN,   //系边框
						'color' => array('rgb' => '#000'),
					),
				),
			);
			$objSheet->getStyle("A1:K".$end)->applyFromArray($borderStyle);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="geigemianzi.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			// $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
			$objWriter->save('php://output');
			exit;
			
			// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			// header('Content-Disposition: attachment;filename="报价记录统计.xlsx"');   // 设置输出文件的名称
			// header('Cache-Control: max-age=0');   // 禁止缓存
			// $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,"Excel2007");
			// $objWriter->save("php://output");

			// $name='example_export.xlsx';
			// header('Content-Type: application/vnd.ms-excel');
			// header('Content-Disposition: attachment; filename='.$name);
			// header('Cache-Control: max-age=0');
			// import("Org.Util.PHPExcel.IOFactory");
			// $ExcelWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			// $ExcelWriter->save('php://output');
		}



		//获取产品名字并返回给页面
		public function getProductNameOne(){
			$productInfo = D("XgProductType")->getProductName($_POST['id']);

			echo $productInfo['0']['type_name'];
		}







		



	}