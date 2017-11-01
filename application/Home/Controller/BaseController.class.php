<?php
	namespace Home\Controller;
	use Think\Controller;
	
	class BaseController extends Controller
	{
		public function _initialize(){
			$typeInfo = M("newstypes")->select();
			
			
			$this->assign("typeInfo",$typeInfo);
			
		}
	}