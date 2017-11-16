<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductTypeModel extends Model {
		// 查询全部产品分类
		public function getProductType(){
			$pid = 0;
			$sql = "select * from xg_product_type where pid = {$pid}";
			$type = M()->query($sql);
			return $type;
		}
		// 根据pid查询产品分类下对应的产品
		public function getProductTypeByPid($pid){
			$sql = "select * from xg_product_type where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		// 根据id查对应产品的名称
		public function getProductName($id){
			$sql = "select type_name from xg_product_type where id = {$id}";
			$result = M()->query($sql);
			return $result;
		}
		// 根据id查对应的产品全部信息
		public function getProduct($id){
			$sql = "select * from xg_product_type where id = {$id}";
			$result = M()->query($sql);
			return $result;
		}
		// 查找Xg_product_type表中对应产品的供应商id所组成的字符串
		public function getSupplierIdStr($id){
			$sql = "select supplier_id_str from xg_product_type where id = {$id}";
			$result = M()->query($sql);
			return $result;
		}
		// 添加或修改Xg_product_type表中对应产品的供应商id所组成的字符串
		public function updateSupplierIdStr($id,$supplierIdStr){
			$sql = "update xg_product_type set supplier_id_str = '{$supplierIdStr}' where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}

	}
	