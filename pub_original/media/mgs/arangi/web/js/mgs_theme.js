/* Coming Soon */

require([
    'jquery'
], function(jQuery) {
    (function($) {
        $(document).ready(function() {
            countDownTimer('Oct 30 2019 23:59:00 GMT+0200');

            function countDownTimer(date) {
                var elem = $('.js-countdown');
                var futureTime = new Date(date).getTime();

                setInterval(function() {
                    var timeLeft = Math.floor((futureTime - new Date().getTime()) / 1000);
                    var days = Math.floor(timeLeft / 86400);
                    timeLeft -= days * 86400;
                    var hours = Math.floor(timeLeft / 3600) % 24;
                    timeLeft -= hours * 3600;
                    var min = Math.floor(timeLeft / 60) % 60;
                    timeLeft -= min * 60;
                    var sec = timeLeft % 60;
                    var timeString = "<span class='days'>" + days + "</span>" +
                        "<span class='hours'>" + hours + "</span>" +
                        "<span class='minutes'>" + min + "</span>" +
                        "<span class='seconds'>" + sec + "</span>";

                    elem.html(timeString);

                }, 1000);
            }
            $('.newsletter-cms .btn-notify').on('click', function() {
                $('.newsletter-cms .subscribe').addClass('active');
                $('.newsletter-cms .subscribe.active .mage-error').addClass('act');
            });

            /* Wishlist */
    
            $(document).on("click",".box-tocart .qty .control .minus",function(e){
                var $itemQty = parseInt($(this).parent().find('input.qty').attr('data-item-qty'));
                var $val = parseInt($(this).parent().find('input.qty').val());
                var $valChange = $val - 1;
                if($valChange >= 0){
                    $(this).parent().find('input.qty').val($valChange);
                }
            });
            
            $(document).on("click",".box-tocart .qty .control .plus",function(e){
                var $itemQty = parseInt($(this).parent().find('input.qty').attr('data-item-qty'));
                var $val = parseInt($(this).parent().find('input.qty').val());
                var $valChange = $val + 1;
                $(this).parent().find('input.qty').val($valChange);
                
            });
			
			/* Mini Cart */
			$(document).on("click",".minicart-wrapper .details-qty .minus",function(e){
				var $itemQty = parseInt($(this).parent().find('.cart-item-qty').attr('data-item-qty'));
				var $val = parseInt($(this).parent().find('.cart-item-qty').val());
				var $valChange = $val - 1;
				if($val > 1){
					$(this).parent().find('.cart-item-qty').val($valChange);
					if($itemQty != $valChange){
						$(this).parents('.details-qty').find('.update-cart-item').show('fade', 500);
					}else {
						$(this).parents('.details-qty').find('.update-cart-item').hide('fade', 500);
					}
				}
			});
			
			$(document).on("click",".minicart-wrapper .details-qty .plus",function(e){
				var $val = parseInt($(this).parent().find('.cart-item-qty').val());
				var $itemQty = parseInt($(this).parent().find('.cart-item-qty').attr('data-item-qty'));
				var $valChange = $val + 1;
				$(this).parent().find('.cart-item-qty').val($valChange);
				if($itemQty != $valChange){
					$(this).parents('.details-qty').find('.update-cart-item').show('fade', 500);
				}else {
					$(this).parents('.details-qty').find('.update-cart-item').hide('fade', 500);
				}
			});
			
        });
    })(jQuery);
});


/* Menu version 1 */

