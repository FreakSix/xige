<?php
	namespace Home\Controller;
	
	
	class GoodsController extends BaseController{

		public function index(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			//根据商品pid来获取对应的商品型号
			$pid = $_GET['type_id'];
			$productModel = D("XgProduct")->getProductByPid($pid);
			//根据商品型号信息来默认获取第一种商品型号对应的商品规格信息
			$productName = D("XgProductType")->getProduct($pid);
			$parameterId = explode(",", $productName['0']['parameter_id_str']);
			$supplierId = explode(",", $productName['0']['supplier_id_str']);

			if(!empty($parameterId)){
				$spec_str = "";
				foreach ($parameterId as $k => $v) {
					//获取商品规格名称信息
					$productParameter = D("XgProductParameter")->getProductParameterById($v);
					//获取该商品型号对应的商品规格信息
					$condition['where'] = "parameter_id = '".$v."' and product_id = '".$pid."' and product_model_id = '".$productModel['0']['id']."'";
					$productSpec = D("XgProductSpec")->getProductSpecInfo($condition);
					if(!empty($productSpec)){
						$spec_id_str = "";
						foreach ($productSpec as $specId => $specValue) {
							$spec_id_str .= $specValue['id']."-";
						}
					}
					if($spec_id_str){
						$spec_id_str = rtrim($spec_id_str,"-");
						$spec_str .= $v."-".$productParameter['0']['name']."@".$spec_id_str.",";
					}
					
					//拼接该商品下第一种商品型号对应的商品规格信息
					if(!empty($productParameter['0'])){
						$specInfo[$k]['parameter'] = $productParameter['0'];

						if(!empty($productSpec)){
							$specInfo[$k]['spec'] = $productSpec;
						}
					}
					
				}
			}
			$spec_str = rtrim($spec_str,",");
			// dump($spec_str);exit;
			//获取供应商信息
			$productSupplier = array();
			// dump($productSupplier);exit;
			if(!empty($supplierId)){
				foreach ($supplierId as $kk => $vv) {
					$productSupplierInfo = D("XgSupplier")->getSupplierInfo($vv);
					if(!empty($productSupplierInfo)){
						$productSupplier[] = $productSupplierInfo;
					}
				}
			}

			//对商品规格为空的数据进行提示处理
			if(empty($specInfo)){
				$specInfo['0']['parameter']['name'] = "商品规格";
				$specInfo['0']['spec']['0']['spec_value'] = "请先添加该商品的规格信息！";
			}
			//对供应商为空的数据进行提示处理
			if(empty($productSupplier)){
				$productSupplier[]['supplier_name'] = "暂无供应商";
			}
			//对商品为空的数据进行提示处理
			if(empty($productModel)){
				$productModel[]['name'] = "请先添加商品信息";
			}

			$this->assign("productModel",$productModel);
			$this->assign("specInfo",$specInfo);
			$this->assign("productSupplier",$productSupplier);
			$this->assign("spec_str",$spec_str);

			$this->display();
		}

		public function getProductSpec(){
			$post = $_POST;
			//查询商品型号对应的商品名称
			$productName = D("XgProductType")->getProduct($post['product_name_id']);
			$parameterId = explode(",", $productName['0']['parameter_id_str']);
			$parameterSpec = "";
			if(!empty($parameterId)){
				foreach ($parameterId as $k => $v) {
					//获取商品规格名称信息
					$productParameter = D("XgProductParameter")->getProductParameterById($v);
					//获取该商品型号对应的商品规格信息
					$condition['where'] = "parameter_id = '".$v."' and product_id = '".$productName['0']['id']."' and product_model_id = '".$post['product_model_id']."'";
					$productSpec = D("XgProductSpec")->getProductSpecInfo($condition);
					//拼接该商品下第一种商品型号对应的商品规格信息
					if(!empty($productParameter['0'])){
						// $specInfo[$k]['parameter'] = $productParameter['0'];
						$parameterSpec .= "<div class='am-u-sm-2'> <div class='goods-title'>".$productParameter['0']['name']."</div> 
								<div class='goods-name'> <ul class='goods-name-detail'> ";
						$specStr = "";
						if(!empty($productSpec)){
							foreach ($productSpec as $kk => $vv) {
								$specStr .= "<li class='select-box' id='product_spec_".$vv['id']."' ><a href='javascript:void(0);' onclick='changeProductSpec(".$vv['id'].")' >".$vv['spec_value']."</a></li> 
											<input type='hidden' value='".$productParameter['0']['id']."-".$productParameter['0']['name']."' id='hide_parameter_value_".$vv['id']."' />	";
							}
						}else{
							$specStr = "<li class='select-box'><a href='javascript:;'>暂无</a></li>";
						}
						$parameterSpec = $parameterSpec.$specStr."</ul> </div> </div> ";

					}
				}
			}
			if($parameterSpec == ''){
				$parameterSpec = "<div class='am-u-sm-2'> <div class='goods-title'>商品规格</div> 
								<div class='goods-name'> <ul class='goods-name-detail'> <li class='select-box'><a href='javascript:;'>请先添加该商品的规格信息！</a></li> </ul> </div> </div> ";
			}

			//拼接供应商信息
			//查询商品型号对应的商品名称
			$supplierId = explode(",", $productName['0']['supplier_id_str']);
			$supplierStr = "<div class='am-u-sm-2'> <div class='goods-title'>供应商</div> <div class='goods-name'> <ul class='goods-name-detail'> ";
			if(!empty($supplierId)){
				$supplierStrLi = "";
				foreach ($supplierId as $k => $v) {
					//获取供应商信息
					$productSupplier = D("XgSupplier")->getSupplierInfo($v);
					
					if(!empty($productSupplier)){
						$supplier[] = $productSupplier;
						$supplierStrLi .= "<li class='select-box' id='supplier_".$productSupplier['supplier_id']."'><a href='javascript:void(0);' onclick='changeSupplier(".$productSupplier['supplier_id'].")'>".$productSupplier['supplier_name']."</a></li>";
					}
				}
			}
			if($supplierStrLi){
				$supplierStr = $supplierStr.$supplierStrLi."</ul> </div> </div> ";
			}else{
				$supplierStr = $supplierStr."<li class='select-box'><a href='javascript:;'>暂无供应商</a></li> </ul> </div> </div> ";
			}

			$html = $parameterSpec.$supplierStr;
			echo $html;
		}


		//添加商品价格
		public function productPrice(){

			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取分类名称为印刷服务的分类
			$condition['yinshua']['where'] = "pid = '0' and type_name = '印刷服务' ";
			$yinshua = D("XgProductType")->getProductInfo($condition['yinshua']);
			$yinshuaId = $yinshua['0']['id'];
			//获取商品的分类
			$condition['type']['where'] = "pid = '0' and id != ".$yinshuaId."";
			$productTypeInfo = D("XgProductType")->getProductInfo($condition['type']);
			//获取全部商品名称
			$condition['name']['where'] = "pid > 0  and pid != ".$yinshuaId."";
			$productInfo = D("XgProductType")->getProductInfo($condition['name']);
			//获取商品的型号
			$productModelInfo = D("XgProduct")->getProductModelInfo();
	 		
			$this->assign("productSpecInfo",$productSpecInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productInfo",$productInfo);
			$this->assign("productModelInfo",$productModelInfo);
			$this->assign("productParameterInfo",$productParameterInfo);
			$this->assign("search",$search);

			$this->display();
		}

		/*商品类型*/
		public function productType(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$productTypeInfo = D("XgProductType")->getProductType();
			
			$this->assign("productTypeInfo",$productTypeInfo);
			
			$this->display();
		}
		/*商品参数*/
		public function productParameter(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$productParameterInfo = D("XgProductParameter")->getProductParameter();
			
			$this->assign("productParameterInfo",$productParameterInfo);
			
			$this->display();
		}
		/*商品名称*/
		public function productName(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取商品的分类
			$productType = D("XgProductType")->getProductType();

			$condition['where'] = "pid > 0 ";
			$product_type_id = $get['product_type'];
			if($product_type_id){
				$condition['where'] = "pid = ".$product_type_id."";
			}
			
			//表xg_product_type中符合条件的总记录数
			$count = D("XgProductType")->getProductCount($condition['where']);
	 		$pageSize = 5;
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

	 		$productInfo = D("XgProductType")->getProductInfo($condition);
	 		
	 		foreach ($productInfo as $k => $v) {
	 			//查询所属分类
	 			$typeName = D("XgProductType")->getProductName($v['pid']);
	 			$productInfo[$k]["type"] = $typeName["0"]["type_name"];
	 			//查询商品规格信息
	 			// $parameter_id_str = rtrim($v['parameter_id_str'], ',');
				$productParameterInfo = D("XgProductParameter")->getProductParameterByIdWhereIn($v['parameter_id_str']);
	 			if(!empty($productParameterInfo)){
	 				foreach ($productParameterInfo as $kk => $vv) {
	 					$parameter .= $vv['name']." / ";
	 				}
	 				$parameter = rtrim($parameter,' / ');
	 				$productInfo[$k]['parameter'] = $parameter;
	 			}
	 		}

			$this->assign("productInfo",$productInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productType",$productType);
			$this->assign("product_type_id",$product_type_id);

			$this->display();
		}

		/*商品型号*/
		public function productModel(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取商品的分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取全部商品
			$condition['product']['where'] = "pid > 0 ";
			$productInfo = D("XgProductType")->getProductInfo($condition['product']);
			// 条件搜索产品型号信息
			if($get['product_type'] || $get['product']){
				$search['product_type'] = $get['product_type'];
				$search['product'] = $get['product'];
				// 根据条件查询产品
				$productInfo = D('XgProductType')->getProductTypeByPid($get['product_type']);
				// 拼接查询产品型号的where条件
				if($get['product']){
					$condition['where'] = "pid = '".$get['product']."'";
					if(empty($get['product_type'])){
						//获取全部商品
						$condition['product']['where'] = "pid > 0 ";
						$productInfo = D("XgProductType")->getProductInfo($condition['product']);
					}
					
				}else{
					if(!empty($productInfo)){
						foreach ($productInfo as $k => $v) {
							$productTypeSearchId .= "pid = '".$v['id']."' or ";
						}
						$productTypeSearchId = rtrim($productTypeSearchId, ' or ');
						$condition['where'] = $productTypeSearchId;
					}
				}
			}
			
			//表xg_product_type中符合条件的总记录数
			$count = D("XgProduct")->getProductModelCount($condition['where']);
	 		$pageSize = 5;
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

	 		$productModelInfo = D("XgProduct")->getProductModelInfo($condition);
	 		
	 		if(!empty($productModelInfo)){
	 			foreach ($productModelInfo as $k => $v) {
		 			//获取商品名称
		 			$product = D("XgProductType")->getProduct($v['pid']);
		 			// dump($product);exit;
		 			$productModelInfo[$k]["product"] = $product["0"];
		 			//获取商品类型
		 			if(!empty($product)){
		 				$productType = D("XgProductType")->getProduct($product["0"]["pid"]);
		 				$productModelInfo[$k]["product_type"] = $productType["0"];
		 			}
		 		}
	 		}
	 		
			$this->assign("productModelInfo",$productModelInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productInfo",$productInfo);
			$this->assign("search",$search);

			$this->display();
		}

		/*商品型号*/
		public function productSpec(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取商品的分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取全部商品名称
			$condition['product']['where'] = "pid > 0 ";
			$productInfo = D("XgProductType")->getProductInfo($condition['product']);
			//获取商品的型号
			$productModelInfo = D("XgProduct")->getProductModelInfo();
			//获取商品的规格名
			$productParameterInfo = D("XgProductParameter")->getProductParameter();
			// 条件搜索产品型号信息
			if(!empty($get)){
				$search['product_type'] = $get['product_type'];
				$search['product'] = $get['product'];
				$search['product_model'] = $get['product_model'];
				$search['product_parameter'] = $get['product_parameter'];
				//如果有产品分类
				if($get['product_type']){
					$whereArr[] = "product_type_id = '".$get['product_type']."'";
					//根据所选商品分类来查询对应的商品分类信息
					$productInfo = D("XgProductType")->getProductTypeByPid($get['product_type']);
				}
				//如果有产品名称
				if($get['product']){
					$whereArr[] = "product_id = '".$get['product']."'";
					//根据产品名称来查询对应的商品型号信息
					$productModelInfo = D("XgProduct")->getProductByPid($get['product']);
					//根据产品名称来查询对应的商品规格信息
					$product = D("XgProductType")->getProduct($get['product']);
					// $parameter_id_str = rtrim($product['0']['parameter_id_str'], ',');
					// $parameter_id_arr = explode(",",$parameter_id_str);
					$productParameterInfo = D("XgProductParameter")->getProductParameterByIdWhereIn($product['0']['parameter_id_str']);
				}
				//如果有产品型号
				if($get['product_model']){
					$whereArr[] = "product_model_id = '".$get['product_model']."'";
				}
				//如果有产品规格
				if($get['product_parameter']){
					$whereArr[] = "parameter_id = '".$get['product_parameter']."'";
				}
				$where = implode(' and ',$whereArr);
				$condition['spec']['where'] = $where;
				
			}
			
			//表xg_product_type中符合条件的总记录数
			$count = D("XgProductSpec")->getProductSpecCount($condition['spec']['where']);
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
	 		$condition['spec']['order'] = "id desc";
	 		$condition['spec']['limit']['firstRow'] = $firstRow;
	 		$condition['spec']['limit']['pageSize'] = $pageSize;

	 		$productSpecInfo = D("XgProductSpec")->getProductSpecInfo($condition['spec']);
	 		
	 		if(!empty($productSpecInfo)){
	 			foreach ($productSpecInfo as $k => $v) {
		 			//获取商品名称
		 			$product = D("XgProductType")->getProduct($v['product_id']);
		 			$productSpecInfo[$k]["product"] = $product["0"];
		 			//获取商品类型
		 			$productType = D("XgProductType")->getProduct($v["product_type_id"]);
		 			$productSpecInfo[$k]["product_type"] = $productType["0"];
		 			//获取商品型号
		 			$productModel = D("XgProduct")->getProductById($v["product_model_id"]);
		 			$productSpecInfo[$k]["product_model"] = $productModel["0"];
		 			//获取商品规格名
		 			$productParameter = D("XgProductParameter")->getProductParameterById($v["parameter_id"]);
		 			$productSpecInfo[$k]["product_parameter"] = $productParameter["0"];
		 		}
	 		}
	 		// dump($productSpecInfo);
	 		
			$this->assign("productSpecInfo",$productSpecInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->assign("productInfo",$productInfo);
			$this->assign("productModelInfo",$productModelInfo);
			$this->assign("productParameterInfo",$productParameterInfo);
			$this->assign("search",$search);

			$this->display();
		}

		/*添加商品服务类型*/
		public function addProductType(){
			$post = $_POST;
			if(!empty($post)){
				$product['type_name'] = $post['product_type'];
				$product['pid'] = $post['pid'];

				$res = D("XgProductType")->addProductTypeInfo($product);

				echo json_encode($res);
			}else{
				$this->display();
			}
		}

		/*添加商品规格名称*/
		public function addProductParameter(){
			$post = $_POST;
			if(!empty($post)){
				$parameter['name'] = $post['name'];
				$parameter['name_pinyin'] = $post['name_pinyin'];

				$res = D("XgProductParameter")->addProductParameterInfo($parameter);

				echo json_encode($res);
			}else{
				$this->display();
			}		
		}

		/*添加商品名称*/
		public function addProductName(){
			$post = $_POST;
			//获取商品全部分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取商品的全部规格类型
			$productParameterInfo = D("XgProductParameter")->getProductParameter();

			if(!empty($post)){
				$product['type_name'] = $post['type_name'];
				$product['pid'] = $post['pid'];
				$product['parameter_id_str'] = $post['parameter'];
				
				$res = D("XgProductType")->addProductTypeInfo($product);

				echo json_encode($res);
			}else{
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productParameterInfo",$productParameterInfo);

				$this->display();
			}		
		}

		/*添加商品型号*/
		public function addProductModel(){
			$post = $_POST;
			//获取商品全部分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取全部商品名称
			$condition['product']['where'] = "pid > 0 ";
			$productNameInfo = D("XgProductType")->getProductInfo($condition['product']);

			if(!empty($post)){
				$product['name'] = $post['model_name'];
				$product['pid'] = $post['pid'];
				
				$res = D("XgProduct")->addProductModelInfo($product);

				echo json_encode($res);
			}else{
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productNameInfo",$productNameInfo);

				$this->display();
			}		
		}

		/*添加商品规格*/
		public function addProductSpec(){
			$post = $_POST;

			//获取商品的分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取全部商品名称
			$condition['product']['where'] = "pid > 0 ";
			$productInfo = D("XgProductType")->getProductInfo($condition['product']);
			//获取商品的型号
			$productModelInfo = D("XgProduct")->getProductModelInfo();
			//获取商品的规格名
			$productParameterInfo = D("XgProductParameter")->getProductParameter();
			if(!empty($post)){
				$spec['spec_value']=$post['spec_value'];
				$spec['parameter_id']=$post['parameter_id'];
				$spec['product_type_id']=$post['type_id'];
				$spec['product_id']=$post['id'];
				$spec['product_model_id']=$post['model_id'];
				
				$res = D("XgProductSpec")->addSpecInfo($spec);

				echo json_encode($res);
			}else{
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productInfo",$productInfo);
				$this->assign("productModelInfo",$productModelInfo);
				$this->assign("productParameterInfo",$productParameterInfo);

				$this->display();
			}
		}
		/*添加商品规格*/
		public function addProductSpecVerify(){
			$post = $_POST;
			if(!empty($post)){

				$condition = "`spec_value` =  '".$post['spec_value']."' AND  `parameter_id` =".$post['parameter_id']." 
								AND  `product_type_id` =".$post['type_id']." AND  `product_id` =".$post['id']."
								AND  `product_model_id` =".$post['model_id']."";
				
				$res = D("XgProductSpec")->getProductSpecInfoByWhere($condition);
				if(!empty($res)){
					$resualt = 1;
				}else{ 
					$resualt = 2;
				}

				echo json_encode($resualt);
			}
		}

		/*添加商品规格*/
		public function addProductPrice(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){

				$spec_arr = explode(",", $post['spec_str']);
				foreach ($spec_arr as $k => $v) {
					$spec[] = (int)$v;
				}
				sort($spec);
				$spec_str = implode(",",$spec);

				$data['supplier_id']=$post['supplier'];
				$data['price']=$post['price'];
				$data['spec_id_str']=$spec_str;
				$data['product_id']=$post['product_model'];
				
				$res = D("XgSupplierProductRel")->addProductPrice($data);

				echo json_encode($res);
			}else{
				//商品名称
				$productName = D("XgProductType")->getProduct($get['product']);
				//商品分类
				$productType = D("XgProductType")->getProduct($productName['0']['pid']);
				//商品型号
				$productModel = D("XgProduct")->getProductById($get['product_model']);
				//商品规格信息处理
				$spec_str = '';
				$product_spec_arr = explode(",", $get["product_spec_str"]);

				foreach ($product_spec_arr as $k => $v) {
					$product_spec = explode("@", $v);
					$spec_str = $spec_str.$product_spec['1'].",";
					//商品规格名称
					$productParameter = D("XgProductParameter")->getProductParameterById($product_spec['0']);
					//商品规格值
					$productSpec = D("XgProductSpec")->getSpecById($product_spec['1']);
					$specInfo[$k]['parameter'] = $productParameter['0']['name'];
					$specInfo[$k]['spec'] = $productSpec['0']['spec_value'];
				}
				$spec_str = rtrim($spec_str,',');
				//供应商
				$productSupplier = D("XgSupplier")->getSupplierInfo($get['product_supplier']);
				//商品价格
				$productPrice['price'] = $get['product_price'];

				$this->assign("productName",$productName);
				$this->assign("productType",$productType);
				$this->assign("productModel",$productModel);
				$this->assign("specInfo",$specInfo);
				$this->assign("productSupplier",$productSupplier);
				$this->assign("productPrice",$productPrice);
				$this->assign("spec_str",$spec_str);


				$this->display();
			}
		}

		/*修改商品规格*/
		public function updateProductPrice(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){
				$id = $post['id'];
				$data['price']=$post['price'];

				$res = D("XgSupplierProductRel")->updateProductPrice($data,$id);

				echo json_encode($res);
			}else{
				//根据ID获取表XgSupplierProductRel中的信息
				$supplierProductPrice = D("XgSupplierProductRel")->getProductSupplierPriceInfoById($get['product_price_id']);
				//商品型号
				$productModel = D("XgProduct")->getProductById($supplierProductPrice['0']['product_id']);
				//商品名称
				$productName = D("XgProductType")->getProductTypeByPid($productModel['0']['pid']);
				//商品分类
				$productType = D("XgProductType")->getProduct($productName['0']['pid']);
				//商品规格信息处理
				if(!empty($supplierProductPrice['0']['spec_id_str'])){
					$spec_arr = explode(",", $supplierProductPrice['0']['spec_id_str']);
					foreach ($spec_arr as $k => $v) {
						//商品规格值
						$productSpec = D("XgProductSpec")->getSpecById($v);
						//商品规格名称
						$productParameter = D("XgProductParameter")->getProductParameterById($productSpec['0']['parameter_id']);
						$specInfo[$k]['parameter'] = $productParameter['0']['name'];
						$specInfo[$k]['spec'] = $productSpec['0']['spec_value'];
					}
				}
				//供应商
				$productSupplier = D("XgSupplier")->getSupplierInfo($supplierProductPrice['0']['supplier_id']);
				//商品价格
				$productPrice['price'] = $get['product_price'];

				$this->assign("productName",$productName);
				$this->assign("productType",$productType);
				$this->assign("productModel",$productModel);
				$this->assign("specInfo",$specInfo);
				$this->assign("productSupplier",$productSupplier);
				$this->assign("productPrice",$productPrice);
				$this->assign("supplierProductPrice",$supplierProductPrice);


				$this->display();
			}
		}

		/*修改商品服务类型*/
		public function updateProductType(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){
				$id = $post['id'];
				$product['type_name'] = $post['product_type'];

				$res = D("XgProductType")->updateProductTypeInfo($product,$id);

				echo json_encode($res);
			}else{
				//查询出对应的信息
				$productTypeInfo = D("XgProductType")->getProduct($get['type_id']);

				$this->assign("productTypeInfo",$productTypeInfo);

				$this->display();
			}
		}

		/*修改商品规格类型*/
		public function updateProductParameter(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){
				$id = $post['id'];
				$parameter['name'] = $post['name'];
				$parameter['name_pinyin'] = $post['name_pinyin'];

				$res = D("XgProductParameter")->updateProductParameterInfo($parameter,$id);

				echo json_encode($res);
			}else{
				//查询出对应的信息
				$productParameterInfo = D("XgProductParameter")->getProductParameterById($get['parameter_id']);
				// dump($productTypeInfo);
				$this->assign("productParameterInfo",$productParameterInfo);

				$this->display();
			}
		}

		/*修改商品名称*/
		public function updateProductName(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){
				$id = $post['id'];

				$product['type_name'] = $post['type_name'];
				$product['pid'] = $post['pid'];
				$product['parameter_id_str'] = $post['parameter'];

				$res = D("XgProductType")->updateProductTypeInfo($product,$id);

				echo json_encode($res);
			}else{
				//查询出全部的商品分类信息
				$productType = D("XgProductType")->getProductType();
				//查询出对应的信息
				$productTypeInfo = D("XgProductType")->getProduct($get['name_id']);
				$selectParameter = explode(",", $productTypeInfo['0']['parameter_id_str']);
				//查询商品规格的全部信息
				$productParameterInfo = D("XgProductParameter")->getProductParameter();
				
				$this->assign("productType",$productType);
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productParameterInfo",$productParameterInfo);
				$this->assign("selectParameter",$selectParameter);

				$this->display();
			}
		}

		/*修改商品型号*/
		public function updateProductModel(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){
				$id = $post['id'];

				$product['name'] = $post['model_name'];
				$product['pid'] = $post['pid'];
				
				$res = D("XgProduct")->updateProductModelInfo($product,$id);

				echo json_encode($res);
			}else{
				//查询出全部的商品分类信息
				$productType = D("XgProductType")->getProductType();
				//查询出全部的商品名称
				$condition['product']['where'] = "pid > 0 ";
				$productNameInfo = D("XgProductType")->getProductInfo($condition['product']);
				//查询出对应的商品型号信息
				$productModelInfo = D("XgProduct")->getProductById($get['model_id']);
				//商品型号对应的商品信息
				$productTypeId = D("XgProductType")->getProduct($productModelInfo['0']['pid']);
				
				$this->assign("productType",$productType);
				$this->assign("productNameInfo",$productNameInfo);
				$this->assign("productModelInfo",$productModelInfo);
				$this->assign("productTypeId",$productTypeId);

				$this->display();
			}
		}
 
		/*修改商品规格信息*/
		public function updateProductSpec(){
			$post = $_POST;
			$get = $_GET;
			
			if(!empty($post)){
				$id = $post['spec_id'];

				$spec['spec_value']=$post['spec_value'];
				$spec['parameter_id']=$post['parameter_id'];
				$spec['product_type_id']=$post['type_id'];
				$spec['product_id']=$post['id'];
				$spec['product_model_id']=$post['model_id'];
				
				$res = D("XgProductSpec")->updateProductSpecInfo($spec,$id);

				echo json_encode($res);
			}else{
				//查询出全部的商品分类信息
				$productTypeInfo = D("XgProductType")->getProductType();
				//查询出全部的商品名称
				$condition['product']['where'] = "pid > 0 ";
				$productNameInfo = D("XgProductType")->getProductInfo($condition['product']);
				//查询出全部的商品型号信息
				$productModelInfo = D("XgProduct")->getProductModelInfo();
				//获取商品的规格名
				$productParameterInfo = D("XgProductParameter")->getProductParameter();
				//查询出对应的商品规格信息
				$productSpecInfo = D("XgProductSpec")->getSpecById($get['spec_id']);
				
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productNameInfo",$productNameInfo);
				$this->assign("productModelInfo",$productModelInfo);
				$this->assign("productParameterInfo",$productParameterInfo);
				$this->assign("productSpecInfo",$productSpecInfo);

				$this->display();
			}
		}

		//删除商品分类信息
		public function deleteProductType(){
			$post = $_POST;
			//删除商品分类对应的规格信息
			$res_spec = D("XgProductSpec")->deleteProductSpecByProduct_Id($post['type_id']);
			//删除商品分类对应的型号信息
			$product = D("XgProductType")->getProductTypeByPid($post['type_id']);
			foreach ($product as $k => $v) {
				$res_model[$v['id']] = D("XgProduct")->deleteProductModelByPid($v['id']);
			}
			//删除商品分类对应的名称信息
			$res_name = D("XgProductType")->deleteProductByPid($post['type_id']);
			//删除商品分类信息
			$res_type = D("XgProductType")->deleteProductById($post['type_id']);
			if($res_type > 0 ){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str); 
		}

		//删除商品规格名称信息
		public function deleteProductParameter(){
			$post = $_POST;
			//删除商品名称对应的规格信息
			$res_spec = D("XgProductSpec")->deleteProductSpecByParameter_id($post['parameter_id']);
			//删除商品规格名称信息
			$res_parameter = D("XgProductParameter")->deleteProductParameterById($post['parameter_id']);
			if($res_parameter > 0 ){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str); 
		}

		//删除商品名称信息
		public function deleteProductName(){
			$post = $_POST;
			//删除商品名称对应的规格信息
			$res_spec = D("XgProductSpec")->deleteProductSpecByProduct_Id($post['name_id']);
			//删除商品名称对应的型号信息
			$res_model = D("XgProduct")->deleteProductModelByPid($post['name_id']);
			//删除商品名称信息
			$res_name = D("XgProductType")->deleteProductById($post['name_id']);
			if($res_name > 0 ){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str); 
		}

		//删除商品型号信息
		public function deleteProductModel(){
			$post = $_POST;
			//删除商品型号对应的规格信息
			$res_spec = D("XgProductSpec")->deleteProductSpecByProduct_Model_Id($post['model_id']);
			//删除商品型号信息
			$res_model = D("XgProduct")->deleteProductModelById($post['model_id']);
			if($res_model > 0 ){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str); 
		}

		//删除商品规格信息
		public function deleteProductSpec(){
			$post = $_POST;
			$res = D("XgProductSpec")->deleteProductSpecById($post['spec_id']);
			if($res > 0){
				$res_str = "删除成功！";
			}else{
				$res_str = "删除失败！";
			}
			echo json_encode($res_str); 
		}

		//Ajax获取商品分类下的商品名称
		public function getProductNames(){
			$post = $_POST;
			$productInfo = D("XgProductType")->getProductTypeByPid($post['type_id']);
			echo json_encode($productInfo);
		}
		//Ajax获取商品名称下的商品型号
		public function getProductModel(){
			$post = $_POST;
			$productModelInfo = D("XgProduct")->getProductByPid($post['id']);
			if(!empty($productModelInfo)){
				$productModel = $productModelInfo;
			}
			echo json_encode($productModel);
		}
		//Ajax获取商品名称对应的供应商
		public function getProductSupplier(){
			$post = $_POST;
			$productNameInfo = D("XgProductType")->getSupplierIdStr($post['id']);
			$supplierId = explode(",",$productNameInfo['0']['supplier_id_str']);
			foreach ($supplierId as $k => $v) {
				$supplier = D("XgSupplier")->getSupplierInfo($v);
				if(!empty($supplier)){
					$supplierInfo[] = $supplier;
				}
			}
			echo json_encode($supplierInfo);
		}
		//Ajax获取商品名称下的商品规格
		public function getProductParameter(){
			$post = $_POST;
			//获取对应的商品信息
			$productInfo = D("XgProductType")->getProduct($post['product_id']);

			// $parameter_id_str = rtrim($productInfo['0']['parameter_id_str'], ',');
			$productParameterInfo = D("XgProductParameter")->getProductParameterByIdWhereIn($productInfo['0']['parameter_id_str']);
			echo json_encode($productParameterInfo);
		}
		//Ajax获取对应商品型号的规格信息
		public function getProductSpecInfos(){
			$post = $_POST;
			//获取商品型号对应的商品信息
			$productNameInfo = D("XgProductType")->getProduct($post['product_id']);
			//该商品拥有的规格名称ID字符串数组
			$parameterIds = explode(",",$productNameInfo['0']['parameter_id_str']);
			foreach ($parameterIds as $k => $v) {
			 	//获取对应的商品规格名称信息
				$productParameterInfo = D("XgProductParameter")->getProductParameterById($v);
				//获取商品型号对应的规格数据
				$condition['where'] = "parameter_id = ".$v." and product_model_id = ".$post['model_id']."";
				$spec = D("XgProductSpec")->getProductSpecInfo($condition);
				if(!empty($productParameterInfo)){
					$productSpecInfo[$k] = $productParameterInfo['0'];
					$productSpecInfo[$k]['spec'] = $spec;
				}
			}
			// dump($productSpecInfo); 
			echo json_encode($productSpecInfo);	
		}

		//获取全部的商品型号信息
		public function getProductModelAll(){
			$productModelInfoAll = D("XgProduct")->getProductModelInfo();
			echo json_encode($productModelInfoAll);
		}
		//获取全部的商品规格信息
		public function getProductParameterAll(){
			$productParameterInfoAll = D("XgProductParameter")->getProductParameter();
			echo json_encode($productParameterInfoAll);
		}
		//验证添加的商品价格是否已经存在
		public function getProductPrice(){
			$post = $_POST;

			// dump($post);exit;
			if(!empty($post)){
				//商品规格信息处理
				$spec_arr = array();
				$product_spec_arr = explode(",", $post["product_spec_str"]);

				foreach ($product_spec_arr as $k => $v) {
					$product_spec = explode("@", $v);
					$spec_arr[] = (int)$product_spec['1'];
				}
				sort($spec_arr);
				$spec_str = implode(",",$spec_arr);

				// dump($spec_str);exit;

				$condition = "supplier_id = '".$post['product_supplier']."' and product_id = '".$post['product_model']."' and spec_id_str = '".$spec_str."'";

				// dump($condition);
				$res = D("XgSupplierProductRel")->getProductPriceInfo($condition);
				echo json_encode($res);
				// return $res; 
			}
		}

	}