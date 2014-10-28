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

            return this;
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
        submit: function () {
            console.log('submit');
        },

        reset: function(){
            this.provider.data.trigger('reset');
        },

        save: function(){
            var params = this.provider.params,
                isValid;

            this.validate();

            isValid = !params.get('invalidElement');

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
            var provider = this.provider;

            provider.params.set('invalidElement', null);
            provider.data.trigger('validate');
        }
    });
});