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
            config = this.getConfig(params, config);

            $.ajax(config)
                .done(this.config.onRead);
        },

        /**
         * Creates config for ajax call.
         * @param {Object} params - request body params
         * @param {Object} config - config to build url from
         * @returns {Object} - merged config for ajax call
         */
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