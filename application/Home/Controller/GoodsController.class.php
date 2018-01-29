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
			$this->assign("productName",$productName);
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
			// //获取分类名称为印刷服务的分类
			// $condition['yinshua']['where'] = "pid = '0' and type_name = '印刷服务' ";
			// $yinshua = D("XgProductType")->getProductInfo($condition['yinshua']);
			// $yinshuaId = $yinshua['0']['id'];
			//获取商品的分类
			$condition['type']['where'] = "	have_price = 1 ";
			$productTypeInfo = D("XgProductType")->getProductInfo($condition['type']);
			// //获取全部商品名称
			// $condition['name']['where'] = "pid > 0  and pid != ".$yinshuaId."";
			// $productInfo = D("XgProductType")->getProductInfo($condition['name']);
			//获取商品的型号
			$productModelInfo = D("XgProduct")->getProductModelInfo();
	 		
			$this->assign("productSpecInfo",$productSpecInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productTypeInfo",$productTypeInfo);
			// $this->assign("productInfo",$productInfo);
			$this->assign("productModelInfo",$productModelInfo);
			$this->assign("productParameterInfo",$productParameterInfo);
			$this->assign("search",$search);

			$this->display();
		}

		// 服务类型管理页面
		public function productType(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$productTypeInfo = D("XgProductType")->getProductType();
			$this->assign("productTypeInfo",$productTypeInfo);
			$this->display();
		}
		// 添加服务类型
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
		// 修改服务类型名称
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
		// 删除服务类型
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


		// 产品规格页面
		public function productParameter(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			// dump($_GET['product_id']);exit;
			$product_id = $_GET['product_id'];
			if(empty($product_id)){
				$productParameterInfo = D("XgProductParameter")->getProductParameter();
			}else{
				$parameter_id_str = D("XgProductType")->getParameterIdStr($product_id);
				if(!empty($parameter_id_str[0]['parameter_id_str'])){
					// dump($parameter_id_str);
					$parameter_id_arr = explode(",",$parameter_id_str[0]['parameter_id_str']);
					// dump($parameter_id_arr);
					// dump(count($parameter_id_arr));
					$num=count($parameter_id_arr);
					for($i=0;$i<$num;$i++){
						$id = $parameter_id_arr[$i];
						// dump($id);
						$productParameterdata = D("XgProductParameter")->getParameterByProduct($id);
						// 判断选出的产品规格名称是否已被删除，如果已被删除将不会显示出来
						if(!empty($productParameterdata)){
							$productParameterInfo[] = $productParameterdata;
						}
						// dump($productParameterdata);
					}
					// dump($productParameterInfo);
					// exit;	
				}
			}
			$this->assign("productParameterInfo",$productParameterInfo);
			
			$this->display();
		}
		// 验证产品规格名称是否已存在
		public function checkProductParameter(){
			if($_POST){
				$name = $_POST["name"];
				$data = D("XgProductParameter")->getProductParameterName($name);
				if(empty($data)){
					$res = 1;
				}else{
					$res = 0;
				}
				echo $res;
			}
		}
		// 添加产品规格名称
		public function addProductParameter(){
			$post = $_POST;
			if(!empty($post)){
				$parameter['name'] = $post['name'];
				$res = D("XgProductParameter")->addProductParameterInfo($parameter);
				echo $res;
			}else{
				$this->display();
			}		
		}
		// 修改产品规格名称
		public function updateProductParameter(){
			$post = $_POST;
			$get = $_GET;
			if(!empty($post)){
				$id = $post['id'];
				$parameter['name'] = $post['name'];
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
		// 删除产品规格名称
		public function deleteProductParameter(){
			$post = $_POST;
			// 删除有该规格的产品的规格id
			$idStr = $post["parameter_id"];
			// print_r($idStr);
			$product = D("XgProductType")->getProductByParameterIdStr($idStr);
			// print_r(sizeof($product));
			$arrLength = count($product);
			for ($i=0; $i < $arrLength; $i++) { 
				// print_r($product[$i]["parameter_id_str"]);
				$parameterIdStr = $product[$i]["parameter_id_str"];
				$parameterIdArr = explode(",", $parameterIdStr);
				// print_r($parameterIdArr);
				foreach ($parameterIdArr as $k => $v) {
					if($v == $idStr){
						unset($parameterIdArr[$k]);
					}
				}
				// print_r($parameterIdArr);
				$newParameterIdStr = implode(",",$parameterIdArr);
				// print_r($newParameterIdStr);
				$productId = $product[$i]["id"];
				// print_r($productId);
				$data['parameter_id_str'] = $newParameterIdStr;
				// print_r($data);
				$res_product = D("XgProductType")->updateParameterStrId($productId,$data);
			}
			// print_r($product);
			// exit;
			//删除产品对应的详细规格信息
			$res_spec = D("XgProductSpec")->deleteProductSpecByParameter_id($post['parameter_id']);
			//删除商品规格名称信息
			$res_parameter = D("XgProductParameter")->deleteProductParameterById($post['parameter_id']);
			if($res_parameter > 0 ){
				$res_str = 1;
			}else{
				$res_str = 0;
			}
			echo json_encode($res_str); 
		}


		// 产品名称管理页面
		public function productName(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取服务类型
			$productType = D("XgProductType")->getProductType();

			$condition['where'] = "pid > 0 ";
			$product_type_id = $get['product_type'];
			if($product_type_id){
				$condition['where'] = "pid = ".$product_type_id."";
			}
			
			//表xg_product_type中符合条件的总记录数
			$count = D("XgProductType")->getProductCount($condition['where']);
	 		$pageSize = 10;
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

	 		$productInfo = D("XgProductType")->getProductInfo($condition);
	 		// dump($productInfo);
	 		foreach ($productInfo as $k => $v) {
	 			//查询服务类型
	 			$typeName = D("XgProductType")->getProductName($v['pid']);
	 			$productInfo[$k]["type"] = $typeName["0"]["type_name"];
	 			//查询产品规格信息
	 			// $parameter_id_str = rtrim($v['parameter_id_str'], ',');
				$productParameterInfo = D("XgProductParameter")->getProductParameterByIdWhereIn($v['parameter_id_str']);
				// dump($productParameterInfo);
				$parameter = "";
	 			if(!empty($productParameterInfo)){
	 				foreach ($productParameterInfo as $kk => $vv) {
	 					$parameter .= $vv['name']." / ";
	 				}
	 				$parameter = rtrim($parameter,' / ');
	 				$productInfo[$k]['parameter'] = $parameter;
	 			}
	 			// 查询产品特殊工艺
	 			$specialTec = D("XgProductSpecialTechnology")->getSpecialByWhereIdIn($v['special_tec_str']);
	 			$specialTecInfo = "";
	 			if(!empty($specialTec)){
	 				foreach ($specialTec as $key => $value) {
	 					$specialTecInfo .= $value['name']." / ";
	 				}
	 				$specialTecInfo = rtrim($specialTecInfo," / ");
	 				$productInfo[$k]['specialTecInfo'] = $specialTecInfo;
	 			}
	 		}
	 		// dump($productInfo);
	 		// exit;
			$this->assign("productInfo",$productInfo);
			$this->assign("pageStr",$pageStr);
			$this->assign("productType",$productType);
			$this->assign("product_type_id",$product_type_id);

			$this->display();
		}
		// 产品名称是否存在验证
		public function checkProductName(){
			if($_POST){
				$name = $_POST['name'];
				$result = D("XgProductType")->getProductByName($name);
				if(empty($result)){
					echo 1;
				}else{
					echo 0;
				}
			}
		}
		// 添加产品名称及其规格名称
		public function addProductName(){
			$post = $_POST;
			//获取产品全部分类
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取产品的全部规格类型
			$productParameterInfo = D("XgProductParameter")->getProductParameter();
			// 获取全部特殊工艺
			$specialTecInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnology();

			if(!empty($post)){
				$product['type_name'] = $post['type_name'];
				$product['pid'] = $post['pid'];
				$product['parameter_id_str'] = $post['parameter'];
				$product['special_tec_str'] = $post['specialTec'];
				$product['product_unit'] = $post['product_unit'];
				$res = D("XgProductType")->addProductTypeInfo($product);

				echo json_encode($res);
			}else{
				$this->assign("specialTecInfo",$specialTecInfo);
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productParameterInfo",$productParameterInfo);

				$this->display();
			}		
		}
		// 修改产品名称及其规格名称
		public function updateProductName(){
			$post = $_POST;
			$get = $_GET;
			if(!empty($post)){
				$id = $post['id'];
				$product['type_name'] = $post['type_name'];
				$product['pid'] = $post['pid'];
				$product['parameter_id_str'] = $post['parameter'];
				$product['special_tec_str'] = $post['specialTec'];
				$product['product_unit'] = $post['product_unit'];
				$res = D("XgProductType")->updateProductTypeInfo($product,$id);
				echo json_encode($res);
			}else{
				//查询出全部的商品分类信息
				$productType = D("XgProductType")->getProductType();
				//查询出对应的信息
				$productTypeInfo = D("XgProductType")->getProduct($get['name_id']);
				$selectParameter = explode(",", $productTypeInfo['0']['parameter_id_str']);
				// 当前产品的特殊工艺
				$selectSpecialTec = explode(",", $productTypeInfo['0']['special_tec_str']);
				//查询产品规格的全部信息
				$productParameterInfo = D("XgProductParameter")->getProductParameter();
				// 查询全部特殊工艺信息
				$specialTecInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnology();
				$this->assign("productType",$productType);
				$this->assign("productTypeInfo",$productTypeInfo);
				$this->assign("productParameterInfo",$productParameterInfo);
				$this->assign("selectParameter",$selectParameter);
				$this->assign("specialTecInfo",$specialTecInfo);
				$this->assign("selectSpecialTec",$selectSpecialTec);
				$this->display();
			}
		}
		// 删除产品名称及其相关数据
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


		// 产品型号管理页面
		public function productModel(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取商品的分类
			$productTypeInfo = D("XgProductType")->getProductType();
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
	 		$pageSize = 10;
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
		// 添加产品型号
		public function addProductModel(){
			$post = $_POST;
			//获取全部服务类型及产品名称
			$productTypeInfo = D("XgProductType")->getProductType();
			//获取全部产品名称
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
		// 修改商品型号
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
		// 删除商品型号信息
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


		// 产品详细规格信息
		public function productSpec(){
			$productType = $this->menu();
			$this->assign("productType",$productType);
			$get = $_GET;
			//获取商品的分类
			$productTypeInfo = D("XgProductType")->getProductType();
			
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
		// 添加商品规格
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
		// 添加商品规格
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
		// 修改商品规格信息
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
		// 删除产品规格信息
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
				//产品名称
				$productName = D("XgProductType")->getProduct($get['product']);
				//服务类型
				$productType = D("XgProductType")->getProduct($productName['0']['pid']);
				//产品型号
				$productModel = D("XgProduct")->getProductById($get['product_model']);
				//产品规格信息处理
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



		// 产品特殊工艺页面
		public function specialTechnology(){
			$specialTec = D("XgProductSpecialTechnology")->getProductSpecialTechnology();
			// dump($specialTec);exit;
			$this->assign("specialTec",$specialTec);
			$this->display();
		}
		// 验证产品特殊工艺名称是否已存在
		public function checkSpecialTec(){
			if($_POST){
				$name = $_POST["name"];
				$data = D("XgProductSpecialTechnology")->getSpecialTecByName($name);
				if(empty($data)){
					$res = 1;
				}else{
					$res = 0;
				}
				echo $res;
			}
		}
		// 添加产品特殊工艺名称
		public function addSpecialTec(){
			$post = $_POST;
			if(!empty($post)){
				$tec['name'] = $post['name'];
				$res = D("XgProductSpecialTechnology")->addSpecialTec($tec);
				echo $res;
			}else{
				$this->display();
			}
		}
		// 修改产品特殊工艺名称页面
		public function updateSpecialTec(){
			$get = $_GET;
			if($get){
				$id = $get["tec_id"];
				$specialTecInfo = D("XgProductSpecialTechnology")->getProductSpecialTechnologyById($id);
				$this->assign("specialTecInfo",$specialTecInfo);
				$this->display();
			}
		}
		// 修改产品特殊工艺名称处理
		public function updateSpecialTecHandle(){
			$post = $_POST;
			if(!empty($post)){
				$id = $post['id'];
				$data['name'] = $post['name'];
				$res = D("XgProductSpecialTechnology")->updateSpecialTec($id,$data);
				echo $res;
			}
		}
		// 删除产品特殊工艺名称
		public function deleteSpecialTec(){
			$post = $_POST;
			$id = $post['tec_id'];
			// 查询有like $id 的产品信息
			$productInfo = D("XgProductType")->getProductBySpecialTecId($id);
			// print_r($productInfo);
			$infoLength = count($productInfo);
			// 逐一修改有该特殊工艺的产品信息里的特殊工艺字符串
			if($infoLength >0){
				for ($i=0; $i < $infoLength; $i++) { 
					$specialTecIdStr = $productInfo[$i]["special_tec_str"];
					$specialTecIdArr = explode(",", $specialTecIdStr);
					foreach ($specialTecIdArr as $k => $v) {
						if($v == $id){
							unset($specialTecIdArr[$k]);
						}
					}
					$newSpecialTecIdStr = implode(",", $specialTecIdArr);
					$productId = $productInfo[$i]["id"];
					// print_r($productId);
					$data['special_tec_str'] = $newSpecialTecIdStr;
					// print_r($data);
					$res_product = D("XgProductType")->updateSpecialTecStr($productId,$data);
					// dump($res_product);
				}
			}
			// 删除产品特殊工艺名称
			$res_2 = D("XgProductSpecialTechnology")->deleteSpecialTec($id);
			if($res_2 > 0){
				echo 1;
			}else{
				echo 0;
			}

		}

	}