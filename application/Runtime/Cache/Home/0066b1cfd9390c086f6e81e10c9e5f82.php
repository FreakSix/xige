<?php if (!defined('THINK_PATH')) exit();?><html>
  <head>
    <title>天天新闻网</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <link href="/1608/15_MVC/day_05/news/public/css/news.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
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
  <a href="/1608/15_MVC/day_05/news/index.php/Index/search.html" class="a">搜索</a>
</div>
	
	<!-- 正文内容 -->
	<div class="mainDiv clear">
	
	
	  <div class="newsTypeDiv">
	    <div class="newsTypeDiv1">&nbsp;<a href="index.php" class="a">新闻主页</a> &raquo; <?php echo ($typeCount["typeName"]); ?></div>
	    <div class="newsTypeDiv2">本类共有<?php echo ($typeCount["articleNums"]); ?>条新闻</div>
	  </div>
	  
	  <?php if(is_array($newsType)): $i = 0; $__LIST__ = $newsType;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="newsTypeDiv3">
	    <img src="/1608/15_MVC/day_05/news/public/images/makealltop.gif"><a href="/1608/15_MVC/day_05/news/index.php/News/index/articleId/<?php echo ($v["articleId"]); ?>.html"><?php echo ($v["title"]); ?></a>
	  </div><?php endforeach; endif; else: echo "" ;endif; ?>
	  
	  
	  
	  <div class="newsTypeDiv3">&nbsp;</div>
	</div>
    
    
    <!-- 网页脚 -->
    <div class="footDiv">
      网站简介 - 广告服务 - 网站地图 - 帮助信息 - 联系方式<br>
   Copyrights &copy; 2014 hys.com All Rights Reserved
</div>
  </body>
</html>