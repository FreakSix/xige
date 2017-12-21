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
		//删除客户信息
		public function delete($customer_id){
			$result = M("xg_customer")->where("id = ".$customer_id)->delete();
			return $result;
		}
		//修改密码
		public function updatePassword($id,$new_pwd){
			$sql = "update xg_manager set password = '{$new_pwd}' where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		//获取所有的管理员
		public function getAllManager(){
			$result = M("xg_manager")->select();
			return $result;
		}
	}