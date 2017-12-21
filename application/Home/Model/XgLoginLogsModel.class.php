<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgLoginLogsModel extends Model{
		
		public $trueTableName = "xg_login_logs";

		//用户登录写入登陆日志
		public function addLoginLogs($data){
// 			$res = M("xg_login_logs")->data($data)->add();
			$feilds=array_keys($data);
			$sql="insert into xg_login_logs (".implode(',',$feilds).") values('".implode("','",$data)."')";
			$result = M()->execute($sql);
			return $res;
		}
		//查看自己的登录日志
		public function getLoginLogsByUserId($user_id){
			$res = M("xg_login_logs")->where("user_id = {$user_id}")->select();
			return $res;
		}
		//查看所有人的登录日志
		public function getLoginLogs(){
			$res = M("xg_login_logs")->select();
			return $res;
		}
	}