$(function() {

    var $fullText = $('.admin-fullText');
    $('#admin-fullscreen').on('click', function() {
        $.AMUI.fullscreen.toggle();
    });

    $(document).on($.AMUI.fullscreen.raw.fullscreenchange, function() {
        $fullText.text($.AMUI.fullscreen.isFullscreen ? '退出全屏' : '开启全屏');
    });


    var dataType = $('#eeeee').attr('data-type');
    for (key in pageData) {
        if (key == dataType) {
            pageData[key]();
        }
    }

    $('.tpl-switch').find('.tpl-switch-btn-view').on('click', function() {
        $(this).prev('.tpl-switch-btn').prop("checked", function() {
                if ($(this).is(':checked')) {
                    return false
                } else {
                    return true
                }
            })
            // console.log('123123123')

    })
});
// 页面数据
var pageData = {
    // ===============================================
    // 首页滑动条
    // ===============================================
    'index': function indexData() {
        var myScroll = new IScroll('#wrapper', {
            scrollbars: true,
            mouseWheel: true,
            interactiveScrollbars: true,
            shrinkScrollbars: 'scale',
            preventDefault: false,
            fadeScrollbars: true
        });
        var myScrollA = new IScroll('#wrapperA', {
            scrollbars: true,
            mouseWheel: true,
            interactiveScrollbars: true,
            shrinkScrollbars: 'scale',
            preventDefault: false,
            fadeScrollbars: true
        });
        var myScrollB = new IScroll('#wrapperB', {
            scrollbars: true,
            mouseWheel: true,
            interactiveScrollbars: true,
            shrinkScrollbars: 'scale',
            preventDefault: false,
            fadeScrollbars: true
        });

    },
}

    // ==========================
    // 侧边导航二级菜单显示与隐藏
    // ==========================
$('.tpl-left-nav-link-list').on('click', function() {
        $(this).siblings('.tpl-left-nav-sub-menu').slideToggle(80)
            .end()
            .find('.tpl-left-nav-more-ico').toggleClass('tpl-left-nav-more-ico-rotate');
    })
    // ==========================
    // 侧边导航三级菜单显示与隐藏
    // ==========================
// 产品名称菜单显示与隐藏
$('.tpl-left-nav-link-list-first').on('click', function() {
    var rec = $(this).attr("rec");
        $(this).siblings('.tpl-left-nav-sub-menu-first-'+rec).slideToggle(80)
            .end()
            .find('.am-icon-angle-right').toggleClass('tpl-left-nav-more-ico-rotate'); 
    });
// 其他三级菜单显示与隐藏
$('.tpl-left-nav-link-list-second').on('click', function() {
        $(this).siblings('.tpl-left-nav-sub-menu-second').slideToggle(80)
            .end()
            .find('.am-icon-angle-right').toggleClass('tpl-left-nav-more-ico-rotate'); 
    });

    // ===============================================
    // 左侧一级菜单选中设置
    // ===============================================
$(document).ready(function(){
    // alert($(".tpl-left-nav-list-son ul li.tpl-left-nav-item").length);
    for (var i = 0; i < $(".tpl-left-nav-list-son ul li.tpl-left-nav-item").length; i++) {
        if($(".tpl-left-nav").attr("nav") == $(".tpl-left-nav-item").find(".nav-link").eq(i).attr("nav")){
            $(".nav-link").eq(i).addClass("active").addClass("current-active");
      }else{
        $(".nav-link").eq(i).removeClass("active");
      }
    }
});
$(".nav-link").mouseout(function(){
    $("nav-link").removeClass("active");
})

    // ==========================
    // 左侧二级菜单选中设置及下拉菜单打开状态设置
    // ==========================
$(document).ready(function(){
    // alert($(".xg-second-level-nav").length);
    for(var i = 0; i < $(".xg-second-level-nav").length; i++){
        // alert($(".xg-second-level-nav").eq(i).find(".xg-second-level-nav-a").attr("nav"));
        // alert($(".xg-left-nav").attr("nav"));
        if($(".xg-left-nav").attr("nav") == $(".xg-second-level-nav").eq(i).find(".xg-second-level-nav-a").attr("nav")){
            // alert($(".xg-second-level-nav").eq(i).find(".xg-second-level-nav-a").attr("nav"));
            // alert(i);
            $(".xg-second-level-nav").eq(i).parent().show();
            $(".xg-second-level-nav").eq(i).find(".xg-second-level-nav-a").addClass("active");
            $(".xg-second-level-nav").eq(i).parent().parent()
                .find('.am-icon-angle-right').first()
                .toggleClass('tpl-left-nav-more-ico-rotate');
            // $(".xg-second-level-nav").eq(i).parent().parent()
            //     .find('.nav-link')
            //     .addClass("active");
            // alert($(".xg-second-level-nav").eq(i).find(".xg-second-level-nav-a").addClass("active"));
        }else{
            $(".xg-second-level-nav").eq(i).find(".xg-second-level-nav-a").removeClass("active");
        }
    }
});
    // ==========================
    // 左侧三级菜单选中设置及下拉菜单打开状态设置
    // ==========================
