<?php
	namespace Home\Controller;
	
	
	class SupplierController extends BaseController{
		// 供应商基本信息展示
		public function index(){
			// 左侧菜单
			$productType = $this->menu();
			$this->assign("productType",$productType);

			$get = $_GET;

			$condition = array();
			if(!empty($get)){
				//如果有省份
				if($get['province'] ){
					$whereArr[] = "supplier_pro_id = '".$get['province']."'";
				}
				//如果有城市
				if($get['city'] ){
					$whereArr[] = "supplier_city_id = '".$get['city']."'";
				}
				//如果有输入值搜索
				if($get['search_value']){
					if($get['input_type_value'] == 'phone'){
						//根据电话查询供应商联系人信息
						$linkmanInfo = D("XgSupplierLinkman")->getLinkmanInfoByPhone($get['search_value']);
						$whereArr[] = "supplier_id = ".$linkmanInfo['supplier_id']."";
					}else if($get['input_type_value'] == 'linkman'){
						//根据电话查询供应商联系人信息
						$linkmanInfo = D("XgSupplierLinkman")->getLinkmanInfoByName($get['search_value']);
						$whereArr[] = "supplier_id = ".$linkmanInfo['supplier_id']."";
					}else{
						$whereArr[] = "supplier_name like '%".$get['search_value']."%'";
					}
				}else{
					$get['search_value'] = '';
				}

				$where = implode(' and ',$whereArr);
				$condition['where'] = $where;
			}
			
			//订单信息中符合条件的总记录数
			$count = D("XgSupplier")->getSupplierCount($condition);
			// dump($count);
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
	 		$condition['order'] = "supplier_id desc";
	 		$condition['limit']['firstRow'] = $firstRow;
	 		$condition['limit']['pageSize'] = $pageSize;

			$supplierInfo = D("XgSupplier")->supplierInfo($condition);

			if(!empty($supplierInfo)){
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
					// 获取可做产品
					$productIdArr = explode(",", $v['product_id_str']);
					// $product[] = "";
					foreach ($productIdArr as $ke => $pi) {
						$id = $pi;
						$product = D("XgProductType")->getProductName($id);
						$supplierInfo[$k]["type_name_arr"][] = $product[0];
					}

				}
			}

			//省份信息
			$province = $this->getProvince("0");
			//城市信息
			if(empty($get['province'])){
				$city = $this->getCity("0");
			}else{
				$city = M("xg_city")->where("provincecode = ".$get['province'] )->select();;
			}


			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("active","active");
			$this->assign("province",$province);
			$this->assign("city",$city);
			$this->assign("get",$get);
			$this -> display();
		}

		//添加供应商信息
		public function addSupplier(){
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


			//获取省份信息
			$province = $this->getProvince("0");
			$this->assign("province",$province);
			//获取城市信息
			$city =M("xg_city")->select();
			$this->assign("city",$city);
			//获取地区信息
			$area =M("xg_area")->select();
			$this->assign("area",$area);
			// 获取产品信息
			$productTypeModel = D("XgProductType");
			$productType = $productTypeModel->getProductType();
			foreach ($productType as $k => $v) {
	    		$pid = $v["id"];
	    		$productMenu = $productTypeModel->getProductTypeByPid($pid);
	    		// dump($productMenu);
	    		$productType[$k]["productMenu"] = $productMenu;
	    	}
	    	// dump($productType);
	    	$this->assign("productType",$productType);
			//接收post传值
			$post = $_POST;
			if(!empty($_POST)){
				// dump(sizeof($post["checkbox"]));
				// dump($post);exit;

				// dump($post['checkbox']);exit;
				// 添加供应商基本信息
				$supplier['supplier_name'] = $post['supplier_name'];
				$supplier['supplier_tel'] = $post['supplier_tel'];
				$supplier['supplier_pro_id'] = $post['supplier_pro'];
				$supplier['supplier_city_id'] = $post['supplier_city'];
				$supplier['supplier_area_id'] = $post['supplier_area'];
				$supplier['supplier_street'] = $post['supplier_street'];
				$supplier['spare_address'] = $post['spare_address'];
				$supplier['info_create_time'] = time();
				$supplier['info_update_time'] = time();
				// 添加供应商可做产品
				$productIdStr = implode(",", $post['checkbox']);   // 将数据转换为字符串
				$supplier['product_id_str'] = $productIdStr;
				// 执行添加供应商基本信息操作
				$supplierModel = D("XgSupplier");
				$res_1 = $supplierModel->addSupplierInfo($supplier);
				// 向产品表里添加供应商id
				for ($i=0; $i < sizeof($post["checkbox"]); $i++) {
					$id = $post["checkbox"][$i];
					// echo $id;
					$data = $res_1;
					$supplierIdStr = D("XgProductType")->getSupplierIdStr($id);
					if($supplierIdStr[0]["supplier_id_str"] == ""){
						$supplierIdStr = $data;
						$result = D("XgProductType")->updateSupplierIdStr($id,$supplierIdStr);
						// dump($supplierIdStr);
						// dump($result);
						// if($result){
						// 	echo "<script>alert('供应商ID添加成功');</script>";
						// }else{
						// 	echo "wtf";
						// }
					}else{
						$supplierIdStr = $supplierIdStr[0]["supplier_id_str"].",".$data;
						$result = D("XgProductType")->updateSupplierIdStr($id,$supplierIdStr);
						// dump($supplierIdStr);
					}
				}
				// 添加供应商联系人信息
				if(!empty($post['link_name0'])){
					$select_num_hide = $post['select_num_hide'];
					for($i=0;$i<=$select_num_hide;$i++){
						$linkman['supplier_id']=$res_1;    //公司的id
						$linkman['linkman_name']=$post['link_name'.$i];
						$linkman['linkman_phone']=$post['link_phone'.$i];
						$linkman['create_time'] = time();
						$linkman['update_time'] = time();
						$model = D("XgSupplierLinkman");
						$res_2 = $model->addSupplierLinkman($linkman);
						// dump($res_2);
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


			//查询供应商公司信息
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
			// 查询供应商可做产品
			$productIdArr = explode(",", $supplierInfo["product_id_str"]);
			// dump($productIdArr);
			// $supplierProduct = array();
			foreach ($productIdArr as $ke => $pi) {
				$id = $pi;
				$product = D("XgProductType")->getProduct($id);
				// dump($product);exit;
				$supplierProduct[$product['0']['pid']][] = $product['0'];
				// dump($supplierProduct[$product['0']['pid']]);
			}
			// dump($supplierProduct);
			foreach ($supplierProduct as $key => $sp) {
				$id = $key;
				// dump($id);
				$productType = D("XgProductType")->getProduct($id);
				$supplierProductInfo[$id]['productType'] = $productType['0'];
				$supplierProductInfo[$id]['productType']['product'] = $sp;
				// dump($productType);
				// dump();exit;
				
			}

			$supplierInfo["local_name"] = $localName;
			$supplierInfo["address"] = $address;
			//获得联系人信息
			$supplierId = $supplierInfo["supplier_id"];
			$linkmanInfo = D("XgSupplierLinkman")->findAllLinkman($supplierId);
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("supplierLinkmanInfo",$linkmanInfo);
			$this->assign("supplierProductInfo",$supplierProductInfo);

			$this->display();
		}

		// 修改供应商信息页面
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

			
			$supplierId = $_GET['supplier_id'];
			// 查询供应商公司信息
			$supplierInfo = D("XgSupplier")->getSupplierInfo($supplierId);
			// 客户公司联系人信息 
			$linkmanInfo = D("XgSupplierLinkman")->findAllLinkman($supplierId);
			// 获取省份信息
			$provinceInfo = $this->getProvince("0");
			// 得到城市名称
			$cityInfo = $this->getCity("0");
			// 得到地区名称
			$areaInfo = $this->getArea("0");
			// 获取全部产品信息
			$productTypeModel = D("XgProductType");
			$productType = $productTypeModel->getProductType();
			foreach ($productType as $k => $v) {
	    		$pid = $v["id"];
	    		$productMenu = $productTypeModel->getProductTypeByPid($pid);
	    		// dump($productMenu);
	    		$productType[$k]["productMenu"] = $productMenu;
	    		// 获取供应商可做产品的ID
				$productIdArr = explode(",", $supplierInfo["product_id_str"]);
				// dump($productIdArr);
				$productType[$k]["productIdArr"] = $productIdArr;
				// dump($productType);
	    	}
	    	
			$this->assign("supplierInfo",$supplierInfo);
			$this->assign("linkmanInfo",$linkmanInfo);
			$this->assign("province",$provinceInfo);
			$this->assign("city",$cityInfo);
			$this->assign("area",$areaInfo);
			$this->assign("productType",$productType);
	 		$this->assign("productIdArr",$productIdArr);
			$this->display();

			
			
			
		}
		// 修改供应商信息处理
		public function updateHandle(){
			$supplierId = $_POST['supplier_id'];
			// dump($supplierId);
			$post = $_POST;
			// dump($post);
			if($post){
				$supplier['supplier_name'] = $post['supplier_name'];
				$supplier['supplier_tel'] = $post['supplier_tel'];
				$supplier['supplier_pro_id'] = $post['supplier_pro'];
				$supplier['supplier_city_id'] = $post['supplier_city'];
				$supplier['supplier_area_id'] = $post['supplier_area'];
				$supplier['supplier_street'] = $post['supplier_street'];
				$supplier['spare_address'] = $post['spare_address'];
				$supplier['info_update_time'] = time();
				// dump($post["checkbox"]);
				$productIdStr = implode(",", $post["checkbox"]);
				$supplier['product_id_str'] = $productIdStr;
				// dump($productIdStr);
				//更新供应商信息
				$result_1 = D("XgSupplier")->updateSupplierInfo($supplierId,$supplier);
				// 向产品分类表中添加供应商id
				foreach ($post['checkbox'] as $key => $v) {
					$supplierId = $post['supplier_id'];
					echo $supplierId;
					$id = $v;;
					echo $id;
					$supplierIdInfo = D("XgProductType")->getSupplierIdStr($id);
					// dump($supplierIdInfo[0]['supplier_id_str']);
					if($supplierIdArr == ""){
						$supplierIdStr = $supplierId.",";
					}else if(in_array($supplierId, $supplierIdArr) == FALSE){
						$supplierIdStr = $supplierIdInfo[0]['supplier_id_str'].$supplierId.",";
						echo $supplierIdStr;
					}
					$result_3 = D("XgProductType")->updateSupplierIdStr($id,$supplierIdStr);
					if($result_3){
						echo "<script>alert('厉害了')</script>";
					}
				}
				
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
				// dump($result_2);
				// 根据数据添加的情况来判断页面跳转
				if($result_1){
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
		
		//删除供应商及联系人信息
		public function deletes(){
			$supplier_id = $_GET['supplier_id'];
			//删除联系人信息
			$result_1 = D("XgSupplierLinkman")->deleteBySupplierId($supplier_id);
			//删除客户信息
			$result_2 = D("XgSupplier")->deleteBySupplierId($supplier_id); 
			$res_str = " ";
			if($result_2){
				$res_str = "删除成功";
			}else{
				$res_str = "删除失败！";
			}

			echo json_encode($res_str);
		}

		//联系人详细信息
		public function detailsLinkman(){
			//获得联系人信息
			if($_GET['supplier_id']){
				$linkmanInfo = D("XgSupplierLinkman")->findAllLinkman($_GET["supplier_id"]);
			}
			$this->assign("linkmanInfo",$linkmanInfo);

			$this->display();
		}

		//删除联系人信息
		public function deletesLinkman(){
			$linkman_id = $_GET['linkman_id'];
			//删除联系人信息
			$result = D("XgSupplierLinkman")->deleteById($linkman_id);
			
			$res_str = " ";
			if($result > 0){
				$res_str = "删除成功";
			}else{
				$res_str = "删除失败！";
			}

			echo json_encode($res_str);
		}

		//已知客户信息，添加联系人信息
		public function addSupplierLinkman(){
			//客户信息ID
			$supplier_id = $_GET['supplier_id'];
			
			if ($_POST['supplier_id'] && $_POST['link_name'] ) {
				//添加联系人信息
				$linkman['supplier_id']=$_POST['supplier_id'];    //公司的id
				$linkman['linkman_name']=$_POST['link_name'];
				$linkman['linkman_phone']=$_POST['link_phone'];
				$linkman['create_time'] = time();
				$linkman['update_time'] = time();

				$result = D("XgSupplierLinkman")->addSupplierLinkman($linkman);
				if($result > 0){
					$res = 1;
				}else{
					$res = 2;
				}
				echo $res;	
			}else{
				$this->assign("supplier_id",$supplier_id);
	 		
				$this->display();
			}
		}

		
	}