<?php
	namespace Home\Model;
	use Think\Model;
	
	class XgLoginLogsModel extends Model{
		
		public $trueTableName = "xg_login_logs";

		//用户登录写入登陆日志
		public function addLoginLogs($data){
			// $res = M("xg_login_logs")->data($data)->add();
			$feilds=array_keys($data);
			$sql="insert into xg_login_logs (".implode(',',$feilds).") values('".implode("','",$data)."')";
			$result = M()->execute($sql);
			return $res;
		}
		//查看自己的登录日志
		public function getLoginLogsByUserId($user_id,$start=0,$size=15){
			$res = M("xg_login_logs")->where("user_id = {$user_id}")->order("addtime desc")->limit($start,$size)->select();
			return $res;
		}
		//查看自己的登录日志总数
		public function getLoginLogsByUserIdCount($user_id){
			$res = M("xg_login_logs")->where("user_id = {$user_id}")->count();
			return $res;
		}
		//查看所有人的登录日志
		public function getLoginLogs($start=0,$size=15){
			$res = M("xg_login_logs")->order("addtime desc")->limit($start,$size)->select();
			return $res;
		}
		//查看所有人的登录日志总数
		public function getLoginLogsCount(){
			$res = M("xg_login_logs")->count();
			return $res;
		}
	}