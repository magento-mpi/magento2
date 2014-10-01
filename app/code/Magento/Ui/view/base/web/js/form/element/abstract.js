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
    'jquery',
    'Magento_Ui/js/lib/events'
], function (Scope, _, utils, $, EventsBus) {
    'use strict';

    var defaults = {
        meta: {
            tooltip: null,
            label: '',
            required: false,
            module: 'ui'
        },
        type: 'input',
        classes: [],
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
            this.observe('classes', this.classes);
            this.initValue = this.value;
            this.observe('value', this.value);

            //this.classes.push('field');
            this.classes.push('field-' + this.type);
            if(this.meta.required) {
                this.classes.push('required');
            }
            this.value.subscribe(this.onUpdate, this);
        },

        /**
         * Stores element's value to registry by element's path value
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

        /**
         * Returns string path for element's control template
         * @return {String}
         */
        getElementTemplate: function () {
            return this.meta.module + '/form/element/' + this.type;
        },

        onUpdate: function(){
            this.trigger('update')
                .store();
        },

        hasChanged: function(){
            return this.value() !== this.initValue;
        }
    }, EventsBus);
});