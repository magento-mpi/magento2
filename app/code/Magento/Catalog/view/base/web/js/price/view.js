/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Catalog/js/price/component',
    'Magento_Ui/js/lib/class',
    'underscore',
    'text!Magento_Catalog/templates/price_view.html',
    'handlebars'
],function (Component, Class, _, template, Handlebars) {
    'use strict';
    
    Handlebars = Handlebars || window.Handlebars;

    var PriceView = Class.extend({
        initialize: function (config) {
            _.extend(this, defaults, config);

            this.initListeners()
                .process();
        },

        render: Handlebars.compile(template),

        initListeners: function () {
            var provider    = this.provider,
                data        = provider.data,
                config      = provider.config,
                process     = this.process.bind(this),
                handlers    = { update: process };

            config
                .on(handlers);
            data
                .on(handlers);

            return this;
        },

        process: function () {
            var data = this.pull();

            this.render(data);

            return this;
        },

        pull: function () {
            var assembled = {},
                provider  = this.provider,
                config    = provider.config.get(),
                data      = provider.data.get();

            _.extend(assembled, config, data);

            return assembled;
        }
    });

    return Component(PriceView);
});