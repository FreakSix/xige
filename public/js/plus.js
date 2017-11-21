/**
 * Created by lenovo on 2017/9/5.
 */
$(document).ready(function(){
	

    // $(".tpl-left-nav").height($(window).height()-150);
    var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
    if($(window).width()<1000){
        $(".tpl-content-wrapper").width($(window).width());
    }else if($(window).width()>1390){
		
		if (userAgent.indexOf("Chrome") > -1){
		    $(".tpl-left-nav").height($(document.body).height()-45);
		    // $(".tpl-content-wrapper").width($(window).width()-240);
		}else{
		    // $(".tpl-content-wrapper").width($(window).width()-245);
		    $(".tpl-left-nav").height($(document.body).height()+45);
		}
	}else{
		if (userAgent.indexOf("Chrome") > -1){
		    $(".tpl-content-wrapper").width($(window).width()-230);
		    $(".tpl-content-wrapper").height($(window).height()-70);
		    // $(".tpl-portlet-components").width($(window).width()-230);
		    // $(".tpl-portlet-components").height($(window).height()-70);
		    $(".tpl-left-nav").height($(window).height()-60);
		}else if(userAgent.indexOf("Trident") > -1){
		    $(".tpl-content-wrapper").width($(window).width()-230);
		    $(".tpl-content-wrapper").height($(window).height()-70);
		    // $(".tpl-portlet-components").width($(window).width()-230);
		    // $(".tpl-portlet-components").height($(window).height()-70);
			$(".tpl-left-nav").height($(window).height()-60);
		}else{
		    $(".tpl-content-wrapper").width($(window).width()-230);
		    $(".tpl-content-wrapper").height($(window).height()-50);
		    // $(".tpl-portlet-components").width($(window).width()-230);
		    // $(".tpl-portlet-components").height($(window).height()-50);
		    $(".tpl-left-nav").height($(window).height()-60);
		    $(".tpl-left-nav-list-son").height($(window).height()-120);
		}
	}
 //    $(".tpl-content-wrapper").height($(window).height()-150);

 //    $("#main-iframe").height($(window).height()-110);

 //    $(".tpl-left-nav").height($(window).height()-150);
 //    var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
 //    if($(window).width()<1000){
 //        $(".tpl-content-wrapper").width($(window).width());
 //        $("#main-iframe").width($(window).width());
 //    }else if($(window).width()>1390){
		
	// 	if (userAgent.indexOf("Chrome") > -1){
	// 	    $(".tpl-left-nav").height($(document.body).height()-45);
	// 	    $(".b-box").height($(document.body).height()-100);
	// 	    $(".tpl-content-wrapper").width($(window).width()-240);
	// 	    $("#main-iframe").width($(window).width()-240);
	// 	}else{
	// 	    $(".tpl-content-wrapper").width($(window).width()-245);
	// 	    $("#main-iframe").width($(window).width()-245);
	// 	    $(".tpl-left-nav").height($(document.body).height()+45);
	// 	    $(".b-box").height($(document.body).height()+45);
	// 	}
	// }else{
	// 	if (userAgent.indexOf("Chrome") > -1){
	// 	    $(".tpl-left-nav").height($(document.body).height()+45);
	// 	    $(".b-box").height($(document.body).height()+45);
	// 	    $(".tpl-content-wrapper").width($(window).width()-240);
	// 	    $("#main-iframe").width($(window).width()-240);
	// 	}else{
	// 	    $(".tpl-content-wrapper").width($(window).width()-245);
	// 	    $("#main-iframe").width($(window).width()-245);
	// 	    $(".tpl-left-nav").height($(document.body).height()+45);
	// 	    $(".b-box").height($(document.body).height()+45);
	// 	}
	// }
    $(".tpl-left-nav-item a").click(function(){
        $(".tpl-left-nav-item a").removeClass("active");
        $(this).addClass("active");
    });
});

