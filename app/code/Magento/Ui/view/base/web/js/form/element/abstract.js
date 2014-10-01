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
    'Magento_Ui/js/lib/events'
], function (Scope, _, utils, EventsBus) {
    'use strict';

    var defaults = {
        tooltip:        null,
        label:          '',
        required:       false,
        module:         'ui',
        type:           'input',
        value:          '',
        description:    ''
    };

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config) {
            _.extend(this, defaults, config);
        
            this.setUniqueId()
                .initObservable();

            this.value.subscribe(this.onUpdate, this);
        },

        /**
         * Initializes observable properties of instance
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this.observe({
                'value': this.initialValue = this.value,
                'required': this.required
            });

            return this;
        },

        /**
         * Sets unique id for element
         * @return {Object} - reference to instance
         */
        setUniqueId: function () {
            this.uid = utils.uniqueid();

            return this;
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} value - current value of form element
         */
        store: function (value) {
            this.refs.provider.data.set(this.name, value);
        },

        /**
         * Returns string path for element's template
         * @return {String}
         */
        getTemplate: function () {
            return this.module + '/form/element/' + this.type;
        },

        /**
         * Is being called when value is updated
         */
        onUpdate: function(){
            this.trigger('update')
                .store();
        },

        /**
         * Defines if value has changed
         */
        hasChanged: function(){
            return this.value() !== this.initialValue;
        }
    }, EventsBus);
});