<?php
	namespace Home\Controller;
	use Think\Verify;
	
	class IndexController extends BaseController {
		
	    public function index(){
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
	    	// 获取公共信息
	    	$noticeInfo = D("XgNotice")->getNotice();
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

	    }
	    // 修改公共备忘录页面
	    public function updatePublicMemo(){
	    	$this->display("update_public_memo");
	    }
	    // 修改公共备忘录处理
	    public function updatePublicMemoHandle(){

	    }
	    // 删除公共备忘录
	    public function deletePublicMemo(){

	    }
	    // 添加个人备忘录页面
	    public function addPersonalMemo(){
	    	$this->display("add_personal_memo");
	    }
	    // 添加个人备忘录处理
	    public function addPersonalMemoHandle(){

	    }
	    // 修改个人备忘录页面
	    public function updatePersonalMemo(){
	    	$this->display("update_personal_memo");
	    }
	    // 修改个人备忘录处理
	    public function updatePersonalMemoHandle(){

	    }
	    // 删除个人备忘录
	    public function deletePersonalMemo(){

	    }
	}