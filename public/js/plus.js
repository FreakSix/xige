/**
 * Created by lenovo on 2017/9/5.
 */
$(document).ready(function(){
    $(".tpl-content-wrapper").height($(window).height()-150);

    $("#main-iframe").height($(window).height()-110);

    $(".tpl-left-nav").height($(window).height()-150);
    var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
    if($(window).width()<1000){
        $(".tpl-content-wrapper").width($(window).width());
        $("#main-iframe").width($(window).width());
    }else if($(window).width()>1280){
		
		if (userAgent.indexOf("Chrome") > -1){
		    $(".tpl-left-nav").height($(document.body).height()-45);
		    $(".b-box").height($(document.body).height()-100);
		    $(".tpl-content-wrapper").width($(window).width()-240);
		    $("#main-iframe").width($(window).width()-240);
		}else{
		    $(".tpl-content-wrapper").width($(window).width()-245);
		    $("#main-iframe").width($(window).width()-245);
		    $(".tpl-left-nav").height($(document.body).height()+45);
		    $(".b-box").height($(document.body).height()+45);
		}
	}else{
		if (userAgent.indexOf("Chrome") > -1){
		    $(".tpl-left-nav").height($(document.body).height()+45);
		    $(".b-box").height($(document.body).height()+45);
		    $(".tpl-content-wrapper").width($(window).width()-240);
		    $("#main-iframe").width($(window).width()-240);
		}else{
		    $(".tpl-content-wrapper").width($(window).width()-245);
		    $("#main-iframe").width($(window).width()-245);
		    $(".tpl-left-nav").height($(document.body).height()+45);
		    $(".b-box").height($(document.body).height()+45);
		}
	}
    $(".tpl-left-nav-item a").click(function(){
        $(".tpl-left-nav-item a").removeClass("active");
        $(this).addClass("active");
    });
});

