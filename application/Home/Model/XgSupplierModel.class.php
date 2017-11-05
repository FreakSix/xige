<?php
	namespace Home\Model;

	use Think\Model;
	
	class XgSupplierModel extends Model{

		public $trueTableName = "xg_province";
		
		
		//省份信息查询
		public function getProvince(){
		
			//省份信息区码
			// $procode = $_GET['procode'];
			$procode = 0;

	 		$province = M("xg_province");
	 		if($procode == 0){
	 			$arr = $province->select();
	 		}else{
	 			$arr = $province->where("code = $procode")->select();
	 		}
	 		return $arr;
		}

		
	}