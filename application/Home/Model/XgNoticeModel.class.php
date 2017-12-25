<?php
namespace Home\Model;
use Think\Model;
/**
* 公告模型
*/
class XgNoticeModel extends Model{
	// 查询公告信息
	public function getNotice(){
		$noticeModel = M("xg_notice");
		$result = $noticeModel->select();
		return $result;
	}
	// 修改公告信息
	public function updateNotice($id,$data){
		$noticeModel = M("xg_notice");
		$res = $noticeModel->where("id = ".$id)->save($data);
		return $res;
	}
	
}


?>