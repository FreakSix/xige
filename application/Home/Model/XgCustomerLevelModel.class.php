<?php
	namespace Home\Model;

	use Think\Model;
	
	class XgCustomerLevelModel extends Model{
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
		//添加客户等级
		public function addLevel($data){
			$feilds=array_keys($data);
			$sql="insert into xg_customer_level (".implode(',',$feilds).") values('".implode("','",$data)."')";
			$result = M()->execute($sql);
			return $result;
		}
		//修改客户等级
		public function updateCustomerLevel($id,$name,$level){
			$sql = "update xg_customer_level set ";
			if(!empty($name)){
				$sql .= "name = '{$name}',";
			}
			if(!empty($level)){
				$sql .= "level = '{$level}',";
			}
			$sql =  rtrim($sql,",");
			$sql .= " where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		//删除客户等级
		public function deleteCustomerRank($id){
			$result = M("xg_customer_level")->where("id = ".$id)->delete();
			return $result;
		}
	}