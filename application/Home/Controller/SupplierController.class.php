<?php
	namespace Home\Controller;
	
	
	class SupplierController extends BaseController{
		
		public function index(){
			$typeId = $_GET['typeId'];
			//共有多少条新闻
			$typeCount = M("newstypes")->where("typeId={$typeId}" )->find();
			//
			$newsType = M("newsarticles")->where("typeId={$typeId} and zt=1")->select();
			
			$this->assign("newsType",$newsType);
			$this->assign("typeCount",$typeCount);
			$this -> display();
		}
		
	}