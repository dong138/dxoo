$(function() {
	$("#J-mobile-login-link").click(function() {
		$(this).hide();
		$("#J-login-link").fadeIn();
		$("#J-login-form").hide();
		$("#J-mobile-login-form").fadeIn();

	})
	$("#J-login-link").click(function() {
		$(this).hide();
		$("#J-mobile-login-link").fadeIn();
		$("#J-mobile-login-form").hide();
		$("#J-login-form").fadeIn();
	})
})

$(function() {
	$(".sheet:gt(0)").hide();
	var $tabmenu = $(".signup__header li");
	$tabmenu
			.click(function() {
				$(this).addClass('current').siblings().removeClass('current');
				$(".sheet").eq($tabmenu.index(this)).fadeIn()
						.siblings('.sheet').hide();
			})
})