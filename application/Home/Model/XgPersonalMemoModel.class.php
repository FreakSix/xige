<?php 
	namespace Home\Model;
	use Think\Model;
	/**
	 **操作个人备忘录信息
	 **/
	Class XgPersonalMemoModel extends Model{
		// 获取今天以后公共备忘录信息
		public function getPersonalMemoByDate($date){
			$table = M("xg_personal_memo");
			$result = $table->where("memo_time > ".$date)->order("memo_time asc,memo_level desc")->select();
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