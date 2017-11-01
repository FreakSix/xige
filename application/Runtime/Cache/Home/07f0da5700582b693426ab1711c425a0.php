<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Amaze UI Admin index Examples</title>
  <meta name="description" content="这是一个 index 页面">
  <meta name="keywords" content="index">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp" />
  <link rel="icon" type="image/png" href="/xige/Public/i/favicon.png">
  <link rel="apple-touch-icon-precomposed" href="/xige/Public/i/app-icon72x72@2x.png">
  <meta name="apple-mobile-web-app-title" content="Amaze UI" />
  <link rel="stylesheet" href="/xige/Public/css/amazeui.min.css" />
  <link rel="stylesheet" href="/xige/Public/css/admin.css">
  <link rel="stylesheet" href="/xige/Public/css/app.css">
  <script src="/xige/Public/js/jquery.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
    $(".myapp-login").height($(window).height());
});
</script>
</head>

<body data-type="login" style="background-color: #023852">

  <div class="am-g myapp-login">
	<div class="myapp-login-logo-block  tpl-login-max">
		<div class="myapp-login-logo-text">
			<div class="myapp-login-logo-text">
				Amaze UI<span> Login</span> <i class="am-icon-skyatlas"></i>
				
			</div>
		</div>

		<div class="login-font">
			<i>Log In </i>   <span> 登录系统</span>
		</div>
		<div class="am-u-sm-10 login-am-center">
			<form class="am-form">
				<fieldset>
					<div class="am-form-group">
						<input type="email" class="user" id="doc-ipt-email-1" placeholder="输入用户名">
					</div>
					<div class="am-form-group">
						<input type="password" class="pass" id="doc-ipt-pwd-1" placeholder="输入密码吧">
					</div>
					<div class="am-form-group codes">
						<input type="text" class="code" id="doc-ipt-code-1" placeholder="验证码">
						<div class="validate">
							<div><img src="/xige/Public/img/passcode.jpg"/><div class="code-tip">点击刷新验证码</div></div>
						</div>
					</div>
					<p><button type="submit" class="am-btn am-btn-default">登录</button></p>
				</fieldset>
			</form>
		</div>
	</div>
</div>

  <script src="/xige/Public/js/amazeui.min.js"></script>
  <script src="/xige/Public/js/app.js"></script>
</body>

</html>