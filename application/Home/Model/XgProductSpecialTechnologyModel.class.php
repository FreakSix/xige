<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductSpecialTechnologyModel extends Model {
		// 查询全部特殊工艺信息
		public function getProductSpecialTechnology(){
			$sql = "select * from xg_product_special_technology";
			$typeArr = M()->query($sql);
			return $typeArr;
		}



	}
	