/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'underscore',
    'mage/utils'
], function (Scope, _, utils) {
    'use strict';

    var defaults = {
        module: 'ui',
        tooltip: null,
        label: '',
        type: 'input',
        required: false,
        value: ''
    };

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         * @param {Number|String} value - initial value of form element
         * @param {Object} refs - references to provider and globalStorage
         */
        initialize: function (config, value, refs) {
            _.extend(this, defaults, config, refs);

            this.uid = utils.uniqueid();
            this.observe('value', value);
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
            return this.module + '/form/element/' + this.type;
        }
    });
});