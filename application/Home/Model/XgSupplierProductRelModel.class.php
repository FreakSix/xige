<?php
	namespace Home\Model;
	use Think\Model;
	class XgSupplierProductRelModel extends Model {
		public function getRelByProductDetailId($product_id,$zhizhang_id,$size_id,$yinmian_id){
			$where = " where";
			if(!empty($product_id)){
				$where.=" product_id = {$product_id}";	
			}
			if(!empty($zhizhang_id)){
				$where.=" and zhizhang_id = {$zhizhang_id}";
			}
			if(!empty($size_id)){
				$where.=" and size_id = {$size_id}";
			}
			if(!empty($yinmian_id)){
				$where.=" and yinmian_id = {$yinmian_id}";
			}
			$sql = "select * from xg_supplier_product_rel ".$where;
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		//添加商品报价信息
		public function addProductPrice($data){
			$res = M("xg_supplier_product_rel")->data($data)->add();
			return $res;
		}
		//根据ID查询商品、联系人、价格信息
		public function getProductSupplierPriceInfoById($id){
			$sql = "select * from xg_supplier_product_rel where id = {$id}";
			$res = M()->query($sql);
			return $res;
		}
		//根据搜索信息查出商品的价格
		public function getProductPriceInfo($condition){
			$sql = "select * from xg_supplier_product_rel where {$condition}";
			// echo $sql;exit; 
			$res = M()->query($sql);
			return $res;
		}
		//修改商品分类信息
		public function updateProductPrice($data,$id){
			$res = M("xg_supplier_product_rel")->where("id = ".$id)->save($data);
			return $res;
		}

		
	}
	