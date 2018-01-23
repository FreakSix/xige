<?php
	namespace Home\Controller;
	use Think\Verify;
	
	class IndexController extends BaseController { 
		
	    public function index(){
	    	dump($_SESSION);exit;
	    	$productTypeModel = D("XgProductType");
	    	// 获取商品分类
	    	$productType = $productTypeModel->getProductType();
	    	// 获取商品分类下对应的产品
	    	foreach ($productType as $k => $v) {
	    		$pid = $v["id"];
	    		$productMenu = $productTypeModel->getProductTypeByPid($pid);
	    		// dump($productMenu);
	    		$productType[$k]["productMenu"] = $productMenu;
	    	}
	    	//添加到期订单到备忘录
	    	$this->autoAddOrderMemo();
	    	// 获取公共信息
	    	$noticeInfo = D("XgNotice")->getNotice();
	    	// 获取公共备忘录信息
	    	$date = strtotime(date("Y-m-d"));
	    	$user = $_SESSION['userInfo']['truename'];
	    	$publicMemo = D("XgPublicMemo")->getPublicMemoByDate($date);   // 获取今天以后的公共备忘录信息
	    	$personalFutureMemo = D("XgPersonalMemo")->getPersonalFutureMemo($date,$user);   // 获取今天以后的个人备忘录信息
	    	// dump($personalFuturelMemo);
	    	$personalPastMemo = D("XgPersonalMemo")->getPersonalPastMemo($date,$user);   // 获取今天以前的个人备忘录信息
	    	// dump($personalPastMemo);exit;
	    	$this->assign("personalFutureMemo",$personalFutureMemo);
	    	$this->assign("personalPastMemo",$personalPastMemo);
	    	$this->assign("publicMemo",$publicMemo);
	    	$this->assign("noticeInfo",$noticeInfo);
	    	$this->assign("productType",$productType);
	        $this->display();    
	    }

	    public function header(){
	    	$this->display();
	    }

	    public function main(){
	    	$this->display();
	    }

	    public function menu(){
	    	$this->display();
	    }
	    
	    //生产验证码
	    public  function image(){
	    	
	    	$verify = new Verify();
	    	$verify->length =4;
	    	$verify->fontSize = 10;
	    	$verify->entry();
	    	
	    }
	    
	    //登录验证
	    public function login(){
	    	
	    	$userName = $_POST['userName'];
	    	$password = $_POST['password'];
	    	$checkCode = $_POST['checkCode'];
	    	
	    	//比对验证码
	    	$verify = new Verify();
	    	$result = $verify->check($checkCode);
	    	//var_dump($result);
	    	if($result){
		    	//登录信息验证
		    	if($userName != NULL){
					
		    		$userInfo = M("manager")->where("userName='{$userName}' and password='{$password}'")->find();
		    		
		    		if($userInfo == NULL){
		    			$this->success("登录失败,输入的用户名或者密码有误！",__APP__."/Index/index.html");
		    		}else{
		    			$_SESSION["userMsg"] = $userInfo;
		    			$this->success("登录成功！",__ROOT__."/admin.php");
		    		}
		    	}
	    	}else{
	    		$this->success("登录失败,输入的验证码有误！",__APP__."/Index/index.html");
	    	}
	    	
	    	$this->assign("userMsg",$_SESSION["userMsg"]);
	    }
	    // 添加公共备忘录页面
	    public function addPublicMemo(){
	    	$this->display("add_public_memo");
	    }
	    // 添加公共备忘录处理
	    public function addPublicMemoHandle(){
	    	$post = $_POST;
	    	if($post){
	    		$post['memo_time'] = strtotime($post["memo_date"]);
		    	$data["memo_time"] = $post["memo_time"];
		    	$data["memo_event"] = $post["memo_info"];
		    	$data["memo_level"] = $post["memo_level"];
		    	$data["edit_time"] = time();
		    	$data["manager_name"] = $post["manager_name"];
		    	$res = D("XgPublicMemo")->addPublicMemo($data);
		    	echo $res;
	    	}
	    	// print_r($data);exit;
	    }
	    // 修改公共备忘录页面
	    public function updatePublicMemo(){
	    	$id = $_GET['id'];
	    	$data = D("XgPublicMemo")->getPublicMemoById($id);
	    	// dump($data);exit;
	    	$this->assign("data",$data);
	    	$this->display("update_public_memo");
	    }
	    // 修改公共备忘录处理
	    public function updatePublicMemoHandle(){
	    	$post = $_POST;
	    	// print_r($post);exit;
	    	if($post){
	    		$id = $post["memo_id"];
	    		// 获取原始数据
	    		$data = D("XgPublicMemo")->getPublicMemoById($id);
	    		if($data["memo_time"] == strtotime($post['memo_date']) && $data["memo_event"] == $post['memo_info'] && $data["memo_level"] == $post['memo_level']){
	    			echo 0;
	    		}else{
	    			$newData['memo_time'] = strtotime($post['memo_date']);
			    	$newData['memo_event'] = $post['memo_info'];
			    	$newData['memo_level'] = $post['memo_level'];
			    	// print_r($data);exit;
			    	$res = D("XgPublicMemo")->updatePublicMemo($id,$newData);
			    	// dump($res);exit;
			    	echo 1;
	    		}
	    	}else{
	    		echo 2;
	    	}
	    }
	    // 删除公共备忘录
	    public function deletePublicMemo(){
	    	if($_POST){
	    		$id = $_POST["id"];
	    		// print_r($_POST);exit;
	    		$res = D("XgPublicMemo")->deletePublicMemo($id);
	    		echo $res;
	    	}else{
	    		echo 0;
	    	}
	    }
	    // 添加个人备忘录页面
	    public function addPersonalMemo(){
	    	$this->display("add_personal_memo");
	    }
	    // 添加个人备忘录处理
	    public function addPersonalMemoHandle(){
	    	$post = $_POST;
	    	if($post){
	    		$post['memo_time'] = strtotime($post["memo_date"]);
		    	$data["memo_time"] = $post["memo_time"];
		    	$data["memo_event"] = $post["memo_info"];
		    	$data["memo_level"] = $post["memo_level"];
		    	$data["edit_time"] = time();
		    	$data["manager_name"] = $post["manager_name"];
		    	$res = D("XgPersonalMemo")->addPersonalMemo($data);
		    	echo $res;
	    	}
	    }
	    // 自动将明天要交货的订单信息加入到备忘录中
	    public function autoAddOrderMemo(){
	    	$tomorrow = strtotime(date("Y-m-d",strtotime("+1 day")));
	    	$user = $_SESSION['userInfo']['truename'];
	    	$orderInfo = D("XgOrder")->getOrderByTime($tomorrow,$user);
	    	// dump($orderInfo);exit;
	    	if($orderInfo){
	    		foreach ($orderInfo as $k => $v) {
	    			$memoEvent = "明天是订单（编号：".$v['order_id']."， 客户：".$v['customer_name']."，商品名称：".$v['product_model']."）的交货时间，请注意及时处理！";
	    			$originalData = D("XgPersonalMemo")->getPersoalMemoByEvent($memoEvent);
	    			if($originalData == ""){
	    				$data['memo_time'] = strtotime(date("Y-m-d"));
			    		$data['memo_event'] = $memoEvent;
			    		$data['memo_level'] = 1;
			    		$data['edit_time'] = time();
			    		$data['manager_name'] = $user;
			    		$result = D("XgPersonalMemo")->addPersonalMemo($data);
	    			}
		    	}
	    	}
	    }
	    // 修改个人备忘录页面
	    public function updatePersonalMemo(){
	    	$id = $_GET['id'];
	    	$data = D("XgPersonalMemo")->getPersonalMemoById($id);
	    	// dump($data);exit;
	    	$this->assign("data",$data);
	    	$this->display("update_personal_memo");
	    }
	    // 修改个人备忘录处理
	    public function updatePersonalMemoHandle(){
	    	$post = $_POST;
	    	// print_r($post);exit;
	    	if($post){
	    		$id = $post["memo_id"];
	    		// 获取原始数据
	    		$data = D("XgPersonalMemo")->getPersonalMemoById($id);
	    		if($data["memo_time"] == strtotime($post['memo_date']) && $data["memo_event"] == $post['memo_info'] && $data["memo_level"] == $post['memo_level']){
	    			echo 0;
	    		}else{
	    			$newData['memo_time'] = strtotime($post['memo_date']);
			    	$newData['memo_event'] = $post['memo_info'];
			    	$newData['memo_level'] = $post['memo_level'];
			    	// print_r($data);exit;
			    	$res = D("XgpersonalMemo")->updatePersonalMemo($id,$newData);
			    	// dump($res);exit;
			    	echo 1;
	    		}
	    	}else{
	    		echo 2;
	    	}
	    }
	    // 删除个人备忘录
	    public function deletePersonalMemo(){
	    	if($_POST){
	    		$id = $_POST["id"];
	    		// print_r($_POST);exit;
	    		$res = D("XgPersonalMemo")->deletePersonalMemo($id);
	    		echo $res;
	    	}else{
	    		echo 0;
	    	}
	    }
	}