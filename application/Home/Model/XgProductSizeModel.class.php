<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductSizeModel extends Model {
		public function getProductSizeByPid($pid){
			$sql = "select * from xg_product_size where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		
	}
	