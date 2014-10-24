/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    './form/adapter'
], function (Component, adapter) {
    'use strict';

    var __super__ = Component.prototype;

    return Component.extend({

        initialize: function(){
            __super__.initialize.apply(this, arguments);

            this.initAdapter();
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('isValid', false);
        },

        initAdapter: function(){
            _.bindAll(this, 'reset', 'save');

            adapter.on({
                'reset':            this.reset,
                'save':             this.save,
                'saveAndContinue':  this.save
            });

            return this;
        },


        /**
         * Submits form
         */
        submit: function () {},

        reset: function(){
            this.provider.data.trigger('reset');
        },

        save: function(){
            var isValid = this.validate();

            this.isValid(isValid);

            if (isValid) {
                this.submit();
            }
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         * 
         * @return {Boolean}
         */
        validate: function () {
            var isInvalidShown = false,
                isElementValid = true,
                isFormValid    = true;

            this.elems.each(function (element) {
                if (!isInvalidShown) {
                    isElementValid = element.validate(true);
                    if (!isElementValid) {
                        isInvalidShown = true;      
                    }
                } else {
                    isElementValid = element.validate();
                }

                if (isFormValid && !isElementValid) {
                    isFormValid = false;
                }
            });

            return isFormValid;
        }
    });
});