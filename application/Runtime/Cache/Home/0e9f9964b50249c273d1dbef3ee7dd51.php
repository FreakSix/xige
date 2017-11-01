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

    <link href="/xige/Public/css/amazeui.min.css" type="text/css" rel="stylesheet" />
    <link href="/xige/Public/css/admin.css" type="text/css" rel="stylesheet" />
    <link href="/xige/Public/css/app.css" type="text/css" rel="stylesheet" />

    <link rel="icon" type="image/png" href="/xige/Public/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/xige/Public/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />

    <script src="/xige/Public/js/echarts.min.js"></script>
    <script src="/xige/Public/js/jquery.min.js"></script>
    <script src="/xige/Public/js/plus.js"></script>

</head>
<body data-type="index">
    <header class="am-topbar am-topbar-inverse admin-header">
        <div class="am-topbar-brand">
            <a href="javascript:;" class="tpl-logo">
                <img src="/xige/Public/img/logo.png" alt="">
            </a>
        </div>
        <!--<div class="am-icon-list tpl-header-nav-hover-ico am-fl am-margin-right"></div>-->
        <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>
        <div class="am-collapse am-topbar-collapse" id="topbar-collapse">
            <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list tpl-header-list">
                
                <!-- <li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen" class="tpl-header-list-link"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li> -->

                <li class="am-dropdown" data-am-dropdown data-am-dropdown-toggle>
                    <a class="am-dropdown-toggle tpl-header-list-link" href="javascript:;">
                        <span class="tpl-header-list-user-nick">Admin</span><span class="tpl-header-list-user-ico"> <img src="/xige/Public/img/user01.png"></span>
                    </a>
                    <ul class="am-dropdown-content">
                        <li><a href="#"><span class="am-icon-bell-o"></span> 资料</a></li>
                        <li><a href="#"><span class="am-icon-cog"></span> 设置</a></li>
                        <li><a href="#"><span class="am-icon-power-off"></span> 退出</a></li>
                    </ul>
                </li>
                <!-- <li><a href="###" class="tpl-header-list-link"><span class="am-icon-sign-out tpl-header-list-ico-out-size"></span></a></li> -->
                <li class="am-dropdown" data-am-dropdown data-am-dropdown-toggle>
                    <a class="am-dropdown-toggle tpl-header-list-link" href="javascript:;">
                        <span class="am-icon-bell-o"></span> 消息提醒 <span class="am-badge tpl-badge-success am-round">5</span></span>
                    </a>
                    <ul class="am-dropdown-content tpl-dropdown-content">
                        <li class="tpl-dropdown-content-external">
                            <h3>你有 <span class="tpl-color-success">5</span> 条提醒</h3><a href="###">全部</a></li>
                        <li class="tpl-dropdown-list-bdbc"><a href="#" class="tpl-dropdown-list-fl"><span class="am-icon-btn am-icon-plus tpl-dropdown-ico-btn-size tpl-badge-success"></span> 【预览模块】移动端 查看时 手机、电脑框隐藏。</a>
                            <span class="tpl-dropdown-list-fr">3小时前</span>
                        </li>
                        <li class="tpl-dropdown-list-bdbc"><a href="#" class="tpl-dropdown-list-fl"><span class="am-icon-btn am-icon-check tpl-dropdown-ico-btn-size tpl-badge-danger"></span> 移动端，导航条下边距处理</a>
                            <span class="tpl-dropdown-list-fr">15分钟前</span>
                        </li>
                        <li class="tpl-dropdown-list-bdbc"><a href="#" class="tpl-dropdown-list-fl"><span class="am-icon-btn am-icon-bell-o tpl-dropdown-ico-btn-size tpl-badge-warning"></span> 追加统计代码</a>
                            <span class="tpl-dropdown-list-fr">2天前</span>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </header>
<!--左侧菜单栏-->
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-left-nav tpl-left-nav-hover">
            <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta charset="utf-8">
    

</head>
<body>
<div class="b-box">
            <div class="tpl-left-nav-title">菜单列表</div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
                    <li class="tpl-left-nav-item">
                        <a href="/xige/index.php/Index/main" target="main" class="nav-link active">
                            <i class="am-icon-home"></i>
                            <span>首页</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/xige/index.php/Order/index" target="main" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-bar-chart"></i>
                            <span>订单管理</span>
                        </a>
                    </li>
  
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-table"></i>
                            <span>商品管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="javascript:;" class="tpl-left-nav-link-list-first">
                                    <i class="am-icon-angle-right"></i>
                                    <span>标准品</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <ul class="tpl-left-nav-sub-menu-first">
                                    <li>
                                        <a href="/xige/index.php/Goods/index" target="main">
                                            <i class="am-icon-credit-card"></i>
                                            <span>名片</span>
                                        </a>
                                        <a href="table-images-list.html"  target="main">
                                            <span>画册</span>
                                        </a>
                                    </li>
                                </ul>
                                <a href="javascript:;" class="tpl-left-nav-link-list-second">
                                    <i class="am-icon-angle-right"></i>
                                    <span>非标准品</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <ul class="tpl-left-nav-sub-menu-second">
                                    <li>
                                        <a href="table-font-list.html" target="main">
                                            <i class="am-icon-credit-card"></i>
                                            <span>展架</span>
                                        </a>
                                        <a href="table-images-list.html"  target="main">
                                            <span>展板</span>
                                        </a>
                                    </li>
                                </ul>
                                
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/xige/index.php/Customer/index" target="main" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-bar-chart"></i>
                            <span>客户管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/xige/index.php/Supplier/index" target="main" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-bar-chart"></i>
                            <span>供应商管理</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-table"></i>
                            <span>数据统计</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/xige/index.php/Statistics/index" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>基本信息统计</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <a href="table-images-list.html"  target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>产品统计</span>
                                </a>
                                <a href="form-news.html" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>客户统计</span>
                                </a>
                                <a href="form-news.html" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>供应商统计</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-table"></i>
                            <span>用户与权限</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="table-font-list.html" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>部门管理</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <a href="table-font-list.html" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>用户管理</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <a href="table-images-list.html"  target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>权限设定</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-wpforms"></i>
                            <span>个人中心</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
                                <a href="/xige/index.php/PersonalCenter/index" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>基本信息</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <a href="form-amazeui.html" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>密码修改</span>
                                    <i class="am-icon-star tpl-left-nav-content-ico am-fr am-margin-right"></i>
                                </a>
                                <a href="form-line.html" target="main">
                                    <i class="am-icon-angle-right"></i>
                                    <span>登录日志</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="login.html" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-key"></i>
                            <span>备忘录</span>
                        </a>
                    </li>
                    <li class="tpl-left-nav-item">
                        <a href="/xige/index.php/Login/index" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-key"></i>
                            <span>登录</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
</body>
</html>
        </div>
        <div class="tpl-content-wrapper">
            <iframe name="main" id="main-iframe" src="/xige/index.php/Index/main" style="overflow: hidden"></iframe>
        </div>
    </div>
    <script src="/xige/Public/js/amazeui.min.js"></script>
    <script src="/xige/Public/js/iscroll.js"></script>
    <script src="/xige/Public/js/app.js"></script>
</body>

</html>