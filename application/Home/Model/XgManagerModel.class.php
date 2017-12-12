<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgManagerModel extends Model{
		
		public $trueTableName = "xg_manager";

		//根据用户名获取用户的信息
		public function getManageInfoByUsername($username){
			$result = M("xg_manager")->where("username = '{$username}'")->find();
			return $result;
		}
		//根据用户名获取用户的信息
		public function getManageInfoByTel($tel){
			$result = M("xg_manager")->where("tel = '{$tel}'")->find();
			return $result;
		}
		
		//条件查询客户信息
		

		//查询客户等级信息
		
		

		//删除客户信息
		public function delete($customer_id){
			$result = M("xg_customer")->where("id = ".$customer_id)->delete();
			return $result;
		}

		
	}