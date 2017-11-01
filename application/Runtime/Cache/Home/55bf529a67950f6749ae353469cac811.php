<?php if (!defined('THINK_PATH')) exit();?><html>
  <head>
    <title>天天新闻网</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <link href="/1608/15_MVC/day_05/news/public/css/news.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="/1608/15_MVC/day_05/news/library/kindeditor/kindeditor.js"></script>
	<script >
		var editor;//编辑器对象
	    //加载编辑器
	    KindEditor.ready(function(e){
	        //创建编辑器
	        editor = e.create("[name=body]",{
	            "width":"555px",
	            "height":"150px",
	            "items":["source","undo","redo","|","bold","italic","underline","|","fontsize","textcolor","table"]
	        });
	        //设置编辑器里的内容
	        editor.html("请留下留言内容...");
	    });

		  //表单验证
	      function checkReview()
	      {
	          if(editor.html() == "")
	          {
	              alert("请输入评论内容！");
	              document.frm.comment.focus();
	              return false;
	          }
	          else if(document.frm.userName.value == "")
	          {
	              alert("请输入姓名！");
	              editor.focus();
	              return false;
	          }

	         
	      }

	</script>

  </head>
  <body onload="initEditor()">
    <!-- 网站头 -->
    <div class="headDiv">
  <div class="headDiv1">
    <div class="headDiv10">www.<b>ttnewS</b>.com</div>
    <div class="headDiv11"><img src="/1608/15_MVC/day_05/news/public/images/banner17.gif" width="370" height="50"></div>
  </div>
  <div class="headDiv2">天天新闻网</div>
</div>
<!-- 导航菜单 -->
<div class="menuDiv">
  <a href="/1608/15_MVC/day_05/news/index.php/Index/index.html" class="a">首页</a> | 
  <?php if(is_array($typeInfo)): $i = 0; $__LIST__ = $typeInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="/1608/15_MVC/day_05/news/index.php/NewsTypes/index/typeId/<?php echo ($v["typeId"]); ?>.html" class="a"><?php echo ($v["typeName"]); ?></a> |<?php endforeach; endif; else: echo "" ;endif; ?>
  <a href="/1608/15_MVC/day_05/news/index.php/Search/index.html" class="a">搜索</a>
</div>
	
	<!-- 正文内容 -->
	<div class="mainDiv clear">
	  <!-- 显示的新闻 -->
	  <?php if($newsInfo != NULL): ?><div class="newsTypeDiv" style="border:1px solid #990100;">
		    <div class="newsTypeDiv1">
		      &nbsp;<a href="index.php" class="a">新闻主页</a> &raquo; <?php echo ($newsInfo["typeName"]); ?>
		    </div>
		  </div>
		  <div class="newsTitle"><?php echo ($newsInfo["title"]); ?></div>
		  <div class="newsTime"><?php echo ($newsInfo["dateandtime"]); ?></div>
		  <?php if($newsInfo["imagepath"] != NULL): ?><div class="newsImg"><img src="/1608/15_MVC/day_05/news/public/<?php echo ($newsInfo["imagepath"]); ?>" width="400" height="300"></div><?php endif; ?>
	
		  
		  <div class="newsContent"><?php echo ($newsInfo["content"]); ?></div>
	
		  <?php if($newsInfo["comment"] ==1): ?><!-- 发表评论 -->
		  
		  <form name="frm" method="post" action="" onsubmit="return checkReview()">
		  <input type="hidden" name="articleId" value="<?php echo ($newsINfo["articleId"]); ?>"/>
		  <div class="newsTypeDiv" style="border:1px solid #990100;">
		    <div class="newsTypeDiv1">&nbsp;&raquo; 发表评论</div>
		  </div>
		  <div class="newsContent">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em01.gif"><input type="radio" name="face" value="em01.gif" checked>
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em02.gif"><input type="radio" name="face" value="em02.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em03.gif"><input type="radio" name="face" value="em03.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em04.gif"><input type="radio" name="face" value="em04.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em05.gif"><input type="radio" name="face" value="em05.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em06.gif"><input type="radio" name="face" value="em06.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em07.gif"><input type="radio" name="face" value="em07.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em08.gif"><input type="radio" name="face" value="em08.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em09.gif"><input type="radio" name="face" value="em09.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em10.gif"><input type="radio" name="face" value="em10.gif">
		    <img src="/1608/15_MVC/day_05/news/public/images/face/em11.gif"><input type="radio" name="face" value="em11.gif">
		  </div>
		  <div class="newsImg"><textarea name="body"></textarea></div>
		  <div class="newsContent">
		    姓名：<input type="text" name="userName" size="20">
		    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <input type="submit" value="发表">
		  </div>
		  </form>
		  <!-- 评论内容 -->
		  <div class="newsTypeDiv" style="border:1px solid #990100;">
		    <div class="newsTypeDiv1">&nbsp;&raquo; 新闻点评</div>
		  </div>
		  <?php if(is_array($reviews)): $k = 0; $__LIST__ = $reviews;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($k % 2 );++$k;?><div class="reviewDiv" style="background-color:<?php if($k%2==0): ?>#CDE7F0
														  <?php else: ?>
														  #FFFBD6<?php endif; ?>">
		    <div class="reviewDiv1">
		   <img src="/1608/15_MVC/day_05/news/public/images/face/<?php echo ($vv["face"]); ?>">游客：<?php echo ($vv["userName"]); ?> 于 [<?php echo ($vv["dateandtime"]); ?>] 发表评论：
		    </div>
		    <div class="reviewDiv1"><?php echo ($vv["body"]); ?></div>
		    <div class="reviewDiv1"><hr class="hr1"></div>
		  </div><?php endforeach; endif; else: echo "" ;endif; ?>
		  
		  <?php else: ?>
		 	 <div class="reviewDiv" style="background-color:#CDE7F0;">
			  	<br><br><br>
		    	<span>该新闻不允许评论！</span>
	    	</div><?php endif; ?>
	<?php else: ?>
	  <div class="reviewDiv" style="background-color:#CDE7F0;">
	  	<br><br><br>
	    <span>没有找到相关内容！</span>
	  </div>
	  <br><?php endif; ?>
	</div>
    
    <!-- 网页脚 -->
    <div class="footDiv">
      网站简介 - 广告服务 - 网站地图 - 帮助信息 - 联系方式<br>
   Copyrights &copy; 2014 hys.com All Rights Reserved
</div>
  </body>
</html>