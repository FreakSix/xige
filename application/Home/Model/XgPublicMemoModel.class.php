<?php 
	namespace Home\Model;
	use Think\Model;
	/**
	 **操作公共备忘录信息
	 **/
	Class XgPublicMemoModel extends Model{
		// 获取今天以后公共备忘录信息
		public function getPublicMemoByDate($date){
			$table = M("xg_public_memo");
			$result = $table->where("memo_time >= ".$date)->order("memo_time asc,memo_level desc")->select();
			return $result;
		}
		// 根据备忘录ID获取该条备忘录信息
		public function getPublicMemoById($id){
			$table = M("xg_public_memo");
			$result = $table->where("id = ".$id)->find();
			return $result;
		}
		// 根据ID修改备忘录信息
		public function updatePublicMemo($id,$data){
			$table = M("xg_public_memo");
			$result = $table->where("id = ".$id)->save($data);
			return $result;
		}
		// 添加公共备忘录
		public function addPublicMemo($data){
			$table = M("xg_public_memo");
			$result = $table->data($data)->add();
			return $result;
		}
		// 根据备忘录ID删除该条备忘录信息
		public function deletePublicMemo($id){
			$table = M("xg_public_memo");
			$result = $table->where("id = ".$id)->delete();
			return $result;
		}
		//条件查询备忘录信息
		public function publicMemoInfo($condition){ 
			$query = M("xg_public_memo");
			if($condition['where']){
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
		//符合条件的备忘录总数
		public function getPublicMemoCount($condition){
			$totalCount = M("xg_public_memo")->where($condition['where'])->count();
			return $totalCount;
		}
	}

 ?>