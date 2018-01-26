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
		// 添加特殊工艺信息
		public function addSpecialTec($name){
			$result = M("xg_product_special_technology")->data($name)->add();
			return $result;
		}
		// 根据特殊工艺名称查数据
		public function getSpecialTecByName($name){
			$result = M("xg_product_special_technology")->where("name = '".$name."'")->find();
			return $result;
		}
		// 根据id进行in查询
		public function getSpecialByWhereIdIn($id){
			$sql = "select * from xg_product_special_technology where id in (".$id.")";
			$result = M()->query($sql);
			return $result;
		}
		// 根据Id修改产品特殊工艺名称
		public function updateSpecialTec($id,$data){
			$result = M("xg_product_special_technology")->where("id = ".$id)->save($data);
			return $result;
		}
		// 删除产品特殊工艺名称
		public function deleteSpecialTec($id){
			$result = M("xg_product_special_technology")->where("id = ".$id)->delete();
			return $result;
		}

	}
	