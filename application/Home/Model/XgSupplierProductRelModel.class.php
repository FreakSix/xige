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
		
	}
	