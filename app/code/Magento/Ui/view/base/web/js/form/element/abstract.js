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
        validation: {},
        validateOnChange: true
    };

    return Scope.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config) {
            _.extend(this, defaults, config);

            this.setUniqueId()
                .initObservable()
                .initDisableStatus();

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
                self.provider.data.on('update:' + path, function(changedValue){
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
            this.provider.data.set(this.name, value);
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
            var isValid;

            if (this.validateOnChange) {
                isValid = this.validate();
                this.trigger('validate', isValid);
            }

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

        /**
         * Validates itself by it's validation rules using validator.
         * If validation of a rule did not pass, writes it's message to
         *     errorMessages array.
         * Triggers validate event on the instance passing the result of
         *     validation to it.
         *     
         * @return {Boolean} - true, if element is valid
         */
        validate: function () {
            var value       = this.value(),
                errors      = [],
                rules       = this.validation,
                isValid     = true,
                isAllValid  = true;

            _.each(rules, function (params, rule) {
                isValid = validator.validate(rule, value, params);

                if (!isValid) {
                    isAllValid = false;
                    errors.push(validator.messageFor(rule));
                }
            });

            this.errorMessages(errors);
            this.trigger('validate', isAllValid);

            return isAllValid;
        }
    }, EventsBus);
});