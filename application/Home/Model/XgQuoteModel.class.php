<?php
	namespace Home\Model;
	use Think\Model;
	class XgQuoteModel extends Model {
		//根据搜索信息查出商品的价格
		public function getProductQuoteInfo($condition){
			$sql = "select * from xg_quote where {$condition}";
			// echo $sql;exit; 
			$res = M()->query($sql);
			return $res;
		}
		//添加订单信息
		public function addQuoteInfo($data){
			$res = M("xg_quote")->data($data)->add();
			return $res;
		}
		//条件查询订单信息
		public function quoteInfo($condition){ 
			$query = M("xg_quote");
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
		//符合条件的订单总数
		public function getQuoteCount($condition){
			$totalCount = M("xg_quote")->where($condition['where'])->count();
			return $totalCount;
		}
		//根据ID查询报价记录信息
		public function getQuoteInfoById($id){
			$quoteInfo = M("xg_quote")->where("id = ".$id)->find();
			return $quoteInfo; 
		}
		//修改报价记录信息
		public function updateQuoteInfo($data,$id){
			$res = M("xg_quote")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过订单ID删除报价记录信息
		public function deleteQuoteInfoById($id){
			$result = M("xg_quote")->where("id = ".$id)->delete();
			return $result;
		}

		
	}
	