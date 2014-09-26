/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/registry/registry'
], function (Scope, registry) {
    'use strict';

    /**
     * Generates a unique identifier.
     * @returns {String}
     * @private
     */
    function uniqueid() {
        var idstr = String.fromCharCode((Math.random() * 25 + 65) | 0),
            ascicode;

        while (idstr.length < 10) {
            ascicode = Math.floor((Math.random() * 42) + 48);

            if (ascicode < 58 || ascicode > 64) {
                idstr += String.fromCharCode(ascicode);
            }
        }

        return idstr;
    };

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         * @param {Number|String} value - initial value of form element
         */
        initialize: function (config, value, path) {
            _.extend(this, config);

            this.uid     = uniqueid();
            this.module  = this.module   || 'ui';
            this.tooltip = this.tooltip  || null;
            this.label   = this.label    || '';

            this.observe('value', this.value || '');
            this.value.subscribe(this.store.bind(this, path));
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {String} path
         * @param  {*} value - current value of form element
         */
        store: function (path, value) {
            registry.set(value, this.path);
        },

        /**
         * Returns string path for element's template
         * @return {String}
         */
        getTemplate: function () {
            return this.module + '/form/element/' + this.type;
        }
    });
});