<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <title>系统提示信息</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="/CIGO_CRM/CIGO_CRM/xige/public/css/news.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
      var index = 5;
      function changeTime()
      {
    	  document.getElementById("timeSpan").innerHTML = index;
    	  index--;
    	  if(index < 0){
    		  window.location = "<?php echo ($jumpUrl); ?>";
    	  }
    	  else{
    		  window.setTimeout("changeTime()",1000);
    	  }
      }
    </script>
  </head>
  <body onload="changeTime()">
    <div class="headDiv">
  <div class="headDiv1">
    <div class="headDiv10">www.<b>ttnewS</b>.com</div>
    <div class="headDiv11"><img src="/CIGO_CRM/CIGO_CRM/xige/public/images/banner17.gif" width="370" height="50"></div>
  </div>
  <div class="headDiv2">天天新闻网</div>
</div>
<!-- 导航菜单 -->
<div class="menuDiv">
  <a href="/CIGO_CRM/CIGO_CRM/xige/index.php/Index/index.html" class="a">首页</a> | 
  <?php if(is_array($typeInfo)): $i = 0; $__LIST__ = $typeInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="/CIGO_CRM/CIGO_CRM/xige/index.php/NewsTypes/index/typeId/<?php echo ($v["typeId"]); ?>.html" class="a"><?php echo ($v["typeName"]); ?></a> |<?php endforeach; endif; else: echo "" ;endif; ?>
  <a href="/CIGO_CRM/CIGO_CRM/xige/index.php/Search/index.html" class="a">搜索</a>
</div>
    
    <table border="1" align="center" width="600">
      <tr>
        <td><b>系统提示信息</b></td>
      </tr>
      <tr>
        <td align="center">
          <br><?php echo ($message); ?> 页面将在 <span id="timeSpan">5</span> 秒钟内自动跳转！<br>
          <br>如果没有自动跳转，<a href="<?php echo ($jumpUrl); ?>">请点击这里</a>。<br><br>
        </td>
      </tr>
    </table>
    
    
    <div class="footDiv">
      网站简介 - 广告服务 - 网站地图 - 帮助信息 - 联系方式<br>
   Copyrights &copy; 2014 hys.com All Rights Reserved
</div>
  </body>
</html>