<?php
	namespace Home\Model;

	use Think\Model;
	
	class XgCustomerAccountModel extends Model{
		// 根据订单ID查询客户回款信息
		public function getCustomerAccountByOrderId($order_id){
			$table = M("xg_customer_account");
			$result = $table->where('order_id ='.$order_id)->select();
			return $result;
		}
		//添加订单信息
		public function addCusromerAccountInfo($data){
			$res = M("xg_customer_account")->data($data)->add();
			return $res;
		}
		// 根据客户回款记录ID查询客户回款信息
		public function getCustomerAccountById($id){
			$table = M("xg_customer_account");
			$result = $table->where('id ='.$id)->find();
			return $result;
		}
		//修改客户回款信息
		public function updateCustomerAccountInfo($data,$id){
			$res = M("xg_customer_account")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过客户回款信息ID删除对应信息
		public function deleteCustomerAccountInfoById($id){
			$result = M("xg_customer_account")->where("id = ".$id)->delete();
			return $result;
		}


		
	}