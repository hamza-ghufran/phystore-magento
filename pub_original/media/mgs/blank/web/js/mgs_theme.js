require([
	'jquery'
], function ($) {
	var $_miniCart = $("header.page-header .minicart-wrapper .block-minicart");
	var $_loginForm = $("header.page-header .header-top-links .login-form");
	var $_settingSite = $("header.page-header .setting-site .setting-site-content");
	var $_wishlistHeader = $("header.page-header .top-wishlist .block-wishlist");
    
    /* Header LeftSide */
    $(window).scroll(function () {
        var headerLeftHeight = $(".left-side .page-header").innerHeight();
        var window_height = $(this).height();
        if (headerLeftHeight - window_height < $(this).scrollTop()) {
            if (!$(".left-side .page-header").hasClass("fixed-bottom")) {
                $(".left-side .page-header").addClass("fixed-bottom");
            }
        }
        if (headerLeftHeight - window_height >= $(this).scrollTop()) {
            if ($(".left-side .page-header").hasClass("fixed-bottom")) {
                $(".left-side .page-header").removeClass("fixed-bottom");
            }
        }
    });
    /* Home Parallax */
    $(document).ready(function(){
        var sss = 1;
        var numItems = $('.parallax_item').length;
        if(numItems > 0){
            $("html").addClass("parallax_wrapper");
            
            function smoothScroll(target) {
                $('body,html').stop().animate(
                    {'scrollTop': target.offset().top + 1},
                    600
                );
            }
            function next() {
                if(sss < numItems){
                    sss = sss + 1;
                    smoothScroll($('.parallax_item_' + sss));
                    if($('.parallax_item_' + sss).hasClass('dark-item')){
                        $('.header-area').addClass('text-white');
                    }else{
                        $('.header-area').removeClass('text-white');
                    }
                }else if(sss == numItems){
                    sss = sss + 1;
                    smoothScroll($('.footer'));
                    $('.header-area').addClass('backgroud');
                }
            }
            function prev() {
                $('.header-area').removeClass('backgroud');
                if(sss > 1){
                    sss = sss - 1;
                    smoothScroll($('.parallax_item_' + sss));
                    if($('.parallax_item_' + sss).hasClass('dark-item')){
                        $('.header-area').addClass('text-white');
                    }else{
                        $('.header-area').removeClass('text-white');
                    }
                }else{
                    smoothScroll($('html'));
                }
            }
            $('.scroll-to-top').click(function () {
                sss = 1;
            });
            smoothScroll($('html'));
            $(window).bind('mousewheel DOMMouseScroll', function(event){
                clearTimeout($.data(this, 'timer'));
                $.data(this, 'timer', setTimeout(function() {
                    if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
                        setTimeout(function() {
                            prev();
                        }, 150);
                    }
                    else {
                        setTimeout(function() {
                            next();
                        }, 150);
                    }
                }, 300));
            });
        }
    });
	
	$(document).ready(function(){
		/* Header */
		calAndSetHeightHeaderFooter();
			/* Header Wishlist */
			$(document).on("click",".top-wishlist > a",function(e){
				e.preventDefault();
				disableBodyScroll();
				$(this).parent().toggleClass('active');
			});
            
			$(document).on("click","#close-wishlist-site",function(e){
				$(this).parents('.top-wishlist').removeClass('active');
				activeBodyScroll(false);
			});
			/* ++++++++++++ */
			
			/* Header Top Links */
			$(document).on("click",".header-top-links > .actions",function(e){
				e.preventDefault();
				if($('.header-area').hasClass('myaccount-slide')){
					disableBodyScroll();
				}
				$('.header-top-links').toggleClass('active');
				if($('.header-top-links').hasClass('active') && $('.header-top-links .captcha-reload').length){
					$('.header-top-links').find('.captcha-reload').click();
				}
			});
			
			$(document).on("click","#close-myaccount",function(e){
				$(this).parents('.header-top-links').removeClass('active');
				if($('header-area').hasClass('myaccount-slide')){
					activeBodyScroll(false);
				}
			});
			/* ++++++++++++ */
			
			/* Search */
			$(document).on("click",".block-search > .block-title",function(e){
				e.preventDefault();
				$(this).parent().toggleClass('active');
				$('header.page-header').toggleClass('ative-search');
				if($('.search-icon-popup').length){
					$('body').toggleClass('search-popup');
				}
			});
			
			$(document).on("click","#close-myaccount",function(e){
				$(this).parents('.header-top-links').removeClass('active');
				if($('header-area').hasClass('myaccount-slide')){
					activeBodyScroll(false);
				}
			});
			/* ++++++++++++ */
		
			/* Mini Cart */
			$(document).on("click",".minicart-wrapper .details-qty .minus",function(e){
				var $itemQty = parseInt($(this).parent().find('.cart-item-qty').attr('data-item-qty'));
				var $val = parseInt($(this).parent().find('.cart-item-qty').val());
				var $valChange = $val - 1;
				if($val > 1){
					$(this).parent().find('.cart-item-qty').val($valChange);
					if($itemQty != $valChange){
						$(this).parents('.details-qty').find('.update-cart-item').show();
					}else {
						$(this).parents('.details-qty').find('.update-cart-item').hide();
					}
				}
			});
			
			$(document).on("click",".minicart-wrapper .details-qty .plus",function(e){
				var $val = parseInt($(this).parent().find('.cart-item-qty').val());
				var $itemQty = parseInt($(this).parent().find('.cart-item-qty').attr('data-item-qty'));
				var $valChange = $val + 1;
				$(this).parent().find('.cart-item-qty').val($valChange);
				if($itemQty != $valChange){
					$(this).parents('.details-qty').find('.update-cart-item').show();
				}else {
					$(this).parents('.details-qty').find('.update-cart-item').hide();
				}
			});
			/* ++++++++++++ */
		
			/* Header Setting */
			$(document).on("click",".setting-site .actions .action.setting",function(e){
				disableBodyScroll();
				$(this).parents('.setting-site').addClass('active');
			});
			
			$(document).on("click","#close-setting-site",function(e){
				$('header.page-header .setting-site').removeClass('active');
				activeBodyScroll(false);
			});
			/* ++++++++++++ */

			/* Header Sticky Menu */
			if($('.active-sticky').length){
				var headerHeight = $('.active-sticky').height();
				$(window).scroll(function(){
					var st = $(this).scrollTop();
					if(st > headerHeight){
						$('.active-sticky').addClass('start-stk');
						$('.header-area .top-header').slideUp();
					}else {
						$('.active-sticky').removeClass('start-stk');
						$('.header-area .top-header').slideDown();
					}
					
				});
			}
			/* Footer */
			$(".footer-title").click(function(){
				if($(window).width() < 767){
					$(this).parent().toggleClass('active');
					$(this).parent().find('ul').slideToggle();
				}
			});
			
			
		$(document).mouseup(function(e) {
			/* Header Wishlist */
			var ctWishlist = $(".top-wishlist .block-wishlist");
			if (!ctWishlist.is(e.target) && ctWishlist.has(e.target).length === 0) {
				$('.top-wishlist').removeClass('active');
				activeBodyScroll(true);
			}
			/* ++++++++++++ */
			
			/* Header Top Links */
			var ctLogin = $(".header-top-links > .login-form");
			var ctLoginAction = $(".header-top-links > .actions");
			if (!ctLogin.is(e.target) && ctLogin.has(e.target).length === 0 && !ctLoginAction.is(e.target) && ctLoginAction.has(e.target).length === 0) {
				ctLogin.parent('.header-top-links').removeClass('active');
				activeBodyScroll(true);
			}
			/* ++++++++++++ */
			
			/* Header Setting */
			if($('.setting-site').length){
				var stSite = $(".setting-site.active > .setting-site-content");
				if (!stSite.is(e.target) && stSite.has(e.target).length === 0) {
					stSite.parent().removeClass('active');
					activeBodyScroll(true);
				}
			}
			/* ++++++++++++ */
			
			/* Wishlist Message */
			var ctResponseWishlist = $(".response_wishlist");
			if (!ctResponseWishlist.is(e.target) && ctResponseWishlist.has(e.target).length === 0) {
				ctResponseWishlist.removeClass('_show');
			}
			/* ++++++++++++ */
			
			/* Search */
			var ctSearch = $(".block-search");
			if (!ctSearch.is(e.target) && ctSearch.has(e.target).length === 0) {
				ctSearch.removeClass('active');
				$('header.page-header').removeClass('ative-search');
			}
			/* ++++++++++++ */
			
			/* Megamenu */
			var ationMenu = $(".megamenu_action");
			var ctMenu = $(".horizontal-menu");
			if (!ationMenu.is(e.target) && ationMenu.has(e.target).length === 0 && !ctMenu.is(e.target) && ctMenu.has(e.target).length === 0) {
				$('header.page-header').removeClass('active-menu');
			}
			/* ++++++++++++ */
		});
		/* ++++++++++++ */
		
		/* Megamenu */
		var isTouchDevice = ('ontouchstart' in window || 'onmsgesturechange' in window),
			$_level0Item = $('.horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0'),
			$_subItem = $('.horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0.menu-1columns .dropdown-submenu');
			
		if(!isTouchDevice){
			$_level0Item.on({
				mouseenter: function(){
					$(this).addClass('_hover');
				},
				mouseleave: function(){
					$(this).removeClass('_hover');
				}
			});
			$_subItem.on({
				mouseenter: function(){
					$(this).addClass('_hover');
				},
				mouseleave: function(){
					$(this).removeClass('_hover');
				}
			});
		}
		
		$(document).on("click",".horizontal-menu li > .toggle-menu",function(e){
			if($(this).parent().find('> .dropdown-mega-menu').length){
				$(this).parent().find('> .dropdown-mega-menu').slideToggle();
				$(this).parent().toggleClass('_show-child');
			}else if($(this).parent().find('> .sub-menu, > .dropdown-menu-ct').length){
				$(this).parent().find('> .sub-menu, > .dropdown-menu-ct').slideToggle();
				$(this).parent().toggleClass('_show-child');
			}
		});
		
		$(document).on("click",".megamenu_action_mb",function(e){
			if($('header.page-header').hasClass('active-menu')){
				$('header.page-header').removeClass('active-menu');
				activeBodyScroll(true);
			}else {
				$('header.page-header').addClass('active-menu');
				disableBodyScroll();
			}
			
		});
		
		$(document).on("click","#close-menu-site",function(e){
			$('header.page-header').removeClass('active-menu');
			activeBodyScroll(true);
		});
		
		$(document).on("click","#js_mobile_tabs button",function(e){
			
			var $el_action = $(this).attr('id');
			if(!$(this).hasClass('active-child')){
				$('#js_mobile_tabs button.active-child').removeClass('active-child');
				$(this).addClass('active-child');
				
				switch ($el_action) {
					case "back-home-action":
						updateAnimation($_miniCart, "");
						updateAnimation($_loginForm, "");
						updateAnimation($_settingSite, "");
						updateAnimation($_wishlistHeader, "");
						setTimeout(function(){ $('body').removeClass('atv-cart atv-setting atv-myaccount atv-wishlist atv-sidebar'); }, 300);
						break;
					case "my-account-action":
						$('body').addClass('atv-myaccount');
						updateAnimation($_loginForm, "translateX(0)");
						updateAnimation($_miniCart, "");
						updateAnimation($_settingSite, "");
						updateAnimation($_wishlistHeader, "");
						setTimeout(function(){ $('body').removeClass('atv-cart atv-setting atv-wishlist atv-sidebar'); }, 300);
						break;
					case "wishlist-link-action":
						$('body').addClass('atv-wishlist');
						updateAnimation($_loginForm, "translateX(-100%)");
						updateAnimation($_wishlistHeader, "translateX(0)");
						updateAnimation($_miniCart, "");
						updateAnimation($_settingSite, "");
						setTimeout(function(){ $('body').removeClass('atv-cart atv-setting atv-myaccount atv-sidebar'); }, 300);
						break;
					case "cart-top-action":
						$('body').addClass('atv-cart');
						updateAnimation($_loginForm, "translateX(-100%)");
						updateAnimation($_wishlistHeader, "translateX(-100%)");
						updateAnimation($_miniCart, "translateX(0)");
						updateAnimation($_settingSite, "");
						setTimeout(function(){ $('body').removeClass('atv-myaccount atv-setting atv-wishlist atv-sidebar'); }, 300);
						break;
					case "setting-web-action":
						$('body').addClass('atv-setting');
						updateAnimation($_loginForm, "translateX(-100%)");
						updateAnimation($_miniCart, "translateX(-100%)");
						updateAnimation($_wishlistHeader, "translateX(-100%)");
						updateAnimation($_settingSite, "translateX(0)");
						setTimeout(function(){ $('body').removeClass('atv-cart atv-myaccount atv-wishlist atv-sidebar'); }, 300);
						break;
				}
			}else {
				$(this).removeClass('active-child');
				updateAnimation($_miniCart, "");
				updateAnimation($_loginForm, "");
				updateAnimation($_settingSite, "");
				updateAnimation($_wishlistHeader, "");
				setTimeout(function(){ $('body').removeClass('atv-cart atv-setting atv-myaccount atv-wishlist atv-sidebar'); }, 300);
			}
		});
		
		/* ++++++++++++ */
		
		/* ++++++++++++ */
		 // $(".form-create-account .field:not('.choice') > .control > input, .form-create-account .field:not('.choice') select").each(function() {
			// $(this).focusin(function(){
			// 	$(this).parent().parent().find(' > .label').addClass('input-focus');
			// });
			// $(this).focusout(function(){
			// 	$(this).parent().find('.label').removeClass('input-focus');
			// 	$(this).blur(function(){
			// 		if( !$(this).val() ) {
			// 			$(this).parent().parent().find('.label').removeClass('input-focus');
			// 		}
			// 	});
			// });
		 // });
		/* ++++++++++++ */
		
	});
	
	$(window).resize(function(){
		if(!$('.header-area.scrolling').length){
			calAndSetHeightHeaderFooter();
		}
		if($(this).width() >= 768){
			if($('body').hasClass('atv-cart') || $('body').hasClass('atv-setting') || $('body').hasClass('atv-myaccount')){
				$('#js_mobile_tabs button.active-child').removeClass('active-child');
				updateAnimation(Form, "");
				updateAnimation($_settingSite, "");
				updateAnimation($_wishlistHeader, "");
				$('body').removeClass('atv-cart atv-setting atv-myaccount atv-wishlist');
			}
		}else {
			activeBodyScroll(false);
		}
	});
	
	function updateAnimation($el, $value) {
		$el.css('transform', $value);
		$el.css('-webkit-transform', $value);
		$el.css('-moz-transform', $value);
		$el.css('-o-transform', $value);
	}
	
	function disableBodyScroll() {
		$('body').addClass('fixed-content');
	}
	
	function activeBodyScroll($status) {
		if(!$status){
			$('body').removeClass('fixed-content');
		}else {
			if(!$('.page-header .header-top-links').hasClass('active') && !$('.page-header .top-wishlist').hasClass('active') && !$('.page-header .setting-site').hasClass('active') && !$('.page-header').hasClass('active-menu')){
				$('body').removeClass('fixed-content');
			}
		}
	}
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
	
	/* ++++++++++++ */
});

