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
    <link rel="stylesheet" href="/xige/Public/css/boot.css">
    <link rel="stylesheet" type="text/css" href="/xige/Public/css/font-awesome.min.css">
    <link rel="stylesheet" href="/xige/Public/css/daterangepicker.css">
    <script src="/xige/Public/js/jquery.min.js"></script>

</head>
<!-- <script>
    
    function a(){
        var oDiv = document.getElementById('div1nnn');
        oDiv.style.cssText = 'position:absolute; top:20px; float:right;min-width:1000px;width:100%; min-height:700px;height:100%; z-index:1000; display:block;border:1px solid #ccc';
    };
</script> -->

<body data-type="generalComponents">
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">首页</a></li>
                <li><a href="#">订单管理</a></li>
                <li class="am-active">订单列表</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">订单列表</div>
                        <div class="tpl-portlet-input tpl-fz-ml">
                                <div class="form-group">
                                            <div class="input-group">
                                            <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                                            <span>
                                                <i class="fa fa-calendar"></i> 日期选择
                                            </span>
                                                <i class="fa fa-caret-down"></i>
                                            </button>
                                            </div>
                                        
                                </div>
                            
                        </div>
                    </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12 am-u-md-6">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs">
                                    <button type="button" onclick="window.open('add.html','main')" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增订单</button>
                                    <!-- <div id="div1nnn" style="display:none;"><iframe src="add.html" width=100% height=100% id=""></iframe></div> -->
                                    <button type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-save"></span> 导出数据</button>
                                    <button type="button" class="am-btn am-btn-default am-btn-danger"><span class="am-icon-trash-o"></span> 删除所选内容</button>
                                </div>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-form-group">
                                <select data-am-selected="{btnSize: 'sm'}">
                                  <option value="option1">全部订单</option>
                                  <option value="option2">已完成订单</option>
                                  <option value="option3">已收货订单</option>
                                  <option value="option3">已发货订单</option>
                                  <option value="option3">生产中订单</option>
                                  <option value="option3">待审核订单</option>
                                  <option value="option3">已作废订单</option>
                                </select>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                                <input type="text" class="am-form-field">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="button"></button>
          </span>
                            </div>
                        </div>
                    </div>
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                            <th class="table-check"><input type="checkbox" class="tpl-table-fz-check"></th>
                                            <th class="table-id">订单编号</th>
                                            <th class="table-title">商品名称</th>
                                            <th class="table-cus">客户名称</th>
                                            <th class="table-sum">金额</th>
                                            <th class="table-type">类别</th>
                                            <th class="table-status">状态</th>
                                            <th class="table-status">付款状态</th>
                                            <th class="table-author am-hide-sm-only">作者</th>
                                            <th class="table-date am-hide-sm-only">修改日期</th>
                                            <th class="table-set">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="checkbox"></td>
                                            <td>1</td>
                                            <td><a href="#">Business management</a></td>
                                            <td>default</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="am-hide-sm-only">测试1号</td>
                                            <td class="am-hide-sm-only">2014年9月4日 7:28:47</td>
                                            <td>
                                                <div class="am-btn-toolbar">
                                                    <div class="am-btn-group am-btn-group-xs">
                                                        <button class="am-btn am-btn-default am-btn-xs am-text-secondary"><span class="am-icon-pencil-square-o"></span> 编辑</button>
                                                        <button class="am-btn am-btn-default am-btn-xs am-hide-sm-only"><span class="am-icon-copy"></span> 复制</button>
                                                        <button class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only"><span class="am-icon-trash-o"></span> 删除</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox"></td>
                                            <td>2</td>
                                            <td><a href="#">Business management</a></td>
                                            <td>default</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="am-hide-sm-only">测试1号</td>
                                            <td class="am-hide-sm-only">2014年9月4日 7:28:47</td>
                                            <td>
                                                <div class="am-btn-toolbar">
                                                    <div class="am-btn-group am-btn-group-xs">
                                                        <button class="am-btn am-btn-default am-btn-xs am-text-secondary"><span class="am-icon-pencil-square-o"></span> 编辑</button>
                                                        <button class="am-btn am-btn-default am-btn-xs am-hide-sm-only"><span class="am-icon-copy"></span> 复制</button>
                                                        <button class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only"><span class="am-icon-trash-o"></span> 删除</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                            <li class="am-disabled"><a href="#">«</a></li>
                                            <li class="am-active"><a href="#">1</a></li>
                                            <li><a href="#">2</a></li>
                                            <li><a href="#">3</a></li>
                                            <li><a href="#">4</a></li>
                                            <li><a href="#">5</a></li>
                                            <li><a href="#">»</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>

                            </form>
                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
    <script src="/xige/Public/js/amazeui.min.js"></script>
    <script src="/xige/Public/js/app.js"></script>
    <script type="text/javascript" src="/xige/Public/js/moment.js"></script>
<script type="text/javascript" src="/xige/Public/js/daterangepicker.js"></script>
<script type="text/javascript">
    $('#daterange-btn').daterangepicker({
            ranges: {
                '今天': [moment(), moment()],
                '本周': [moment().startOf('week'), moment().endOf('week')],
                '本月': [moment().startOf('month'), moment().endOf('month')],
                '今年': [moment().startOf('year'), moment().endOf('year')]
            },
            startDate: moment(),
            endDate: moment().endOf('month')
        },
        function(start, end) {
            $('#daterange-btn span').html(start.format('YYYY年MMMD日') + ' - ' + end.format('YYYY年MMMD日'));
            alert(start.format('YYYYMMDD') + " " + end.format('YYYYMMDD'));
        }
    );

    //Date picker
    $('#datepicker').datepicker({
        autoclose: true
    });
</script>
</body>

</html>