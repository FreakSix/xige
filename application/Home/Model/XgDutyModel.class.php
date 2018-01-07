<?php
	namespace Home\Model;
	use Think\Model;
	class XgDutyModel extends Model{
		public $trueTableName = "xg_duty";
		/**
		 * 根据职位的id获取职位
		 * @param unknown $id
		 */
		public function getDutyById($id){
			$result = M("xg_duty")->where("id = ".$id)->find();
			return $result;
		}
		/**
		 * 获取所有的职位
		 */
		public function getDutyAll() {
			$result = M("xg_duty")->select();
			return $result;
		}
		/**
		 * 新增加职位
		 * @param unknown $depart_id
		 * @param unknown $duty_name
		 */
		public function addNewDuty($data){
			$feilds=array_keys($data);
			$sql="insert into xg_duty (".implode(',',$feilds).") values('".implode("','",$data)."')";
			$result = M()->execute($sql);
			return $result;
		}
		/**
		 * 修改职位信息
		 */
		public function updateDutyById($id,$depart_id,$duty_name){
			$sql = "update xg_duty set ";
			if(!empty($depart_id)){
				$sql .= "depart_id = '{$depart_id}',";
			}
			if(!empty($duty_name)){
				$sql .= "duty_name = '{$duty_name}',";
			}
			$sql =  rtrim($sql,",");
			$sql .= " where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		/**
		 * 修改权限的内容
		 */
		public function updateDutyPower($id,$power){
			$sql = "update xg_duty set power = '{$power}' where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		
		/**
		 * 根据部门的id获取对应的职位信息
		 */
		public function getDutyByDepartId($deaprt_id){
			$result = M("xg_duty")->where("depart_id = ".$deaprt_id)->select();
			return $result;
		}
	}