<?php
	namespace Home\Controller;
	
	class CustomerController extends BaseController
	{
		
		public function index(){

			//总记录数
	 		$totalCount = M("xg_customer")->count();
	 		$pageSize = 1;
	 		//实例化分页类
	 		$page = new \Think\Page($totalCount,$pageSize);
	 		//获取起始位置
	 		$firstRow = $page->firstRow;
	 		//获取分页结果
	 		$pageStr = $page->show();
	 		//总页数
	 		$totalPage = $page->totalPages;
	 		//查询客户公司信息
			$customer = M("xg_customer");
			$customerInfo = $customer->order("id desc")->limit("$firstRow,$pageSize")->select();

			$level = M("xg_customer_level");
			$linkman = M("xg_customer_linkman");
			foreach ($customerInfo as $k => $v) {
				//得到客户等级名称
				$levelInfo = $level->where("level = ".$v["rank"])->find();
				$customerInfo[$k]["level_name"] = $levelInfo["name"];
				//得到省份名称
				$provinceInfo = $this->getProvince($v["local_procode"]);
				//得到城市名称
				$cityInfo = $this->getCity($v["local_citycode"]);
				//得到地区名称
				$areaInfo = $this->getArea($v["local_areacode"]);
				//拼接所在地区名
				if ($provinceInfo["0"]["name"] == $cityInfo["0"]["name"]) {
					$localName = $provinceInfo["0"]["name"]."-".$areaInfo["0"]["name"];
				} else {
					$localName = $provinceInfo["0"]["name"]."-".$cityInfo["0"]["name"];
				}
				$customerInfo[$k]["local_name"] = $localName;
				//获得联系人信息
				$linkmanInfo = $linkman->where("c_id = ".$v["id"])->find();
				$customerInfo[$k]["link_name"] = $linkmanInfo["name"];
				$customerInfo[$k]["link_phone"] = $linkmanInfo["phone"];  

			}

			$this->assign("customerInfo",$customerInfo);
			$this->assign("pageStr",$pageStr);

			$this->display();
		}

		public function addCustomer(){

			//客户等级信息
			$levelInfo = M("xg_customer_level")->order("level asc")->select();
			//获取省份信息
			$provinceInfo = $this->getProvince("0");
			//得到城市名称
			$cityInfo = $this->getCity("0");
			//得到地区名称
			$areaInfo = $this->getArea("0");

			$post = $_POST;
			if ($post) {
// 				var_dump($post);exit;
			
				//将表单中提交过来的数据添加到 xg_customer 表中
				$ob=M("xg_customer");
				$time = time();
				$customer['cname']=$post['cname'];
				$customer['type_id']=$post['customer_type'];
				$customer['rank']=$post['rank'];
				$customer['local_procode']=$post['province_name'];
				$customer['local_citycode']=$post['city_name'];
				$customer['local_areacode']=$post['area_name'];
				$customer['local_address']=$post['local_address'];
				$customer['code']=$post['code'];
				$customer['remarks']=$post['remarks'];
				$customer['invoice_num']=$post['invoice_num'];
				$customer['invoice_tel']=$post['invoice_tel'];
				$customer['bank_name']=$post['bank_name'];
				$customer['bank_num']=$post['bank_num'];
				$customer['bank_address']=$post['bank_address'];
				$customer['ctime']=$time;
				$customer['utime']=$time;
				
				$res_1 = $ob->data($customer)->add();
				// 将表单提交过来的数据添加到 xg_customer_linkman 表中
				if($res_1 > 0){
					$select_num_hide = $post['select_num_hide'];
					for($i=0;$i<=$select_num_hide;$i++){
						$cus_link['c_id']=$res_1;    //公司的id
						$cus_link['name']=$post['link_name'.$i];
						$cus_link['phone']=$post['link_phone'.$i];
						$cus_link['address']=$post['link_address'.$i];
						print_r($cus_link);
						$res_2 = M("xg_customer_linkman")->data($cus_link)->add();
					}
// 					$cus_link['c_id']=$res_1;
// 					$cus_link['name']=$post['link_name'];
// 					$cus_link['phone']=$post['link_phone'];
// 					$cus_link['address']=$post['link_address'];
// 					$res_2 = M("xg_customer_linkman")->data($cus_link)->add();
				}

				//根据数据添加的情况来判断页面跳转
				if($res_1 || ($res_1&&$res_2)){
					$this->redirect("Customer/index");
				}else{
					$this->assign("post",$post);
					$this->assign("rank",$levelInfo);
					$this->assign("province",$provinceInfo);
					$this->assign("city",$cityInfo);
					$this->assign("area",$areaInfo);

					$this -> display("addCustomer");
				}
				// $customer_sql = "insert into xg_customer
		 	// 					(cname,type_id,rank,local_procode,local_citycode,local_areacode,local_address,
		 	// 						code,remarks,invoice_num,invoice_tel,bank_name,bank_num,bank_address)
		  // 						values
		  // 						('".$post['cname']."','".$post['customer_type']."','".$post['rank']."','".$post['province_name']."','".$post['city_name']."','".$post['area_name']."','".$post['local_address']."',
		  // 							'".$post['code']."','".$post['remarks']."','".$post['invoice_num']."','".$post['invoice_tel']."','".$post['bank_name']."','".$post['bank_num']."','".$post['bank_address']."')";
				
				// $result = M("xg_customer")->execute($customer_sql);
				
				// var_dump($customer_sql);
				// var_dump($result);
			}
			$this->assign("rank",$levelInfo);
			$this->assign("province",$provinceInfo);
			$this->assign("city",$cityInfo);
			$this->assign("area",$areaInfo);
	 		
			$this->display();
		}
		
		

		
		
	}