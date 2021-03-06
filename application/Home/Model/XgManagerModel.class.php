<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgManagerModel extends Model{
		
		public $trueTableName = "xg_manager";

		//根据用户名获取用户的信息
		public function getManageInfoByUsername($username){
			$result = M("xg_manager")->where("username = '".$username."'")->find();
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
		//根据用户的id获取用户的信息
		public function getManagerInfoById($id){
		    $result = M("xg_manager")->where("id = '{$id}'")->find();
		    return $result;
		}
		//根据部门id获取该部门有多少人数
		public function getDepartManagerCount($depart_id){
		    $sql = "select count(1) from xg_manager where department_id = {$depart_id}";
		    $result = M()->query($sql);
		    return $result[0]["count(1)"];
		}
		
		//添加新用户
		public function addNewManager($data){
			// $feilds=array_keys($data);
			// $sql="insert into xg_manager (".implode(',',$feilds).") values('".implode("','",$data)."')";
			// $result = M()->execute($sql);
			// $userId = M()->getLastInsID();
			// return $userId;
			$result =M("xg_manager")->data($data)->add();
			return $result;
		}
		//保存修改的员工信息
		public function updateEditManager($id,$data){
			$sql = "update xg_manager set ";
			foreach($data as $k=>$v){
				$sql .= "{$k} = '{$v}',";
			}
			$sql =  rtrim($sql,",");
			$sql .= " where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		//删除员工信息
		public function delManagerInfo($id){
			$result = M("xg_manager")->where("id = ".$id)->delete();
			return $result;
		}
		//注销员工账号
		public function deleteAccount($id){
			$sql = "update xg_manager set state = 0 where id = ".$id;
			$result = M()->execute($sql);
			return $result;
		}
		//开通员工账号
		public function kaitongAccount($id){
			$sql = "update xg_manager set state = 1 where id = ".$id;
			$result = M()->execute($sql);
			return $result;
		}
		// 修改sessionId
		public function updateSessionId($sessionId,$id){
			$sql = "update xg_manager set session_id = '{$sessionId}' where id = {$id}";
			$result = M()->execute($sql);
			return $result;
		}
		// 获取sessionId
		public function getSessionId($id){
			$sql = "select session_id from xg_manager where id = {$id}";
			$result = M()->query($sql);
			return $result;
		}
		//根据条件查出管理员信息
		public function getManagerInfo($condition){
			$query = M("xg_manager");
			if($condition['where']){
				// var_dump($condition['where']);
				$query = $query->where($condition['where']);
			}
			if($condition['order']){
				$query = $query->order($condition['order']);
			}
			if($condition['limit']){
				$query = $query->limit($condition['limit']['firstRow'],$condition['limit']['pageSize']);
			}

			$result = $query->select();
			return $result;
		}
	}