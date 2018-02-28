<?php
	namespace Home\Model;
	use Think\Model;
	class XgOrderProductModel extends Model {
		// 获取全部订单产品信息
		public function getAllOrderProduct(){
			$sql = "select * from xg_order_product order by add_time asc";
			$result = M()->query($sql);
			return $result;
		}
		// 根据id获取订单产品数据
		public function getOrderProductById($id){
			$result = M("xg_order_product")->where("id =".$id)->find();
			return $result;
		}
		//添加订单信息
		public function addOrderProductInfo($data){
			$res = M("xg_order_product")->data($data)->add();
			return $res;
		}
		//根据ID查询订单商品信息
		public function getOrderProductInfoById($id){
			$orderProductInfo = M("xg_order_product")->where("id = ".$id)->find();
			return $orderProductInfo; 
		}
		// 根据订单编号查询订单产品信息
		public function getProductInfoByOrderNum($order_num){
			$sql = "select order_num,product_name_id,product_name,product_model,product_spec_id_str,special_technologyh_id_str,num,end_price,end_money from xg_order_product where order_num=".$order_num;
			$result = M()->query($sql);
			return $result;
		}
		//修改订单商品信息
		public function updateOrderProductInfo($data,$id){
			$res = M("xg_order_product")->where("id = ".$id)->save($data);
			return $res;
		}
		//条件查询订单信息
		public function orderProductInfo($condition){ 
			$query = M("xg_order_product");
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
		//符合条件的订单产品总数
		public function getOrderProductCount($condition){
			$totalCount = M("xg_order_product")->where($condition['where'])->count();
			return $totalCount;
		}

		
		//根据搜索信息查出商品的价格
		public function getOrderProductInfo($condition){
			$sql = "select * from xg_order_product where {$condition}";
			// echo $sql;exit; 
			$res = M()->query($sql);
			return $res;
		}
		//通过ID删除对应信息
		public function deleteOrderProductInfoById($id){
			$result = M("xg_order_product")->where("id = ".$id)->delete();
			return $result;
		}

		//通过 条件 删除对应信息 
		public function deleteOrderProductInfoByCondition($condition){
			$result = M("xg_order_product")->where($condition['where'])->delete();
			return $result;
		}
		// 根据订单ID删除产品信息
		public function deleteOrderProductByOrderId($order_id){
			$result = M("xg_order_product")->where("order_id = ".$order_id)->delete();
			return $result;
		}
	}
	