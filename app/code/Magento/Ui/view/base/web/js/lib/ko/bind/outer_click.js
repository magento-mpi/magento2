/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'ko',
    'jquery'
], function (ko, $) {

    ko.bindingHandlers.outerClick = {

        /**
         * Attaches click handler to document
         * @param {HTMLElement} el - Element, that binding is applied to
         * @param {Function} valueAccessor - Function that returns value, passed to binding
         * @param  {[type]} allBindings - all bindings object
         * @param  {[type]} viewModel - reference to viewmodel
         */
        init: function (element, valueAccessor, allBindings, viewModel) {
            var callback = valueAccessor();
            $(document).on('click', callback.bind(viewModel));
        }
    }

});