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
	}