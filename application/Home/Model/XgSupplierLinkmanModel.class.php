<?php
	namespace Home\Model;
	use Think\Model;
	// 供应商联系人操作
	class XgSupplierLinkmanModel extends Model {
		// 添加联系人
		public function addSupplierLinkman($linkman){
			$table = M("xg_supplier_linkman");
			$result = $table->data($linkman)->add();
			return $result;
		}
		// 按供应商ID查一条联系人信息
		public function findLinkman($supplierId){
			$table = M("xg_supplier_linkman");
			$linkmanInfo = $table->where("supplier_id = ".$supplierId)->find();
			return $linkmanInfo;
		}
		// 按供应商ID查所有联系人信息
		public function findAllLinkman($supplierId){
			$table = M("xg_supplier_linkman");
			$linkmanInfo = $table->where("supplier_id = ".$supplierId)->select();
			return $linkmanInfo;
		}
	}