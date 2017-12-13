<?php
	namespace Home\Controller;
	
	
	class PublicController extends BaseController{
		// 错误页面
		public function error(){
			$this->display("Public/error");
		}
	}