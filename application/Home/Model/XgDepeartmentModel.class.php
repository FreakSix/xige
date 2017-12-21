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

		
	}