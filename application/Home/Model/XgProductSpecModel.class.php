<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductSpecModel extends Model {
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
		
	}