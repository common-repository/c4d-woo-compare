(function($){
	"use strict";
	if (typeof c4d_woo_compare == 'undefined') return;
	var c4dWooCompare = {
		prefix : 'c4d-woo-compare',
		cookieName: ''
	};

	c4dWooCompare.cookieName = c4dWooCompare.prefix + '-cookie';

	c4dWooCompare.current = function() {
		var current = $.cookie(c4dWooCompare.cookieName);
		return current = typeof current != 'undefined' ? current.split(',').filter(Number) : [];
	};

	c4dWooCompare.cart = function(callback) {
		$.get({
			url: c4d_woo_compare.ajax_url,
			data: {
				'action': 'c4d_woo_compare_cart',
			}
		}).done(function(res){
			$('.c4d-woo-compare-cart__list').html(res);
			$('.c4d-woo-compare-cart__list_items').owlCarousel({
				items: 5,
				navigation: true,
				pagination: false,
				navigationText: ['','']
			});
			c4dWooCompare.hideList($('.c4d-woo-compare-cart__icon'));
			if (callback) {
				callback();	
			}
		});
	};

	c4dWooCompare.hideList = function(self, addCart) {
		if (addCart) {
			$('.c4d-woo-compare-cart').removeClass('empty');
		} else {
			if ($(self).parents('.c4d-woo-compare-cart' ).find('.c4d-woo-compare-cart__list_items .item').length <= 1) {
				$(self).parents('.c4d-woo-compare-cart').addClass('empty');
			} else {
				$(self).parents('.c4d-woo-compare-cart').removeClass('empty');
			}	
		}
	};

	$(document).ready(function(){
		var current = $.cookie(c4dWooCompare.cookieName),
		number = $('.c4d-woo-compare-cart__icon .number'),
		current = c4dWooCompare.current();
		number.html(current.length);
		c4dWooCompare.cart();
		$('.c4d-woo-compare-cart__icon').fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	true,
			'autoSize'		: 	true,
        	'scrolling'		: 'yes',
        	'wrapCSS' : 'c4d-woo-compare'
		});
		// add product to compare list
		$('body').on('click', '.c4d-woo-compare-button', function(event){
			event.preventDefault();
			var self = this,
			id = $(self).attr('data-id'),
			current = c4dWooCompare.current();
			
			if ($.inArray(id, current) < 0) {
				current.push(id);
				$.cookie(c4dWooCompare.cookieName, current, { expires: 30, path: '/'});	
				number.html(parseInt(number.html()) + 1);
				number.addClass('add-new');
				$(self).addClass('added');
				// c4dWooCompare.hideList(self, true);
				c4dWooCompare.cart(function(){
					number.removeClass('add-new');
				});
			}
			return false;
		});

		// remove product from compare list
		$('body').on('click', '.c4d-woo-compare-remove-item', function(event){
			event.preventDefault();
			var self = this,
			id = $(self).attr('data-id'),
			current = c4dWooCompare.current(),
			index = $.inArray(id, current);
			if (index > -1) {
				current.splice(index, 1);
				//update cookie
				$.cookie(c4dWooCompare.cookieName, current, { expires: 30, path: '/'});	
				// update number cart compare
				if (parseInt(number.html()) >= 1) {
					number.html(parseInt(number.html()) - 1);
					$('.c4d-woo-compare-cart__list_header .number').html(parseInt($('.c4d-woo-compare-cart__list_header .number').html()) - 1);	
				}
				// remove added class for button compare 
				$('.c4d-woo-compare-button[data-id="'+id+'"]').removeClass('added');
				// remove this item from list
				$(self).parents('.item').addClass('remove');
				setTimeout(function(){
					$(self).parents('.owl-item').remove();
				}, 500);	

				if (current.length < 1) {// close modal when remove latest item
					$.fancybox.close();
				}
			}
			return false;
		});
	});
})(jQuery);