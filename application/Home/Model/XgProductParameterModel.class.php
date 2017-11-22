<?php
	namespace Home\Model;
	use Think\Model;
	class XgProductParameterModel extends Model {
		// 查询商品参数的所有信息
		public function getProductParameter(){
			
			$sql = "select * from xg_product_parameter order by id desc";
			$parameter = M()->query($sql);
			return $parameter;
		}
		//根据id查询商品参数信息
		public function getProductParameterById($id){
			$sql = "select * from xg_product_parameter where id = {$id}";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		//根据pid进行in条件查询
		public function getProductParameterByIdWhereIn($id){
			$sql = "select * from xg_product_parameter where id in (".$id.")";
			$typeArr = M()->query($sql);
			return $typeArr;
		}
		//添加商品规格名称信息
		public function addProductParameterInfo($data){
			$res = M("xg_product_parameter")->data($data)->add();
			return $res;
		}
		//修改商品规格信息
		public function updateProductParameterInfo($data,$id){
			$res = M("xg_product_parameter")->where("id = ".$id)->save($data);
			return $res;
		}
		//通过 id 删除商品规格信息
		public function deleteProductParameterById($id){
			$result = M("xg_product_parameter")->where("id = ".$id)->delete();
			return $result;
		}

	}
	