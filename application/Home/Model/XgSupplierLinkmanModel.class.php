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
		// 按供应商ID修改联系人信息
		public function updateSupplierLinkman($supplierId,$linkmanId,$supplierLinkman){
			$condition['supplier_id'] = $supplierId;
			$condition['id'] = $linkmanId;
			$table = M("xg_supplier_linkman");
			$result = $table->where($condition)->save($supplierLinkman);
			return $result;
		}
		//根据电话查询对应的联系人信息
		public function getLinkmanInfoByPhone($phone){
			$result = M("xg_supplier_linkman")->where("linkman_phone = ".$phone)->find();
			return $result;
		}
		//根据联系人姓名查询对应的联系人信息
		public function getLinkmanInfoByName($name){
			$result = M("xg_supplier_linkman")->where("linkman_name = ".$name)->find();
			return $result;
		}

		//根据供应商ID删除联系人信息
		public function deleteBySupplierId($supplier_id){
			$result = M("xg_supplier_linkman")->where("supplier_id = ".$supplier_id)->delete();
			return $result;
		}
		//根据联系人ID删除联系人信息
		public function deleteById($id){
			$result = M("xg_supplier_linkman")->where("id = ".$id)->delete();
			return $result;
		}
	}