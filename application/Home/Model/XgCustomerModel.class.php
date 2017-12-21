<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgCustomerModel extends Model{
		
		public $trueTableName = "xg_customer";

		//查询单条客户信息
		public function getCustomerInfo($customer_id){
			$result = M("xg_customer")->where("id = ".$customer_id)->find();
			return $result;
		}

		//条件查询客户信息
		public function getCustomerInfos($condition){
			$query = M("xg_customer");
			if($condition['field']){
				$query = $query->field($condition['field']);
			}
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

		//查询客户等级信息
		public function getCustomerLevelInfo($level){
			$customer_level = M("xg_customer_level");
			if($level){
				$result = $customer_level->where("level = ".$level)->order("level asc")->find();
			}else{
				$result = $customer_level->order("level asc")->select();
			}
			return $result;
		}

		//客户信息处理
		public function dellCustomerInfo($post,$type){
			if($post){
				$time = time();
				$customers['cname']=$post['cname'];
				$customers['type_id']=$post['customer_type'];
				$customers['rank']=$post['rank'];
				$customers['local_procode']=$post['province_name'];
				$customers['local_citycode']=$post['city_name'];
				$customers['local_areacode']=$post['area_name'];
				$customers['local_address']=$post['local_address'];
				$customers['code']=$post['code'];
				$customers['remarks']=$post['remarks'];
				$customers['invoice_num']=$post['invoice_num'];
				$customers['invoice_tel']=$post['invoice_tel'];
				$customers['bank_name']=$post['bank_name'];
				$customers['bank_num']=$post['bank_num'];
				$customers['bank_address']=$post['bank_address'];
				$customers['utime']=$time;

				$customer_model = M("xg_customer");
				if($type == 'update'){
					$result_1 = $customer_model->where("id = ".$post['customer_id'])->save($customers);
				}else if($type == 'add'){
					$customers['ctime']=$time;
					$result_1 = $customer_model->data($customers)->add();
				}
			}
			return $result_1;
		}

		//删除客户信息
		public function delete($customer_id){
			$result = M("xg_customer")->where("id = ".$customer_id)->delete();
			return $result;
		}

		public function getLevelInfoByLevel($leve){
			$table = M("xg_customer_level");
			$result = $table->where('level ='.$leve)->find();
			return $result;
		}

		//符合条件的客户总数
		public function getCustomerCount($condition){
			$totalCount = M("xg_customer")->where($condition['where'])->count();
			return $totalCount;
		}

		
	}