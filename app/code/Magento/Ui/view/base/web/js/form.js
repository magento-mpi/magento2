/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'Magento_Ui/js/lib/spinner',
    './form/adapter'
], function (Component, loader, adapter) {
    'use strict';

    var __super__ = Component.prototype;

    function collectData(selector){
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
                .initSelector()
                .hideLoader();
        },

        initAdapter: function(){
            adapter.on({
                'reset':            this.reset.bind(this),
                'save':             this.save.bind(this, true),
                'saveAndContinue':  this.save.bind(this, false)
            });

            return this;
        },
        
        initSelector: function(){
            this.selector = '[data-form-part='+ this.namespace +']';

            return this;
        },

        hideLoader: function () {
            loader.get(this.name).hide();

            return this;
        },

        save: function(redirect){
            var params = this.provider.params;

            this.validate();

            if (!params.get('invalid')) {
                this.submit(redirect);
            }
        },

        /**
         * Submits form
         */
        submit: function (redirect) {
            var additional  = collectData(this.selector),
                provider    = this.provider;

            _.each(additional, function(value, name){
                provider.data.set(name, value);
            });

            provider.save({
                redirect: redirect
            });
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         */
        validate: function () {
            var provider = this.provider;

            provider.params.set('invalid', false);
            provider.data.trigger('validate');
        },

        reset: function(){
            var data = this.provider.data;

            data.trigger('reset');
        }
    });
});