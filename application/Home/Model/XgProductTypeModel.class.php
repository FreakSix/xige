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
		//查出商品名称
		public function getProductInfo($condition){
			$query = M("xg_product_type");
			if($condition['where']){
				// var_dump($condition['where']);
				$query = $query->where($condition['where']);
			}
			if($condition['order']){
				$query = $query->order($condition['order']);
			}
			if($condition['limit']){
				$query = $query->limit($condition['limit']['firstRow'],$condition['limit']['pageSize']);
			}

			$result = $query->select();
			return $result;
		}
		//符合条件的商品名称总数
		public function getProductCount($condition){
			$totalCount = M("xg_product_type")->where($condition)->count();
			return $totalCount;
		}
		//添加商品分类信息
		public function addProductTypeInfo($data){
			$res = M("xg_product_type")->data($data)->add();
			return $res;
		}
		//修改商品分类信息
		public function updateProductTypeInfo($data,$id){
			$res = M("xg_product_type")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过 id 删除商品信息
		public function deleteProductById($id){
			$res = M("xg_product_type")->where("id = ".$id)->delete();
			return $res;
		}
		//通过 pid 删除商品信息
		public function deleteProductByPid($pid){
			$res = M("xg_product_type")->where("pid = ".$pid)->delete();
			return $res;
		}


	}
	