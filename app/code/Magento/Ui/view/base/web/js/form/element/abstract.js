/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'underscore',
    'mage/utils',
    'jquery'
], function (Scope, _, utils, $) {
    'use strict';

    var defaults = {
        meta: {
            tooltip: null,
            label: '',
            required: false,
            module: 'ui'
        },
        type: 'input',
        value: ''
    };

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         * @param {Object} refs - references to provider and globalStorage
         */
        initialize: function (config, refs) {
            $.extend(true, this, defaults, config, refs);

            this.uid = utils.uniqueid();
            this.observe('value', this.value);
            this.value.subscribe(this.store, this);
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {String} path
         * @param  {*} value - current value of form element
         */
        store: function (value) {
            this.provider.data.set(this.name, value);
        },

        /**
         * Returns string path for element's template
         * @return {String}
         */
        getTemplate: function () {
            return this.meta.module + '/form/element';
        },

        getElementTemplate: function () {
            return this.meta.module + '/form/element/' + this.type;
        }
    });
});