<?php
	namespace Home\Controller;
	
	
	class GoodsController extends BaseController{
		
		public function index(){
			$pid = $_GET['type_id'];
			$productModel = D("XgProduct");
			$productArr = $productModel->getProductByPid($pid);
			$this->assign("productArr",$productArr);
			$this -> display();
		}
		/**
		 * 根据产品的id获取相关的详细信息，包括尺寸，纸张，还有印面
		 * @return json对象
		 */
		public function getProductNextDefault(){
			$pid = $_POST['pid'];
			
			$zhizhangModel = D("XgProductZhizhang");
			$sizeModel = D("XgProductSize");
			$yinmianModel = D("XgProductYinmian");
			
			$zhizhangArr = $zhizhangModel->getProductZhizhangByPid($pid);
			$sizeArr = $sizeModel->getProductSizeByPid($pid);
			$yinmianArr = $yinmianModel->getProductYinmianByPid($pid);
			
			$zhizhang = "";
			$size = "";
			$yinmian = "";
			if(!empty($zhizhangArr)){
				foreach($zhizhangArr as $k=>$v){
					if(!empty($v['zhizhang_name'])){
						$zhizhang.="<li class='selwect-box zhizhang_".$v['id']."'><a href='javascript:;' onclick='getGongyingshangByZhizhang(".$v['id'].")'>".$v['zhizhang_name']."</a></li>";
					}
				}
			}
			if(!empty($sizeArr)){
				foreach($sizeArr as $k=>$v){
					if(!empty($v['size_name'])){
						$size.="<li class='selwect-box size_".$v['id']."'><a href='javascript:;' onclick='getGongyingshangBySize(".$v['id'].")'>".$v['size_name']."</a></li>";
					}
				}
			}
			if(!empty($yinmianArr)){
				foreach($yinmianArr as $k=>$v){
					if(!empty($v['yinmian_name'])){
						$yinmian.="<li class='selwect-box yinmian_".$v['id']."'><a href='javascript:;' onclick='getGongyingshangByYinmian(".$v['id'].")'>".$v['yinmian_name']."</a></li>";
					}
				}
			}
			$data = array();
			$data['zhizhang'] = $zhizhang;
			$data['size'] = $size;
			$data['yinmian'] = $yinmian;
			$data['caizhi'] = $caizhi;
// 			print_r($data);
			$res = json_encode($data);
			echo $res;
		}
		
		
		/**
		 * 根据纸张，尺寸还有印面获取供应商的信息
		 * 注意，获取之前先根据产品的id进行删除
		 */
		
		public function getGongyingshangArr(){
			$product_id = $_POST['product_id'];
			$zhizhang_id = $_POST['zhizhang_id'];
			$size_id = $_POST['size_id'];
			$yinmian_id = $_POST['yinmian_id'];
			$supplierProductrelModel = D("XgSupplierProductRel");
			$relArr = $supplierProductrelModel->getRelByProductDetailId($product_id,$zhizhang_id,$size_id,$yinmian_id);
			//根据多个条件删选当前用户选择的信息剩余的供应商id
			$gongyingshang = "";
			$supplierModel = D("XgSupplier");
			if(!empty($relArr)){
				foreach($relArr as $k=>$v){
					$supplierInfo = $supplierModel->getSupplierInfo($v['supplier_id']);
					$gongyingshang.= "<li class='select-box'><a href='javascript:;' onclick='getGongyingshangPrice(".$v['price'].")'>".$supplierInfo['supplier_name']."</a></li>";
				}
			}
			echo $gongyingshang;
		}

		// /*商品信息添加*/
		// public function add(){
		// 	$pageSize = 3;
		// 	//获得产品服务类型
		// 	$productTypeInfo = D("XgProductType")->getProductType();
		// 	// var_dump($productTypeInfo);
		// 	//获取商品参数信息
		// 	$productParameterInfo = D("XgProductParameter")->getProductParameter();
			
		// 	//获取表xg_product_type中的商品名称
		// 	$condition['product']['where'] = "pid > 0 ";
		// 	//表xg_product_type中符合条件的总记录数
		// 	$productCount = D("XgProductType")->getProductCount($condition['product']['where']);
	 // 		//实例化分页类
	 // 		$productPage = new \Think\Page($productCount,$pageSize);
	 // 		//获取起始位置
	 // 		$productFirstRow = $productPage->firstRow;
	 // 		//获取分页结果
	 // 		$productPageStr = $productPage->show();
	 // 		//总页数
	 // 		$productTotalPage = $productPage->totalPages;
	 		
	 // 		//查询商品名称
	 // 		$condition['product']['order'] = "id desc";
	 // 		$condition['product']['limit']['firstRow'] = $productFirstRow;
	 // 		$condition['product']['limit']['pageSize'] = $pageSize;

	 // 		$productInfo = D("XgProductType")->getProductInfo($condition['product']);
	 // 		dump($condition);



		// 	$this->assign("productTypeInfo",$productTypeInfo);
		// 	$this->assign("productParameterInfo",$productParameterInfo);
		// 	$this->assign("productInfo",$productInfo);
		// 	$this->assign("productPageStr",$productPageStr);
		// 	$this->display();
		// }

		/*商品类型*/
		public function productType(){
			$productTypeInfo = D("XgProductType")->getProductType();
			
			$this->assign("productTypeInfo",$productTypeInfo);
			
			$this->display();
		}
		/*商品参数*/
		public function productParameter(){
			$productParameterInfo = D("XgProductParameter")->getProductParameter();
			
			$this->assign("productParameterInfo",$productParameterInfo);
			
			$this->display();
		}
		/*商品名称*/
		public function productName(){
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
	 			}
	 			$productInfo[$k]['parameter'] = $parameter;
	 		}

			$this->assign("productInfo",$productInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productType",$productType);
			$this->assign("product_type_id",$product_type_id);

			$this->display();
		}

		/*商品型号*/
		public function productModel(){
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

		/*修改商品服务类型*/
		public function updateProductType(){
			$post = $_POST;
			$get = $_GET;
			//查询出对应的信息
			$productTypeInfo = D("XgProductType")->getProduct($get['type_id']);
			if(!empty($post)){
				$id = $post['id'];
				$product['type_name'] = $post['product_type'];

				$res = D("XgProductType")->updateProductTypeInfo($product,$id);

				echo json_encode($res);
			}else{
				// dump($productTypeInfo);
				$this->assign("productTypeInfo",$productTypeInfo);

				$this->display();
			}
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
			echo json_encode($productModelInfo);
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

	}