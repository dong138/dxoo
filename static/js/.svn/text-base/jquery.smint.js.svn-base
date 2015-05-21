
(function(){


	$.fn.smint = function( options ) {

		var settings = $.extend({
			'scrollSpeed'  : 500,
			'mySelector'     : 'div'
		}, options);

		$(this).addClass('smint');

		var optionLocs = new Array(),
			lastScrollTop = 0,
			menuHeight = $(".smint").height(),
			smint = $('.smint'),
        	smintA = $('.smint a'),
        	myOffset = smint.height();

      



		if ( settings.scrollSpeed ) {
				var scrollSpeed = settings.scrollSpeed
			}

		if ( settings.mySelector ) {
				var mySelector = settings.mySelector
		};



		return smintA.each( function(index) {
            
			var id = $(this).attr('href').split('#')[1];

			if (!$(this).hasClass("extLink")) {
				$(this).attr('id', id);
			}
			optionLocs.push(Array(
				$(mySelector+"."+id).position().top-menuHeight, 
				$(mySelector+"."+id).height()+$(mySelector+"."+id).position().top, id)
			);
			var stickyTop = smint.offset().top;	
			var stickyMenu = function(direction){
				var scrollTop = $(window).scrollTop()+myOffset; 

				// if we scroll more than the navigation, change its position to fixed and add class 'fxd', otherwise change it back to absolute and remove the class
				if (scrollTop > stickyTop+myOffset) { 
					smint.css({ 'position': 'fixed', 'top':0 }).addClass('name-cur');
					$('body').css('padding-top', menuHeight );	
				} else {
					smint.css( 'position', 'relative').removeClass('name-cur'); 
					$('body').css('padding-top', '0' );	
				}   
				if(optionLocs[index][0] <= scrollTop && scrollTop <= optionLocs[index][1]){	
					if(direction == "up"){
						$("#"+id).addClass("name-cur");
						$("#"+optionLocs[index+1][2]).removeClass("name-cur");
					} else if(index > 0) {
						$("#"+id).addClass("name-cur");
						$("#"+optionLocs[index-1][2]).removeClass("name-cur");
					} else if(direction == undefined){
						$("#"+id).addClass("name-cur");
					}
					$.each(optionLocs, function(i){
						if(id != optionLocs[i][2]){
							
							$("#"+optionLocs[i][2]).removeClass("name-cur");
						}
					});
				}
			};
			stickyMenu();
			$(window).scroll(function() {
				var st = $(this).scrollTop()+myOffset;
				if (st > lastScrollTop) {
				    direction = "down";
				} else if (st < lastScrollTop ){
				    direction = "up";
				}
				lastScrollTop = st;
				stickyMenu(direction);
				if($(window).scrollTop() + $(window).height() == $(document).height()) {
	       			smintA.removeClass('name-cur')
	       			$(".smint a:not('.extLink'):last").addClass('name-cur')
	       			
   				} else {
   					smintA.last().removeClass('name-cur')
   				}
			});
        
        	$(this).on('click', function(e){
				// gets the height of the users div. This is used for off-setting the scroll so the menu doesnt overlap any content in the div they jst scrolled to
				var myOffset = smint.height();   

				e.preventDefault();
				var hash = $(this).attr('href').split('#')[1];

				

				var goTo =  $(mySelector+'.'+ hash).offset().top-myOffset;
				$("html, body").stop().animate({ scrollTop: goTo }, scrollSpeed);
				if ($(this).hasClass("extLink"))
                {
                    return false;
                }

			});	
			$('.intLink').on('click', function(e){
				var myOffset = smint.height();   

				e.preventDefault();
				
				var hash = $(this).attr('href').split('#')[1];

				if (smint.hasClass('fxd')) {
					var goTo =  $(mySelector+'.'+ hash).position().top-myOffset;
				} else {
					var goTo =  $(mySelector+'.'+ hash).position().top-myOffset*2;
				}
				
				$("html, body").stop().animate({ scrollTop: goTo }, scrollSpeed);

				if ($(this).hasClass("extLink"))
                {
                    return false;
                }

			});	
		});

	};

	$.fn.smint.defaults = { 'scrollSpeed': 500, 'mySelector': 'div'};
})(jQuery);
