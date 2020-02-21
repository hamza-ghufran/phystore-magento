define([
    'jquery',
    'MGS_AjaxCart/js/config',
	'Magento_Ui/js/modal/modal'
], function($, mgsConfig, modal) {
    "use strict";

    jQuery.widget('mgs.action', {
        options: {
            requestParamName: mgsConfig.requestParamName
        },
		
		/* Catalog Add To Cart */
        fire: function(tag, actionId, url, formData, redirectToCatalog) {
            this._fire(tag, actionId, url, formData);
        },
		/*========================*/
		
		/* Ajax Cart Function */
        _fire: function(tag, actionId, url, formData) {
			var self = this;
			
			/* Loading Effect */
			if(tag.closest('.product-top').length){
				tag.closest('.product-top').addClass('loading-ajax');

			}else {
				if(tag.find('.tocart').length){
					if(tag.find('.tocart .icon-shopping-cart1').length) {
					}else{
						tag.find('.tocart').addClass('tocart-loading');
						tag.find('.tocart').text('+ Adding...');
					}
					
				}else{
					tag.addClass('tocart-loading');
				}
			} 
			/*========================*/
            
            formData.append(this.options.requestParamName, 1);
            
            jQuery.ajax({
                url: url,
                data: formData,
                type: 'post',
                dataType: 'json',
                contentType: false,
                cache: false,
                processData:false, 
                beforeSend: function(xhr, options) {
					if(tag.find('.tocart').length){
						// tag.find('.tocart').addClass('disabled');

						if(tag.find('.tocart .icon-shopping-cart1').length) {
							tag.find('.tocart').addClass('show-icon-cart-small');
							tag.find('.tocart > .text-add-tocart').text("Adding...");
							tag.find('.tocart').append('<span class="icon-load-add-to-cart"></span>');
						}else{
							tag.find('.tocart').text("Adding...");
							tag.find('.tocart-item').html("<span class='ti-shopping-cart'><span>Adding...</span></span>");
							tag.find('.tocart').addClass("button-opacity-07");
							tag.find('.tocart').append("<span class='icon-load-add-to-cart'></span>");
							tag.closest(".product-item-inner").addClass("show-product-item-inner");
						}
						
						// tag.closest(".product-top").append("<span class='icon-load-add-to-cart'></span>");
					}else{
						tag.find('.tocart').text('+ Adding...');
						tag.closest(".product-top").addClass("show-product-item-inner");
					} 
                },
                success: function(response, status) {
					
					/* Remove Loading Effect */
					if(tag.closest('.product-top').length){
						tag.closest('.product-top').removeClass('loading-ajax');
					}
					if(tag.find('.tocart').length){

						if(tag.find('.tocart .icon-shopping-cart1').length) {
							tag.find('.tocart').removeClass('show-icon-cart-small');
							tag.find('.tocart .icon-load-add-to-cart').remove();
							tag.find('.tocart > .text-add-tocart').text("+ Add to cart");
							tag.find('.tocart').blur();
						}else{
							tag.find('.tocart').html("<span>Added</span>");
							tag.find('.tocart-item').html("<span class='ti-shopping-cart'><span>Added</span></span>");
							tag.find('.tocart').blur();
							tag.find('.tocart').html("<span>+ Add to cart</span>");
							tag.find('.tocart-item').html("<span class='ti-shopping-cart'><span>+ Add to cart</span></span>");
							tag.find('.tocart').removeClass("button-opacity-07");
							$(".icon-load-add-to-cart").remove();
							tag.closest(".product-item-inner").removeClass("show-product-item-inner");
						}
						
					}else{
						tag.removeClass('tocart-loading');
						tag.removeClass('disabled');
					}
					/*========================*/
					
                    if (status == 'success') {
                        if(response.backUrl){
                            formData.append('action_url', response.backUrl);
                            self._fire(tag, actionId, response.backUrl, formData);
                        }else{
                            if (response.ui) {
                                if(response.productView){
                                	console.log(response.animationType);
									$('#ajaxcart_loading_overlay').addClass('loading');
									/* Add to cart false - Show popup options */
									if($('body.catalog-product-view').size() > 0){
										 $('body').addClass('origin-catalog-product-view');
									}else {
										 $('body').addClass('catalog-product-view');
									}
									$.ajax({
										url: response.ui,
										dataType: 'json',
										success: function (result) {
											$('#ajaxcart_loading_overlay').removeClass('loading');
											if (result.product_detail) {
												$('body').append('<div id="ajaxcart_form_popup'+result.id_product+'" class="product_quickview_content"></div>');
												var options =
												{
													type: 'popup',
													modalClass: "ajaxCartForm viewBox",
													responsive: true,
													innerScroll: true,
													title: false,
													buttons: false
												};
												
												var popup = modal(options, $('#ajaxcart_form_popup'+result.id_product));
												$('#ajaxcart_form_popup'+result.id_product).html(result.product_detail);
												$('#ajaxcart_form_popup'+result.id_product).trigger('contentUpdated');
												$('#ajaxcart_form_popup'+result.id_product).modal('openModal').on('modalclosed', function() { 
													$('#ajaxcart_form_popup'+result.id_product).parents('.ajaxCartForm').remove();
													$('body:not(.origin-catalog-product-view)').removeClass('catalog-product-view');
												});
											}
										}
									});
                                }else{
                                	console.log(response.animationType);
									/* After Add to cart success */
                                    if(response.animationType == 'popup' || response.animationType=='showcart') {
										/* Success Cart Popup */

										if(response.animationType == 'popup') {
											$('body').append('<div id="popup_ajaxcart_success" class="popup__main popup--result"></div>');
										
											var options =
											{
												type: 'popup',
												modalClass: "success-ajax--popup viewBox",
												responsive: true,
												innerScroll: true,
												title: false,
												buttons: false
											};
											
											var popup = modal(options, $('#popup_ajaxcart_success'));
											$('#popup_ajaxcart_success').html(response.ui + response.related);
											$('#popup_ajaxcart_success').trigger('contentUpdated');
											$('#popup_ajaxcart_success').modal('openModal').on('modalclosed', function() { 
												$('#popup_ajaxcart_success').parents('.success-ajax--popup').remove();
											});
										}else {
											$('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog("open");
										}
                                    }else{
										/* Success Cart Fly */
										var $source = '';
										if(tag.find('.tocart').length){
											if(tag.closest('.product-item-info').length){
												$source = tag.closest('.product-item-info');
											}else{
												$source = tag.find('.tocart');
											}
										}else{
											tag.removeClass('disabled');
											$source = tag.closest('.product-item-info');
										}
										
										var $animatedObject = jQuery('<div class="flycart-animated-add" style="position: absolute;z-index: 99999;">'+response.image+'</div>');
										
										var $_left = $source.offset().left - 1;
										var $_top = $source.offset().top - 1;
										
										$animatedObject.css({top: $_top, left: $_left});
										jQuery('html').append($animatedObject);
										
										if(jQuery(window).width() > 767){
											var gotoX = jQuery("#fixed-cart-footer").offset().left + 20;
											var gotoY = jQuery("#fixed-cart-footer").offset().top;      
											
											jQuery('#footer-cart-trigger').addClass('active');
											jQuery('#footer-mini-cart').slideDown(300);
										}else {
											var gotoX = jQuery("#cart-top-action").offset().left;
											var gotoY = jQuery("#cart-top-action").offset().top;      
										}
										
										$animatedObject.animate({
											opacity: 0.6,
											left: gotoX,
											top: gotoY
										}, 2000,
										function () {
											$animatedObject.remove();
										});
                                    }
                                }
                            }
                        }
                    }
                },
                error: function() {
                    window.location.href = mgsConfig.redirectCartUrl;
                }
            });
        }
    });

    return jQuery.mgs.action;
});