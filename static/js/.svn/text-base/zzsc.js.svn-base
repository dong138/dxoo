$(function() {
	pj(1, 1);
	$(".pl_list:first").show();
	var $plTab = $(".pl_tab li");
	$plTab
			.click(function() {
				$(this).addClass("item").siblings("li").removeClass("item");
				$(".pl_list").eq($plTab.index(this)).show()
						.siblings(".pl_list").hide();
				clickTab_d($plTab.index(this) + 1);
			})

});