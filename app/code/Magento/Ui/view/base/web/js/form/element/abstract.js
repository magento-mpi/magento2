/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/registry/registry',
    'underscore',
    'mage/utils'
], function (Scope, registry, _, utils) {
    'use strict';

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         * @param {Number|String} value - initial value of form element
         * @param {String} path - element instance's path to store value to
         */
        initialize: function (config, value, path) {
            _.extend(this, config);

            this.uid     = utils.uniqueid();
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