$(document).ready(function(){
    // alert($(".xg-third-level-nav").length);
    // alert($(".xg-left-nav-3").attr("nav"));
    for(var i = 0; i < $(".xg-third-level-nav").length; i++){
        // alert($(".xg-third-level-nav").eq(i).find(".xg-third-level-nav-a").attr("nav"));
        if($(".xg-left-nav-3").attr("nav") == $(".xg-third-level-nav").eq(i).find(".xg-third-level-nav-a").attr("nav")){
            $(".xg-third-level-nav").eq(i).parent().show();
            $(".xg-third-level-nav").eq(i).find(".xg-third-level-nav-a").addClass("active");
            $(".xg-third-level-nav").eq(i).parent().parent()
                .find('.am-icon-angle-right').toggleClass('tpl-left-nav-more-ico-rotate');
            $(".xg-third-level-nav").eq(i).parent().parent().show();
            $(".xg-third-level-nav").eq(i).parent().parent().parent().parent()
                .find(".nav-link").find('.am-icon-angle-right').toggleClass('tpl-left-nav-more-ico-rotate');
            $(".xg-third-level-nav").eq(i).parent().parent().parent().show();
            // $(".xg-third-level-nav").eq(i).parent().parent().parent().siblings().hide();
        }
    }
});

    // ==========================
    // 头部导航隐藏菜单
    // ==========================

$('.tpl-header-nav-hover-ico').on('click', function() {
        $('.tpl-left-nav').toggle();
        $('.tpl-content-wrapper').toggleClass('tpl-content-wrapper-hover');
    })
    // ==========================
    // 选中商品边框效果
    // ==========================
//$('.select-box').click(function(){
//    $(this).addClass('select-box-selected').siblings('li').removeClass('select-box-selected');
//    })

// ===============================================
// 添加客户信息页中添加多个联系人
// ===============================================
$("#add-contact").click(function(){
    var i = $("input[name=select_num_hide]").val();
    i++;
    if(i<6){
	    $("input[name=select_num_hide]").val(i);
//	    alert($("input[name=select_num_hide]").val());
	    var html = '<div class="am-form-group"><label class="am-u-sm-2 am-form-label">联系人姓名<span style="color:#FF0000"> *</span></label><div class="am-u-sm-4"><input type="tel" id="link_name'+i+'" name="link_name'+i+'" ></div><label class="am-u-sm-2 am-form-label">联系电话</label><div class="am-u-sm-4"><input type="tel" id="link_phone'+i+'" name="link_phone'+i+'" ></div></div><div class="am-form-group"><label class="am-u-sm-2 am-form-label">联系人地址</label><div class="am-u-sm-10"><input type="text" id="link_address'+i+'" name="link_address'+i+'" ></div></div>';
	    $('.contact').append(html);
    }else{
    	alert ("抱歉，最多可以添加五个联系人！！");
    }
});
// ===============================================
// 添加供应商信息页中添加多个联系人
// ===============================================
$("#add_supplier_contact").click(function(){
    var i = $("input[name=select_num_hide]").val();
    i++;
    if(i<6){
        $("input[name=select_num_hide]").val(i);
        var html = '<div class="am-form-group"><label class="am-u-sm-2 am-form-label">联系人姓名<span style="color:#FF0000"> *</span></label><div class="am-u-sm-4"><input type="tel" id="link_name'+i+'" name="link_name'+i+'" ></div><label class="am-u-sm-2 am-form-label">联系电话</label><div class="am-u-sm-4"><input type="tel" id="link_phone'+i+'" name="link_phone'+i+'" ></div></div>'
        $('.contact').append(html);
    }else{
        alert("如需添加更多联系人，请到供应商信息中进行添加");
    }
});
// ===============================================
// 多选框全选，全不选
// ===============================================
$(".xg-select-box").click(function(){
    var inputs = $(".xg-table-tr").find("input[type='checkbox']");
    // alert($(this).is(":checked"));
    if($(this).is(":checked")){
        inputs.each(function(){
            $(this).prop("checked",true);
        })
    }else{
        inputs.each(function(){
            $(this).removeAttr("checked");
        })
    }
});


    