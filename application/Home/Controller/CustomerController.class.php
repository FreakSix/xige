<?php
	namespace Home\Controller;
	
	class CustomerController extends BaseController
	{
		
		public function index(){
			
			$articleId = $_GET['articleId'];
			//新闻内容
			$newsInfo = M("newsarticles")->join("inner join newstypes")->where("articleId={$articleId}")->find();
			//$newsInfo = M("")->query("select * from newsarticles as a inner join newstypes as t on a.typeId=t.typeId where articleId='{$articleId}'");
			
			//查询评论内容
			$reviews = M("reviews")->where("articleId={$articleId}")->select();
						
			if(!empty($_POST)){
				$userName = $_POST['userName'];
				$body = $_POST['body'];
				$face = $_POST['face'];
				//$result = M("reviews")->add($_POST);
				$result = M("reviews")->add(array("articleId"=>"{$articleId}","userName"=>"{$userName}","body"=>"{$body}","face"=>"{$face}"));
				if($result > 0)
				{
					$this->success("评论成功！",__APP__."/News/index/articleId/{$articleId}.html");
						
				}
				else
				{
					$this->success("评论失败！",__APP__."/News/index/articleId/{$articleId}.html");
			
				}
				//exit;
			}else{
			
				//点击量加一
				$hints = $newsInfo['hints'];
				$hint = $hints + 1;
				M("newsarticles")->where("articleId={$articleId}")->save(array("hints"=>"{$hint}"));
			}
			
			
			$this->assign("reviews",$reviews);
			$this->assign("newsInfo",$newsInfo);
			$this->display();
		}
		
		
		
	}