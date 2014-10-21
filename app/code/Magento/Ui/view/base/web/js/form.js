/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component'
], function (Component) {
    'use strict';

    var __super__ = Component.prototype;

    return Component.extend({

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('isValid', false);
        },

        /**
         * Handler for submit action. Validates form and then, if form is valid,
         *     invokes 'submit' method.
         */
        onSubmit: function () {
            var isValid = this.validate();

            this.isValid(isValid);

            if (isValid) {
                this.submit();
            }
        },

        /**
         * Submits form
         */
        submit: function () {},

        isElementValid: function (element) {
            return element.validate(true);
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         * 
         * @return {Boolean}
         */
        validate: function () {
            var isElementValid = this.isElementValid.bind(this);

            return this.elems.every(isElementValid);
        }
    });
});