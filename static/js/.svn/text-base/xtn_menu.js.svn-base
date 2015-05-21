$(function(){
  var tabH=$(".content-navbar").offset().top;
  $(window).scroll(function() {
	var scroH1=$(this).scrollTop();
	if(scroH1>=tabH){
	  $(".content-navbar").css({"position":"fixed","top":"0","z-index":"9999"});
	}else if(scroH1<tabH){
	  $(".content-navbar").css("position","static");
	}
  })
});

$(function(){

				$(".content-navbar li ").click(function(){
					$(this).addClass("active").siblings('li').removeClass("active");

				})
				
				$("#name-adress").click(function(){
					$("html,body").animate({scrollTop:$("#shop_adress").offset().top-90},500);
				})
				
				$("#name-product-detail").click(function(){
					$("html,body").animate({scrollTop:$("#product_detail_area").offset().top-60},500);
				})
				$("#name-detail").click(function(){
					$("html,body").animate({scrollTop:$("#name_detail_box").offset().top-60},500);
				})
				$("#name-infro").click(function(){
					$("html,body").animate({scrollTop:$("#name_infor_box").offset().top-60},500);
				})
				$("#name-comment").click(function(){
					$("html,body").animate({scrollTop:$("#name-comment-block_lh").offset().top-60},500);
				})
			});



$(function(){
	var pro_top = -10000;
	var news_top = -10000;
	var ser_top = -10000;
	var con_top = -10000;
	var job_top = -10000;
	if($("#shop_adress").length > 0){
		pro_top = $("#shop_adress").offset().top - 300;
	}
	if($("#product_detail_area").length > 0){
		news_top = $("#product_detail_area").offset().top - 300;
	}
	if($("#name_detail_box").length > 0){
		ser_top = $("#name_detail_box").offset().top - 300;
	}
	if($("#name_infor_box").length > 0){
		con_top = $("#name_infor_box").offset().top - 300;
	}
	if($("#name-comment-block_lh").length > 0){
		job_top = $("#name-comment-block_lh").offset().top - 300;
	}
	//alert(tops);
	$(window).scroll(function(){
		var scroH = $(this).scrollTop();
		if(job_top != -10000 && scroH>=job_top){
			set_cur(".name_five");
		}else if(con_top != -10000 && scroH>=con_top){
			set_cur(".name_four");
		}else if(ser_top != -10000 && scroH>=ser_top){
			set_cur(".name_three");
			
		}else if(news_top != -10000 && scroH>=news_top){
			set_cur(".name_two");
			
		}else if(pro_top != -10000 && scroH>=pro_top){
			set_cur(".name_one");
		}
	});
	
	
});


function set_cur(n){
	$(n).addClass("active").siblings('li').removeClass("active");

}



