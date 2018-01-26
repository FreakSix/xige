<?php
	namespace Home\Model;

	use Think\Model;
	
	class XgSupplierAccountModel extends Model{
		// 根据 订单产品ID 查询向供应商付款的信息
		public function getSupplierAccountInfo($order_product_id){
			$table = M("xg_supplier_account");
			$result = $table->where("order_product_id =".$order_product_id )->select();
			return $result;
		}
		//添加订单信息
		public function addSupplierAccountInfo($data){
			$res = M("xg_supplier_account")->data($data)->add();
			return $res;
		}
		// 根据客户回款记录ID查询客户回款信息
		public function getSupplierAccountById($id){
			$table = M("xg_supplier_account");
			$result = $table->where('id ='.$id)->find();
			return $result;
		}
		//修改客户回款信息
		public function updateSupplierAccountInfo($data,$id){
			$res = M("xg_supplier_account")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过客户回款信息ID删除对应信息
		public function deleteSupplierAccountInfoById($id){
			$result = M("xg_supplier_account")->where("id = ".$id)->delete();
			return $result;
		}

		//通过订单产品ID删除对应信息
		public function deleteSupplierAccountInfoByOrderProductId($order_product_id){
			$result = M("xg_supplier_account")->where("order_product_id = ".$order_product_id)->delete();
			return $result;
		}


		
	}