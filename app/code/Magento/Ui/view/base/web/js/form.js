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

    function collect(selector){
        var items = document.querySelectorAll(selector),
            result = {};

        items = Array.prototype.slice.call(items);

        items.forEach(function(item){
            result[item.name] = item.value;
        });

        return result;
    }

    return Component.extend({

        initialize: function(){
            __super__.initialize.apply(this, arguments);

            this.initAdapter()
                .initSelector();
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

        initSelector: function(){
            this.selector = '[data-form-part='+ this.namespace +']';

            return this;
        },

        reset: function(){
            this.provider.data.trigger('reset');
        },

        save: function(){
            var params = this.provider.params;

            this.validate();

            if (!params.get('invalidElement')) {
                this.submit();
            }
        },

        /**
         * Submits form
         */
        submit: function () {
            var additional  = collect(this.selector),
                provider    = this.provider;

            _.each(additional, function(value, name){
                provider.data.set(name, value);
            });

            provider.save();
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