<?php
	namespace Home\Model;

	use Think\Model;
	
	class XgSupplierModel extends Model{

		

		// 添加供应商信息
		public function addSupplierInfo($supplier){
			$table = M("xg_supplier");
			$result = $table->data($supplier)->add();

			return $result;
		}
		
		// 查询供应商信息
		public function getSupplierInfo($supplier_id){
			$table = M("xg_supplier");
			$result = $table->where('supplier_id ='.$supplier_id)->find();
			return $result;
		}

		// 更新供应商信息
		public function updateSupplierInfo($supplierId,$supplier){
			$table = M("xg_supplier");
			$result = $table->where("supplier_id = ".$supplierId)->save($supplier);
			return $result;
		}
		//
		public function getProductSupplierInfo($condition){
			$query = M("xg_supplier");
			if($condition['where']){
				$query = $query->where($condition['where']);
			}
			$result = $query->select();
			return $result;
		}

		//条件查询供应商信息
		public function supplierInfo($condition){ 
			$query = M("xg_supplier");
			if($condition['where']){
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
		//符合条件的供应商总数
		public function getSupplierCount($condition){
			$totalCount = M("xg_supplier")->where($condition['where'])->count();
			return $totalCount;
		} 

		//删除供应商信息
		public function deleteBySupplierId($supplier_id){
			$result = M("xg_supplier")->where("supplier_id = ".$supplier_id)->delete();
			return $result;
		}

		
	}