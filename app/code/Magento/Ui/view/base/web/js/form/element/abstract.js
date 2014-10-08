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
    'Magento_Ui/js/lib/events',
    'Magento_Ui/js/lib/validation/validator'
], function (Scope, _, utils, EventsBus, validator) {
    'use strict';

    var defaults = {
        tooltip:        null,
        label:          '',
        required:       false,
        module:         'ui',
        type:           'input',
        value:          '',
        description:    '',
        disabled:       false,
        validation: {}
    };

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config) {
            _.extend(this, defaults, config);

            this.setUniqueId()
                .initDisableStatus()
                .initObservable();

            this.value.subscribe(this.onUpdate, this);

        },

        /**
         * Initializes observable properties of instance
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this.observe({
                'value':         this.initialValue = this.value,
                'required':      this.required,
                'disabled':      this.disabled,
                'errorMessages': []
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

        initDisableStatus: function() {
            var self = this;

            _.each(this.disable_rules, function(triggeredValue, path){
                self.refs.provider.data.on('update:' + path, function(changedValue){
                    self.disabled(triggeredValue === changedValue);
                });
            });

            return self;
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
        onUpdate: function(value){
            this.trigger('update', this.name, value)
                .store(value);
        },

        /**
         * Defines if value has changed
         * @return {Boolean}
         */
        hasChanged: function(){
            return this.value() !== this.initialValue;
        },

        isValid: function () {
            return this.validate();
        },

        validate: function () {
            var value    = this.value(),
                messages = [],
                failed   = [],
                rules    = this.validation,
                isValid;

            _.each(rules, function (params, rule) {
                isValid = validator.validate(rule, value, params);

                if (!isValid) {
                    failed.push(rule);
                    messages.push(validator.messageFor(rule));
                }
            });

            this.errorMessages(messages);

            return !failed.length;
        }
    }, EventsBus);
});