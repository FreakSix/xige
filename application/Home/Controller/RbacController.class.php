<?php
namespace Home\Controller;

// 权限管理控制器
class RbacController extends BaseController {
	// 添加职位信息页面
	public function addPosition(){
		//获取所有的部门
		$depeartModel = D("XgDepeartment");
		$depeartArr = $depeartModel->getDepeartAll();
		$this->assign("depeartArr",$depeartArr);
		$this->display("PersonalCenter/add_position");
	}
	// 修改职位信息页面
	public function updatePosition(){
		$duty_id = $_REQUEST['duty_id'];
		$depart_id = (int)$_REQUEST['depart_id'];
		$departModel = D("XgDepeartment");
		$all_depart = $departModel->getDepeartAll();
		$dutyModel = D("XgDuty");
		$duty_info = $dutyModel->getDutyById($duty_id);
		$this->assign("depart_id",$depart_id);
		$this->assign("duty_id",$duty_id);
		$this->assign("all_depart",$all_depart);
		$this->assign("duty_info",$duty_info);
		$this->display("PersonalCenter/update_position");
	}
	//修改职位---保存
	public function saveEditPosition(){
		$id = $_REQUEST['id'];
		$depart_id = empty($_REQUEST['depart_id'])?"":$_REQUEST['depart_id'];
		$duty_name = empty($_REQUEST['duty_name'])?"":$_REQUEST['duty_name'];
		$dutyModel = D("XgDuty");
		$res = $dutyModel->updateDutyById($id,$depart_id,$duty_name);
		if($res){
			echo 1;
		}else{
			echo 2;
		}
	}
	
	// 权限管理首页
	public function index(){
		//获取所有的职位
		$dutyModel = D("XgDuty");
		$dutyArr = $dutyModel->getDutyAll();
		//获取所有的部门
		$depeartModel = D("XgDepeartment");
		$depeartArr = $depeartModel->getDepeartAll();
		if(!empty($dutyArr)){
			foreach($dutyArr as $k=>$v){
				if(!empty($depeartArr)){
					foreach($depeartArr as $key=>$val){
						if($v['depart_id'] == $val['id']){
							$dutyArr[$k]['depart'] = $val['depart_name'];
						}
					}
				}
			}
		}
		$this->assign("list",$dutyArr);
		$this->display("PersonalCenter/role");
	}
	// 分配权限页面
	public function access(){
		$id = $_REQUEST['id'];
		$duty = D("XgDuty")->getDutyById($id);
		// dump($duty);
		$dutyPower = explode(",", $duty["power"]);
		// dump($dutyPower);
		// exit;
		$powerModel = D("XgPower");
		$powerArr = $powerModel->getAllPower();
		$powerList = array();
		if(!empty($powerArr)){
			foreach($powerArr as $k=>$v){
				if($v['fid'] == 0){
					$powerList[] = $v;
				}
			}
		}
		if(!empty($powerList)){
			foreach ($powerList as $key=>$val){
				foreach ($powerArr as $kk=>$vv){
					if($val['id'] == $vv['fid']){
						$powerList[$key]['info'][]=$vv;
						$powerList[$key]['dutyPower']=$dutyPower;
					}
				}
			}
		}
		// dump($powerList);
		//获取当前用户对应的职位，同时获取该职位对应的所有的权限
		$this->assign("dutyPower",$dutyPower);
		$this->assign("duty_id",$id);
		$this->assign("powerList",$powerList);
		$this->display("PersonalCenter/set_access");
	}
	// 节点页面（指的控制器、方法）
	public function node(){
		$this->display("PersonalCenter/node");
	}
	// 添加节点
	public function addNode(){
		$this->display("PersonalCenter/add_node");
	}
	// 修改节点
	public function updateNode(){
		$this->display("PersonalCenter/update_node");
	}
	
	//保存新增加的职位
	public function saveNewDyty(){
		$data['duty_name'] = $_POST['duty_name'];
		$data['depart_id'] = $_POST['depart_id'];
		$data['is_manager'] = $_POST['is_manager'];
		$data['total_person'] = 0;
		$dutyModel = D("XgDuty");
		$res = $dutyModel->addNewDuty($data);
		if($res){
			echo 1;
		}else{
			echo 0;
		}
		exit;
	}
	//保存修改权限的数据
	public function savePowerSubmit(){
		$duty_id = $_REQUEST['duty_id'];
		$select = $_POST['children_select'];
		$selectStr = implode(",",$select);
		$dutyModel = D("XgDuty");
		$res = $dutyModel->updateDutyPower($duty_id,$selectStr);
		if($res){
			echo 1;
		}else{
			echo 0;
		}
	}
}


?>