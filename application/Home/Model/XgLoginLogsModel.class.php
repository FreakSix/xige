<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgLoginLogsModel extends Model{
		
		public $trueTableName = "xg_login_logs";

		//用户登录写入登陆日志
		public function addLoginLogs($data){
			$res = M("xg_login_logs")->data($data)->add();
			return $res;
		}
	}