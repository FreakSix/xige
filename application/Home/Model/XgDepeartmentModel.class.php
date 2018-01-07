<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgDepeartmentModel extends Model{
		
		public $trueTableName = "xg_depeartment";

		//查询单条客户信息
		public function getDepeartmentById($id){
			$result = M("xg_depeartment")->where("id = ".$id)->find();
			return $result;
		}

		/**
		 * 查询所有的部门
		 */
		public function getDepeartAll(){
			$result = M("xg_depeartment")->select();
			return $result;
		}
		/**
		 * 保存新添加的部门名称
		 * @param unknown $name
		 */
		public function saveNewAddDepartment($name){
		    $sql="insert into xg_depeartment (depart_name) values ('{$name}')";
		    $result = M()->execute($sql);
		    return $result;
		}
		/**
		 * 修改部门名称 
		 */
		public function saveUpdateDepartName($id,$depart_name){
			$sql = "update xg_depeartment set depart_name = '{$depart_name}' where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		/**
		 * 删除部门  根据id
		 */
		public function deleteDepartmentById($id){
			$result = M("xg_depeartment")->where("id = ".$id)->delete();
			return $result;
		}
	}