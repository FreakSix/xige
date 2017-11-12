<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductZhizhangModel extends Model {
		public function getProductZhizhangByPid($pid){
			$sql = "select * from xg_product_zhizhang where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		
	}
	