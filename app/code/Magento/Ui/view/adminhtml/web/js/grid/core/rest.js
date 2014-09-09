define([
    '_',
    'jquery',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/request_builder'
], function(_, $, Class, requestBuilder) {
    'use strict';

    var defaults = {
        ajax: {
            dataType: 'json'
        }
    };

    return Class.extend({
        initialize: function(config) {
            $.extend(true, this.config = {}, defaults, config);
        },

        read: function(params, config) {
            config = this.getConfig(params, config);

            $.ajax(config)
                .done(this.config.onRead);
        },

        getConfig: function(params, config) {
            var baseConf;

            config = config || {};
            params = params || {};

            baseConf = {
                url: requestBuilder(this.config.root, params),
                data: params
            };

            return $.extend(true, baseConf, this.config.ajax, config);
        }
    });

});