require(['jquery'], function($) {
    $(document).ready(function() {
        $(".search-menu").click(function() {
            $(".block-search-header").slideToggle();
            $('.opacity-background').show();
        });

        $(".label .cancel").click(function() {
            $(".block-search-header").slideToggle();
            $('.opacity-background').hide();
        });


        $(".menu-right").click(function() {
            var widthScreen = $(window).width();
            
            $('body').css({
                "overflow": "hidden"
            });
            if (widthScreen >= 768) {
                $('.opacity-background').addClass('js-box-opacity-active');
                $('html').attr('show', 'active-sidebar');
            }

            $('.sidebar-content').addClass('sidebar-active');

        });

        $(".opacity-background").click(function() {
            $('html').removeAttr('show');
            $('body').css({
                "overflow": "inherit"
            });
			$(".block-search-header").slideUp();
			$('.header .opacity-background').hide();
            $('.opacity-background').removeClass('js-box-opacity-active');
			$('body.active-wishlist').removeClass('active-wishlist');
            $('.sidebar-content').removeClass('sidebar-active');
            $(".search-header").addClass('search-header-not-active');
            $(".search-header").removeClass('search-header-active');
        });

        $(".sidebar-cancel").click(function() {
            $('html').removeAttr('show');
            $('body').css({
                "overflow": "inherit"
            });
            $('.opacity-background').removeClass('js-box-opacity-active');
            $('.sidebar-content').removeClass('sidebar-active');
        });
        //filter 1column
        $(".icon-action-filter").click(function() {
            var widthScreen = $(window).width();
            $('html').attr('show', 'active-sidebar');    
            $('html').addClass('filter-open');

        });

        $(".opacity-background-filter").click(function() {
            $('html').removeAttr('show');
            $('html').removeClass('filter-open');
        });
        $(".filter-cancel").click(function() {
            $('html').removeAttr('show');
            $('html').removeClass('filter-open');
        });
        

        $(".show-password").click(function() {
            var $_input = $(this).parent().find('input');
            if ($_input.attr('type') == 'text') {
                $_input.attr('type', 'password');
            } else {
                $_input.attr('type', 'text');
            }

        });

        $(".register").on('click', function() {
            resetAll();
            $(".block-customer-login").addClass('js-form-hidden');
            $(".register-container").addClass('js-form-active');
        });
        $(".login").on('click', function() {
            resetAll();
            $(".block-customer-login").addClass('js-form-active');
            $(".register-container").addClass('js-form-hidden');
        });

        function resetAll() {
            $(".block-customer-login").removeClass('js-form-active');
            $(".register-container").removeClass('js-form-hidden');
            $(".block-customer-login").removeClass('js-form-hidden');
            $(".register-container").removeClass('js-form-active');
        }

     
        if($(window).width() < 1199) {
            $(".menu-mobile-control-right").append($('.switcher-language'));
            $(".menu-mobile-control-right").append($('.switcher-currency'));
            $(".main-menu-mobile").append($('.nav-main-menu'));
            $(".page-header .header3 .header-bottom-search .block-search").appendTo($('.block-search-header .search-popup'));
            $(".page-header .header4 .conts-middle-header .block.block-search").appendTo($('.block-search-header .search-popup'));
            $(".scroll-to-top").hide();
            $(".icon-menu-responsive").on('click', function() {
                $(".menu-mobile").addClass("active-menu-mobile");
            });
    
            $(".menu-mobile .cancel").on('click', function() {
                $(".menu-mobile").removeClass("active-menu-mobile");
            });
    
            $('.sub-child-menu').parent('li').append("<span class='ti-angle-down open-sub'></span>");

            $(".main-menu-mobile > .nav-main-menu > li").has(".dropdown-mega-menu").append("<span class='ti-angle-down open-sub'></span>");
            

            $(".sub-menu .level1").append("<span class='ti-angle-down open-sub'></span>");

            $(".open-sub").on("click", function() {
                $(this).toggleClass("ti-angle-down");
                $(this).toggleClass("ti-angle-up");
                $(this).prev().slideToggle();
                $(this).parent().find("a.level0").toggleClass("a-active");
            })
        }

        if($(window).width() >= 1200) {
            // $(".sidebar-content").prepend($(".customer-web-config"));
            $(".icon-menu-responsive").hide();
            $('.sub-child-menu').parent('li').append('<span class="open-submenu open-submenu-child"><i class="ti-angle-right"></i></span>');
            $(".cart-mobile").remove();
        }

        $(".switch-info").on("click", function(){
            $(".contact-form").hide();
            $(".contact-info").show();
        });

         $(".switch-form").on("click", function(){
            $(".contact-form").show();
            $(".contact-info").hide();
        });
        /* Header Sticky Menu */
        if($('.active-sticky').length){         
            var headerHeight = $('.active-sticky').height();
            var height = $('.active-sticky').innerHeight();
            $('body:not(.active-builder) .page-header').css('height', height );
            $('body:not(.active-builder) .mgs-instant-search-dropdown').css('top', height );
            $(window).resize(function(){
                if(!$('.header-area.scrolling').length){
                    var height = $('.active-sticky').innerHeight();
                    $('body:not(.active-builder) .page-header').css('height', height );
                }
            });
            
            $(window).scroll(function(){
                var height = $('.header3.active-sticky, .header4.active-sticky').innerHeight();
                $('body:not(.active-builder) .mgs-instant-search-dropdown').css('top', height );
                var st = $(this).scrollTop();
                if(st > headerHeight){
                    $('.active-sticky').addClass('start-stk');
                    $('.header3 .header-bottom-control .minicart-wrapper').appendTo('.header3 .middle-header .right-control');
                    $('.header-area .top-header').slideUp();
                    $('.header-area .header-bottom').slideUp();
                }else {
                    $('.active-sticky').removeClass('start-stk');
                    $('.header3 .middle-header .right-control .minicart-wrapper').appendTo('.header3 .header-bottom-control .addToCart');
                    $('.header-area .top-header').slideDown();
                    $('.header-area .header-bottom').slideDown();
                }
            });
            
        }
        if($(window).width() < 768) {
            if($('.show-sticky-menu').length){          
                var headerHeight = $('.active-sticky').height();
                var height = $('.active-sticky').innerHeight();

                $('body .page-header').css('height', height );
                $(window).scroll(function(){
                    var st = $(this).scrollTop();
                    if(st > headerHeight){
                        $('.active-sticky').addClass('start-stk');
                    }else {
                        $('.active-sticky').removeClass('start-stk');
                    }
                });
                $('body:not(.active-builder) .mgs-instant-search-dropdown').css('top', height );                
            }
        }


		/* Header Wishlist */
		$(document).on("click",".top-wishlist > a",function(e){
			e.preventDefault();
			$('body').addClass('active-wishlist');
			$('body').css('overflow', 'hidden');
			 $('.opacity-background').show();
		});
		
		$(document).on("click",".wishlist.action-wishlist",function(e){
			$('body').toggleClass('active-wishlist');
			$('.opacity-background').show();
		});
		$(document).on("click","#close-wishlist-site",function(e){
			$('body').removeClass('active-wishlist');
			$('body').css('overflow', 'inherit');
			$('.opacity-background').hide();
		});
		/* ++++++++++++ */
		
		//Instant search
		if($('.block-search-header').length){
			var searchHeight = $('body .block-search-header').height();
			var heights = $('body .block-search-header').innerHeight();
		}
		if(height < heights){
			$('body .mgs-instant-search-dropdown').css('top', heights );
		}

    });

});
/* Newsletter*/

require([
    'jquery'
], function(jQuery) {
    (function($) {
        $(document).ready(function() {
            $('.newsletter-social .btn-show-social').on('click', function() {
                 $('.newsletter-home').toggleClass('show');
            });
        });
    })(jQuery);
});

/* cookie */

require(['jquery'], function($) {

    $(document).ready(function(){
        $(".close-gdpr").on("click",function(){
            $(".mgs-gdpr-cookie-notice").addClass('hidden-cookie');
        });
    });

});


