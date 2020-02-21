define([
'jquery',
'uiComponent',
'underscore',
'Magento_Customer/js/customer-data',
'jquery/jquery-storageapi'
], function ($, Component, _, customerData,ko) {
'use strict';

	return Component.extend({
		defaults: {
			cookieMessages: [],
			messages: [],
			selector: '.page.messages .message',
			listens: {
				isHidden: 'onHiddenChange'
			}
		},
		/** @inheritdoc */
		initialize: function () {
			this._super();

			this.cookieMessages = $.cookieStorage.get('mage-messages');
			this.messages = customerData.get('messages').extend({
				disposableCustomerData: 'messages'
			});

			if (!_.isEmpty(this.messages().messages)) {
				customerData.set('messages', {});
			} 

			$.cookieStorage.set('mage-messages', '');
		},

		initObservable: function () {
			this._super()
				.observe('isHidden');

			return this;
		},

		RemoveMessage: function () {
			var el = $(this.selector);
			el.toggleClass('bounceInRight bounceOutRight');
			setTimeout(function () {
				el.hide()
			}, 2000);
		},

		isVisible: function () {
			return this.isHidden(!_.isEmpty(this.messages().messages) || !_.isEmpty(this.cookieMessages));
		},

		onHiddenChange: function (isHidden) {
			var self = this;
			if (isHidden) {
				setTimeout(function () {
					self.RemoveMessage();
				}, 5000);
			}
			this.isHidden(false);
		}

	});
});