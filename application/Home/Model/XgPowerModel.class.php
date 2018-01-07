<?php
	namespace Home\Model;
	use Think\Model;
	class XgPowerModel extends Model {
		public function getAllPower(){
			$sql = "select * from xg_power";
			$res = M()->query($sql);
			return $res;
		}
		
		
	}
	