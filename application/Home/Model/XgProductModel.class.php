<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductModel extends Model {
		public function getProductByPid($pid){
			$sql = "select * from xg_product where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		
	}
	