/* Calculate height & set it for header & footer */
function calAndSetHeightHeaderFooter() {
    require([
        'jquery'
    ], function ($) {
		var $heightHeader = $('header.page-header > div').height();
		$('header.page-header').height($heightHeader);
		if($('.footer-parallax').length){
			var $heightFooter = $('footer.page-footer .footer-parallax').height();
			$('footer.page-footer').height($heightFooter);
		}
    });
}
/* +++++++ */
/* POPUP VIDEO */

require([
	'jquery',
	'magnificPopup'
], function(jQuery){
	(function($) {
		$(document).ready(function() {
			$('.popup-video').magnificPopup({
				disableOn: 700,
				disableOn: 700,
				type: 'iframe',
				mainClass: 'mfp-img-gallery',
				removalDelay: 160,
				preloader: false,
				fixedContentPos: false
			}); 
		});
	})(jQuery);
});
require([
	'jquery'
], function(jQuery){
	(function($) {
		$(document).ready(function(){
			function setWidth() {
				var result = $(".pub-video .frame").width();
				$('.pub-video').css('max-width', result);
			};
			setWidth();		
		});
	})(jQuery);
});
/** Header **/
require([
	'jquery'
], function(jQuery){
	(function($) {
		$(document).ready(function(){
			$(document).on("click",".page-header .search-icon-left .block-title.theme-header-icon",function(e){
				e.preventDefault();
				disableBodyScroll();
				$(this).parent().toggleClass('show-search-box');
			});
		});
	})(jQuery);
});


