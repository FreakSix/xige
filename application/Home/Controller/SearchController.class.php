<?php
	namespace Home\Controller;
	use Think\Page;
	
	
	class  SearchController extends BaseController{
		
		public function index(){
			
			$searchType = $_POST['searchType'];
			$keyword = $_POST['keyword'];
			
			//获得搜索的新闻总数
			$totalRow = M("newsarticles")->where("zt=1 and $searchType like '%{$keyword}%'")->count();
				
			//
			$page = New Page($totalRow,3);
			
			//获得所搜相关的内容
			$searchNews = M("newsarticles")->where("zt=1 and $searchType like '%{$keyword}%'")->limit($page->firstRow,$page->listRows)->select();
			
			
			$this->assign("pageList",$page->show());
			$this->assign("searchNews",$searchNews);
			$this->assign("totalRow",$totalRow);
			$this->assign("keyword",$keyword);
			
			$this->display();
			
		}
		
	}