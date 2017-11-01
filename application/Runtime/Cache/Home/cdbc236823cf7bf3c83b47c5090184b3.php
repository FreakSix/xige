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
    <style>
    </style>
</head>

<body data-type="generalComponents">
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">首页</a></li>
                <li><a href="#">供应商管理</a></li>
                <li class="am-active">新增供应商</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">新增供应商</div>
                        <div class="tpl-portlet-input tpl-fz-ml">
                        <div class="am-btn-group am-btn-group-xs">
                            <button type="button" onclick="javascript:history.back(-1);" class="am-btn am-btn-default am-btn-primary"><span class="am-icon-angle-left"></span> 返回</button>
                            </div>
                        </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-9">
                            <form class="am-form am-form-horizontal">
                                <!-- 公司信息 -->
                                <h4>公司信息</h4>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-2 am-form-label">客户名称</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="user-name" >
                                    </div>
                                    <label for="user-name" class="am-u-sm-2 am-form-label">客户类型</label>
                                    <div class="am-u-sm-4">
                                        <select id="doc-select-1">
                                            <option value="option1">公司</option>
                                            <option value="option2">个人</option>
                                            <option value="option3">政府机构</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-2 am-form-label">所在区域</label>
                                    <div class="am-u-sm-2">
                                        <select id="doc-select-1">
                                            <option value="option1">北京市</option>
                                            <option value="option2">河北省</option>
                                            <option value="option3">上海市</option>
                                        </select>
                                    </div>
                                    <div class="am-u-sm-2">
                                        <select id="doc-select-1">
                                            <option value="option1">朝阳区</option>
                                            <option value="option2">丰台区</option>
                                            <option value="option3">通州区</option>
                                        </select>
                                    </div>
                                    <div class="am-u-sm-2">
                                        <select id="doc-select-1">
                                            <option value="option1">朝阳区</option>
                                            <option value="option2">丰台区</option>
                                            <option value="option3">通州区</option>
                                        </select>
                                    </div>
                                    <label for="user-email" class="am-u-sm-2 am-form-label">客户等级</label>
                                    <div class="am-u-sm-2">
                                        <select id="doc-select-1">
                                            <option value="option1">一级客户</option>
                                            <option value="option2">二级客户</option>
                                            <option value="option3">三级客户</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-address" class="am-u-sm-2 am-form-label">详细地址</label>
                                    <div class="am-u-sm-10">
                                        <input type="text" id="user-address" >
                                    </div>
                                </div>
                                <hr/>
                                <!-- 联系人信息 -->
                                <h4>联系人1</h4>
                                <div class="am-form-group">
                                    <label for="contact-name" class="am-u-sm-2 am-form-label">联系人</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="contact-name" >
                                    </div>
                                    <label for="contact-phone" class="am-u-sm-2 am-form-label">联系电话</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="contact-phone" >
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="contact-wechat" class="am-u-sm-2 am-form-label">微信</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="contact-wechat" >
                                    </div>
                                    <label for="contact-email" class="am-u-sm-2 am-form-label">联系人邮箱</label>
                                    <div class="am-u-sm-4">
                                        <input type="email" id="contact-email" >
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="contact-department" class="am-u-sm-2 am-form-label">所属部门</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="contact-department" >
                                    </div>
                                    <label for="contact-position" class="am-u-sm-2 am-form-label">职位</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="contact-position" >
                                    </div>
                                </div>
                                <h4>联系人2</h4>
                                <div class="am-form-group">
                                    <label for="contact-name2" class="am-u-sm-2 am-form-label">联系人</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="contact-name2" >
                                    </div>
                                    <label for="contact-phone2" class="am-u-sm-2 am-form-label">联系电话</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="contact-phone2" >
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="contact-wechat2" class="am-u-sm-2 am-form-label">微信</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="contact-wechat2" >
                                    </div>
                                    <label for="contact-email2" class="am-u-sm-2 am-form-label">联系人邮箱</label>
                                    <div class="am-u-sm-4">
                                        <input type="email" id="contact-email2" ">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="contact-department2" class="am-u-sm-2 am-form-label">所属部门</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="contact-department2" >
                                    </div>
                                    <label for="contact-position2" class="am-u-sm-2 am-form-label">职位</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="contact-position2" >
                                    </div>
                                </div>
                                <hr/>
                                <!-- 财务信息 -->
                                <h4>财务信息</h4>
                                <div class="am-form-group">
                                    <label for="invoice-title" class="am-u-sm-2 am-form-label">发票抬头</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="invoice-title" >
                                    </div>
                                    <label for="invoice-id" class="am-u-sm-2 am-form-label">纳税人识别号</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="invoice-id" >
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="invoice-bank" class="am-u-sm-2 am-form-label">开户银行</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="invoice-bank" >
                                    </div>
                                    <label for="invoice-phone" class="am-u-sm-2 am-form-label">电话</label>
                                    <div class="am-u-sm-4">
                                        <input type="tel" id="invoice-phone" >
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="invoice-name" class="am-u-sm-2 am-form-label">开户名称</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="invoice-name" >
                                    </div>
                                    <label for="invoice-num" class="am-u-sm-2 am-form-label">银行账号</label>
                                    <div class="am-u-sm-4">
                                        <input type="text" id="invoice-num" >
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="invoice-address" class="am-u-sm-2 am-form-label">地址</label>
                                    <div class="am-u-sm-10">
                                        <input type="text" id="invoice-address" >
                                    </div>
                                </div>
                                <hr/>




                                <div class="am-form-group">
                                    <div class="am-u-sm-10 am-u-sm-push-2">
                                        <button type="button" class="am-btn am-btn-primary">确认添加</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
    <script src="/xige/Public/js/jquery.min.js"></script>
    <script src="/xige/Public/js/amazeui.min.js"></script>
    <script src="/xige/Public/js/app.js"></script>
</body>

</html>