/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
	'ko',
	'jquery'
], function(ko, $) {
	'use strict';

	ko.bindingHandlers.enter = {

		/**
         * Attaches enter handler to element
         * @param {HTMLElement} el - Element, that binding is applied to
         * @param {Function} valueAccessor - Function that returns value, passed to binding
         * @param  {Object} allBindings - all bindings object
         * @param  {Object} viewModel - reference to viewmodel
         */
		init: function(element, valueAccessor, allBindings, viewModel) {
			var callback = valueAccessor();

			ko.utils.registerEventHandler(element, 'keypress', function(event) {
				if (event.keyCode === 13) {
					callback.call(viewModel);
				}
			});
		}
	};
});