<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductModel extends Model {
		public function getProductByPid($pid){
			$sql = "select * from xg_product where pid = {$pid}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		//根据id查询商品型号信息
		public function getProductById($id){
			$sql = "select * from xg_product where id = {$id}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		// 根据产品型号名称查询信息
		public function getProductModelByName($product_model){
			$result = M("xg_product")->where("name = '".$product_model."'")->select();
			return $result;
		}
		//查出商品对应的商品型号
		public function getProductModelInfo($condition){
			$query = M("xg_product");
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
		//符合条件的商品名称总数
		public function getProductModelCount($condition){
			$totalCount = M("xg_product")->where($condition)->count();
			return $totalCount;
		}
		//根据pid进行in条件查询
		public function getProductByPidWhereIn($pid){
			dump($pid);
			$sql = "select * from xg_product where pid in (".$pid.")";
			var_dump($sql);
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		//添加商品型号信息
		public function addProductModelInfo($data){
			$res = M("xg_product")->data($data)->add();
			return $res;
		}
		//修改商品型号信息
		public function updateProductModelInfo($data,$id){
			$res = M("xg_product")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过 id 删除商品型号信息
		public function deleteProductModelById($id){
			$res = M("xg_product")->where("id = ".$id)->delete();
			return $res;
		}
		//通过 pid 删除商品型号信息
		public function deleteProductModelByPid($pid){
			$res = M("xg_product")->where("pid = ".$pid)->delete();
			return $res;
		}
		
	}
	