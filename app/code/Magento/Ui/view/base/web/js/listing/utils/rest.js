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
    'Magento_Ui/js/lib/events',
    './request_builder'
], function(_, $, Class, EventsBus, requestBuilder) {
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
                .done(this.onRead.bind(this));
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

        /**
         * Is being called after ajax call. Parses results and triggers read event;
         * @param  {Object|*} result - result of ajax call
         */
        onRead: function(result){
            result = typeof result === 'string' ?
                JSON.parse(result) :
                result;

            this.trigger('read', result);
        },

        /**
         * Submits data using utils.submitAsForm
         * @param  {Object} config - object containing ajax options
         * @param  {Object} params
         */
        submit: function(config, params){
            var ajax = this.config.ajax,
                data = ajax.data || {};

            _.extend(config.data, data);

            utils.submitAsForm(config);
        }
    }, EventsBus);

});