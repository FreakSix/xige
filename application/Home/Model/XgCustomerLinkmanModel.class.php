<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgCustomerLinkmanModel extends Model{
		
		public $trueTableName = "xg_customer_linkman";

		//通过客户ID删除联系人信息
		public function delete($customer_id){
			$result = M("xg_customer_linkman")->where("c_id = ".$customer_id)->delete();
			return $result;
		}

		//通过联系人ID删除联系人信息
		public function deletesByLinkmanId($linkman_id){
			$result = M("xg_customer_linkman")->where("id = ".$linkman_id)->delete();
			return $result;
		}

		//查询客户联系人信息
		public function getCustomerLinkInfo($link_id){
			$customer_link = M("xg_customer_linkman");
			if($link_id){
				$result = $customer_link->where("c_id = ".$link_id)->select();
			}else{
				$result = $customer_link->select();
			}
			return $result;
		}
		//根据id查询客户联系人信息
		public function getCustomerLinkInfoByid($id){
			$customer_link = M("xg_customer_linkman");
			$result = $customer_link->where("id = ".$id)->find();
			return $result;
		}

		//客户联系人信息修改
		public function updateCustomerLinkInfo($post){
			if($post['link_man']){
				foreach ($post['link_man'] as $k => $v) {
					$customer_link['c_id']=$post['customer_id'];    //公司的id
					$customer_link['name']=$v['name'];
					$customer_link['phone']=$v['phone'];
					$customer_link['address']=$v['address'];
					
					$res = M("xg_customer_linkman")->where("id = ".$v['link_id'])->save($customer_link);
					$result_2[$v['name']] = $res;
				}
			}
			return $result_2;
		}

		//单条客户联系人信息添加
		public function addCustomerLinkman($post){
			
			$customer_link['c_id']=$post['customer_id'];    //公司的id
			$customer_link['name']=$post['link_name'];
			$customer_link['phone']=$post['link_phone'];
			$customer_link['address']=$post['link_address'];
			
			$res = M("xg_customer_linkman")->data($customer_link)->add();
			
			return $res;
		}

		//客户联系人信息添加
		public function addCustomerLinkInfo($post,$res_id){
			if($res_id > 0){
				for($i=0;$i<=$post['select_num_hide'];$i++){
					$cus_link['c_id']=$res_id;    //公司的id
					$cus_link['name']=$post['link_name'.$i];
					$cus_link['phone']=$post['link_phone'.$i];
					$cus_link['address']=$post['link_address'.$i];
					
					$res = M("xg_customer_linkman")->data($cus_link)->add();
					$res_2[$post['link_name'.$i]] = $res;
				}
			}
			return $res_2;
		}

		
	}