<?php
	namespace Home\Model;
	use Think\Model;
	class XgDutyModel extends Model{
		public $trueTableName = "xg_duty";
		public function getDutyById($id){
			$result = M("xg_duty")->where("id = ".$id)->find();
			return $result;
		}
	}