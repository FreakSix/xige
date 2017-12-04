<?php
	namespace Home\Controller;
	
	
	class OrderController extends BaseController{
		// 订单首页
		public function index(){
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
		// 新增订单页面
		public function addOrder(){
			$customerModel = D("XgCustomer");
			$customerList = $customerModel->getCustomerInfos('');
			$productTypeModel = D("XgProductType");
			$productTypeFirst = $productTypeModel->getProductTypeByPid(0);
			$this->assign("productTypeFirst",$productTypeFirst);
			$this->assign("customerList",$customerList);
			$this->assign("qqq","aaaa");
			$this->display("add_order");
		}
		// 订单详情页
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
			$html = '';
			if(!empty($customerLxrArr)){
				foreach ($customerLxrArr as $k=>$v){
					$html.= "<option value='".$v['id']."' class='lxr_each' onclick='getLxrByCustomerId(".$v['id'].")'>".$v['name']."</option>";
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
			$html = '';
			if(!empty($productTypeFirst)){
				foreach($productTypeFirst as $k=>$v){
					$html .= " <option value='".$v['id']."' onclick='getChanpinXinghao(".$v['id'].")'>".$v['type_name']."</option>";
				}
			}
			echo $html;
		}
		/**
		 * 根据产品的名称获取产品的型号
		 * @param unknown $id
		 */
		public function getChanpinXinghaoByProductName($id){
			$productModel = D("XgProduct");
			$productXinghao = $productModel->getProductByPid($id);
			$productTyprModel = D("XgProductType");
			$parameInfo = $productTyprModel->getProduct($id);
			$xinghaoId = $parameInfo[0]['parameter_id_str'];
			$xinghaoIdArr = explode(",",$xinghaoId);
			$XgProductParameterModel = D("XgProductParameter");
			$xinghaoArr = array();
			if(!empty($xinghaoIdArr)){
				foreach($xinghaoIdArr as $k=>$v){
					$xinghaoArr[] = $XgProductParameterModel->getProductParameterById($v);
				}
			}
			$this->assign("productXinghao",$productXinghao);
			$this->assign("xinghaoArr",$xinghaoArr);
			$this->display("common_parameter");
		}
	}