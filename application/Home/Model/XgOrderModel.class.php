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
		//修改订单信息
		public function updateOrderInfo($data,$id){
			$res = M("xg_order")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过订单ID删除订单信息
		public function deleteOrderInfoById($id){
			$result = M("xg_order")->where("id = ".$id)->delete();
			return $result;
		}
		// 根据订单id查询客户id
		public function getCustomerIdById($id){
			$sql = " select customer_id from xg_order where id = ".$id;
			$result = M()->query($sql);
			return $result[0];
		}
		// 根据订单id查数据
		public function getInfoById($id){
			$sql = "select order_id,add_time,customer_name,product_model,num,end_price,end_money from xg_order where id = ".$id;
			$result = M()->query($sql);
			return $result;
		}
		// 查询订单录入人为当前登录用户且交货时间为明天的订单
		public function getOrderByTime($tomorrow,$user){
			$sql = "select order_id,customer_name,product_model from xg_order where trade_time = ".$tomorrow." AND manager_name = '".$user."' AND order_status != 4";
			$result = M()->query($sql);
			return $result;
		}
		//第一条订单数据查询
		public function getOrderInfoByOrder($condition){
			$table = M("xg_order");
			$result = $table->order("id ".$condition)->find();
			return $result;
		}
		//根据id进行in条件查询
		public function getProductParameterByIdWhereIn($id){
			$sql = "select * from xg_product_parameter where id in (".$id.")";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		
	}
	