<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductSpecModel extends Model {
		//根据id查询商品规格信息
		public function getSpecById($id){
			$sql = "select * from xg_product_spec where id = {$id}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		//
		public function getProductSpecInfo($condition){
			$query = M("xg_product_spec");
			if($condition['where']){
				// var_dump($condition['where']);
				$query = $query->where($condition['where']);
			}
			if($condition['order']){
				$query = $query->order($condition['order']);
			}
			if($condition['limit']){
				$query = $query->limit($condition['limit']['firstRow'],$condition['limit']['pageSize']);
			}

			$result = $query->select();
			return $result;
		}
		//符合条件的商品规格总数
		public function getProductSpecCount($condition){
			$totalCount = M("xg_product_spec")->where($condition)->count();
			return $totalCount;
		}
		//添加商品规格信息
		public function addSpecInfo($spec){
			$res = M("xg_product_spec")->data($spec)->add();
			return $res;
		}
		//修改商品规格信息
		public function updateProductSpecInfo($data,$id){
			$res = M("xg_product_spec")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过 id 删除商品规格信息
		public function deleteProductSpecById($id){
			$result = M("xg_product_spec")->where("id = ".$id)->delete();
			return $result;
		}
		//通过 product_model_id 删除商品规格信息
		public function deleteProductSpecByProduct_Model_Id($product_model_id){
			$result = M("xg_product_spec")->where("product_model_id = ".$product_model_id)->delete();
			return $result;
		}
		//通过 product_model_id 删除商品规格信息
		public function deleteProductSpecByProduct_Id($product_id){
			$result = M("xg_product_spec")->where("product_id = ".$product_id)->delete();
			return $result;
		}
		//通过 parameter_id 删除商品规格信息
		public function deleteProductSpecByParameter_id($parameter_id){
			$result = M("xg_product_spec")->where("parameter_id = ".$parameter_id)->delete();
			return $result;
		}
		//根据输入信息查出商品的规格信息
		public function getProductSpecInfoByWhere($condition){
			$sql = "select * from xg_product_spec where {$condition}";
			// echo $sql;exit; 
			$res = M()->query($sql);
			return $res;
		}
		
	}