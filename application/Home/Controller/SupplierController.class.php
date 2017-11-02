<?php
	namespace Home\Controller;
	
	
	class SupplierController extends BaseController{
		
		public function index(){

			// $supplier = M("xg_supplier");
			// $arr = $supplier->select();
			// var_dump($arr);exit;
			
			$this -> display();
		}

		//添加供应商信息
		public function add_supplier(){
			
			var_dump($_POST);
			if(!empty($_POST)){
				$this -> display("index");	
			}else{
				$this -> display();
			}
			
		}

		//验证供应商是否已存在
		function checkSname(){
			$sname = $_GET['sname'];
			//echo $proid;
	 		$supplier = M("xg_supplier");
			$arr = $supplier->where("sname = $sname")->select();
			// var_dump($arr);exit;
	 		if(!empty($arr)){
	 			$res = 1;
	 		}else{
	 			$res = 2;
	 		}
	 			echo json_encode($res);
	}

		
	}