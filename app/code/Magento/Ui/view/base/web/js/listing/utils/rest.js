/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'jquery',
    'Magento_Ui/js/lib/class',
    './request_builder'
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

        /**
         * Sends ajax request using params and config passed to it and calls this.config.onRead when done.
         * @param {Object} params - request body params
         * @param {Object} config - config to build url from
         */
        read: function(params, config) {
            config = this.createConfig(params, config);

            $.ajax(config)
                .done(this.config.onRead);
        },

        /**
         * Creates config for ajax call.
         * @param {Object} params - request body params
         * @param {Object} config - config to build url from
         * @returns {Object} - merged config for ajax call
         */
        createConfig: function(params, config) {
            var baseConf;

            config = config || {};
            params = params || {};

            baseConf = {
                url: requestBuilder(this.config.root, params),
                data: params
            };

            return $.extend(true, baseConf, this.config.ajax, config);
        },

        submit: function(config, params){
            var ajax = this.config.ajax,
                data = ajax.data || {},
                form,
                field;

            data = _.extend({}, data, params);

            form = document.createElement('form');

            $(form).attr({
                method: config.method,
                action: config.action
            });

            _.each(data, function(value, name){
                field = document.createElement('input');

                if(typeof value === 'object'){
                    value = JSON.stringify(value);
                }

                $(field).attr({
                    name: name,
                    type: 'hidden',
                    value: value
                });

                form.appendChild(field);
            });

            document.body.appendChild(form);

            form.submit();
        }
    });

});