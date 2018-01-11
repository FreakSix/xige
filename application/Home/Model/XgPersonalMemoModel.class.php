<?php 
	namespace Home\Model;
	use Think\Model;
	/**
	 **操作个人备忘录信息
	 **/
	Class XgPersonalMemoModel extends Model{
		// 获取今天以后个人备忘录信息
		public function getPersonalFutureMemo($date,$user){
			$sql = " select * from xg_personal_memo where manager_name = '".$user."' AND memo_time >= ".$date." order by memo_time asc,memo_level desc";
			$result = M()->query($sql);
			// $table = M("xg_personal_memo");
			// $result = $table->where("manager_name = '".$user."'")->where("memo_time >= ".$date)->order("memo_time asc,memo_level desc")->select();
			return $result;
		}
		// 获取今天以前的个人备忘录信息
		public function getPersonalPastMemo($date,$user){
			$sql = " select * from xg_personal_memo where manager_name = '".$user."' AND memo_time < ".$date." order by memo_time desc,memo_level desc";
			$result = M()->query($sql);
			$table = M("xg_personal_memo");
			// $result = $table->where("manager_name = '".$user."'")->where("memo_time < ".$date)->order("memo_time asc,memo_level desc")->select();
			return $result;
		}
		// 根据备忘录ID获取该条备忘录信息
		public function getPersonalMemoById($id){
			$table = M("xg_personal_memo");
			$result = $table->where("id = ".$id)->find();
			return $result;
		}
		// 根据ID修改备忘录信息
		public function updatePersonalMemo($id,$data){
			$table = M("xg_personal_memo");
			$result = $table->where("id = ".$id)->save($data);
			return $result;
		}
		// 添加个人备忘录
		public function addPersonalMemo($data){
			$table = M("xg_personal_memo");
			$result = $table->data($data)->add();
			return $result;
		}
		// 根据备忘录ID删除该条备忘录信息
		public function deletePersonalMemo($id){
			$table = M("xg_personal_memo");
			$result = $table->where("id = ".$id)->delete();
			return $result;
		}
		// 根据备忘录内容查询
		public function getPersoalMemoByEvent($memoEvent){
			$result = M("xg_personal_memo")->where("memo_event = '".$memoEvent."'")->find();
			return $result;
		}
	}

 ?>