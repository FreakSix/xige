<?php
	namespace Home\Controller;
	
	class CustomerController extends BaseController
	{
		
		public function index(){
			
			//查询客户公司信息
			$customer = M("xg_customer");
			$customerInfo = $customer->select();
			// var_dump($customerInfo);

			$level = M("xg_customer_level");
			foreach ($customerInfo as $k => $v) {
				// var_dump($v);
				//得到客户等级名称
				$levelInfo = $level->where("id = ".$v["level_id"])->find();
				// var_dump($levelInfo);
				$customerInfo[$k]["level_name"] = $levelInfo["name"];
				//得到省份名称
				$provinceInfo = $this->getProvince($v["local_procode"]);
				$customerInfo[$k]["local_pro_name"] = $provinceInfo["0"]["name"];
				//得到城市名称
				$cityInfo = $this->getCity($v["local_citycode"]);
				$customerInfo[$k]["local_city_name"] = $cityInfo["0"]["name"];

			}
			var_dump($customerInfo);
			exit;
			//客户联系人信息
			$customerLinkman = M("xg_customer_linkman");
			$this->assign("customerInfo",$customerInfo);

			$this->display();
		}
		
		
		
	}