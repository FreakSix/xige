<?php
	namespace Home\Controller;
	use Think\Verify;
	
	class IndexController extends BaseController {
		
	    public function index(){
	    	$productTypeModel = D("XgProductType");
	    	$pid = 1;
	    	$productMenu = $productTypeModel->getProductTypeByPid($pid);
	    	$this->assign("productMenu",$productMenu);
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
	}