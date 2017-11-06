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
		

		
	}