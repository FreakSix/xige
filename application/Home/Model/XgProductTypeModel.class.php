<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductTypeModel extends Model {
		public function getProductTypeByPid($pid){
			$sql = "select * from xg_product_type where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		
	}
	