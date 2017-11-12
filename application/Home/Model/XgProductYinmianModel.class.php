<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductYinmianModel extends Model {
		public function getProductYinmianByPid($pid){
			$sql = "select * from xg_product_yinmian where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		
	}
	