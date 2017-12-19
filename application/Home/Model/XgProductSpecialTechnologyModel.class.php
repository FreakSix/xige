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
		//根据id查询特殊工艺信息
		public function getProductSpecialTechnologyById($id){
			$data = M("xg_product_special_technology")->where("id = ".$id)->find();
			return $data;
		}



	}
	