/* Coming Soon */

/* Coming Soon */

require([
	'jquery'
], function(jQuery){
	(function($) {
		$(document).ready(function(){
			$('.cms-coming-soon .page.messages').addClass('_show');
			setTimeout(function(){ $('.cms-coming-soon .page.messages').removeClass('_show'); }, 10000);
		});
		countDownTimer('Apr 30 2019 23:59:00 GMT+0200');
		function countDownTimer(date) {
			var elem = $('#deal-time-coming .countdown');
			var futureTime = new Date(date).getTime();

			setInterval(function() { 
				var timeLeft = Math.floor( (futureTime - new Date().getTime()) / 1000 );
				var days =  Math.floor(timeLeft / 86400);
				timeLeft -= days * 86400;
				var hours = Math.floor(timeLeft / 3600) % 24;
				timeLeft -= hours * 3600;
				var min = Math.floor(timeLeft / 60) % 60;
				timeLeft -= min * 60;
				var sec = timeLeft % 60;
				var timeString = "<span class='days'>"+days+"</span>"+
								 "<span class='hours'>"+hours+"</span>"+
								 "<span class='minutes'>"+min+"</span>"+
								 "<span class='seconds'>"+sec+"</span>";
								 
				elem.html(timeString);
				
			}, 1000);
		}
	})(jQuery);
});


/* End Coming Soon */


/* Home Full slider */
require([
	'jquery'
], function(jQuery){
	(function($) {
		$(document).ready(function(){
			$(document).on("click",".action-newsletter-mb .icon",function(e){
				$(this).parent().parent().parent().toggleClass('active');
			});
		});
	})(jQuery);
});
			
/*  End Home Full slider */

function setLocation(url) {
    require([
        'jquery'
    ], function (jQuery) {
        (function () {
            window.location.href = url;
        })(jQuery);
    });
}