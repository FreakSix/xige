<?php
	namespace Home\Model;

	use Think\Model;
	
	class XgCustomerAccountModel extends Model{
		// 根据客户等级
		public function getLevelInfoByLevel($leve){
			$table = M("xg_customer_level");
			$result = $table->where('level ='.$leve)->find();
			return $result;
		}
		// 根据ID查询客户等级信息
		public function getLevelInfoById($id){
			$result = M("xg_customer_level")->where('id ='.$id)->find();
			return $result;
		}
		// 查询全部的客户等级信息
		public function getCustomerLevelInfo(){
			$result = M("xg_customer_level")->select();
			return $result;
		}


		
	}