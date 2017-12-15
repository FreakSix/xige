<?php
	namespace Home\Model;
	use Think\Model;
	class XgOrderModel extends Model {
		//根据搜索信息查出商品的价格
		public function getProductOrderInfo($condition){
			$sql = "select * from xg_order where {$condition}";
			// echo $sql;exit; 
			$res = M()->query($sql);
			return $res;
		}
		//添加订单信息
		public function addOrderInfo($data){
			$res = M("xg_order")->data($data)->add();
			return $res;
		}
		//条件查询订单信息
		public function orderInfo($condition){ 
			$query = M("xg_order");
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
		public function getOrderCount($condition){
			$totalCount = M("xg_order")->where($condition['where'])->count();
			return $totalCount;
		}
		//根据ID查询订单信息
		public function getOrderInfoById($id){
			$orderInfo = M("xg_order")->where("id = ".$id)->find();
			return $orderInfo; 
		}

		
	}
	