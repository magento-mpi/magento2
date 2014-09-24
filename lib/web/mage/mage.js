/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint eqnull:true browser:true jquery:true */
/*global require:true console:true*/
(function (factory) {
    if (typeof define === "function" && define.amd) {
        define([
            "jquery",
            "mage/apply/main"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function ($, mage) {
    "use strict";


    /**
     * Main namespace for Magento extensions
     * @type {Object}
     */
    $.mage = $.mage || {};

    /**
     * Plugin mage, initialize components on elements
     * @param {String} name - Components' path.
     * @param {Object} config - Components' config.
     * @returns {JQuery} Chainable.
     */
    $.fn.mage = function(name, config) {
        config = config || {};

        this.each(function(index, el){
            mage.applyFor(el, config, name);
        });

        return this;
    };
    
    $.extend($.mage, {
        /**
         * Handle all components declared via data attribute
         * @return {Object} $.mage
         */
        init: function() {
            mage.apply();

            return this;
        },

        /**
         * Method handling redirects and page refresh
         * @param {String} url - redirect URL
         * @param {(undefined|String)} type - 'assign', 'reload', 'replace'
         * @param {(undefined|Number)} timeout - timeout in milliseconds before processing the redirect or reload
         * @param {(undefined|Boolean)} forced - true|false used for 'reload' only
         */
        redirect: function(url, type, timeout, forced) {
            var _redirect;

            forced  = !!forced;
            timeout = timeout || 0;
            type    = type || "assign";
            
            _redirect = function() {
                window.location[type](type === 'reload' ? forced : url);
            };
            
            timeout ? setTimeout(_redirect, timeout) : _redirect();
        }
    });

    /**
     * Init components inside of dynamically updated elements
     */
    $('body').on('contentUpdated', function(e) {
        mage.apply(e.target);
    });

    return $.mage;
}));