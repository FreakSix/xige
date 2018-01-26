<?php
	namespace Home\Model;
	use Think\Model;
	class XgOrderStatusModel extends Model {
		//查询订单状态信息
		public function getOrderStatusInfo(){
			$orderStatus = M("xg_order_status")->select();
			return $orderStatus; 
		}
		//根据ID查询订单状态信息
		public function getOrderStatusInfoById($id){
			$orderStatus = M("xg_order_status")->where("id = ".$id)->find();
			return $orderStatus; 
		}
		
		
	}
	