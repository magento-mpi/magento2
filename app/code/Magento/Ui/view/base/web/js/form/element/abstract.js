/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'mage/utils',
    'Magento_Ui/js/form/component',
    'Magento_Ui/js/lib/validation/validator'
], function (_, utils, Component, validator) {
    'use strict';

    var defaults = {
        tooltip:            null,
        required:           false,
        disabled:           false,
        module:             'ui',
        type:               'input',
        placeholder:        null,
        noticeid:           null,
        description:        '',
        label:              '',
        error:              '',
        addbefore:          null,
        addafter:           null,
        notice:             null
    };

    var __super__ = Component.prototype;

    return Component.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.setUniqueId()
                .initDisableStatus();

            this.value.subscribe(this.onUpdate, this);
        },

        /**
         * Initializes observable properties of instance
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            var rules;

            __super__.initObservable.apply(this, arguments);

            rules = this.validation = this.validation || {};

            this.observe({
                'value':         this.initialValue = this.provider.data.get(this.name),
                'required':      rules['required-entry'],
                'disabled':      this.disabled,
                'error':         this.error,
                'focused':       false
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
         * Sets notice id for element
         * @return {Object} - reference to instance
         */
        setNoticeId: function () {
            if (this.notice) {
                this.noticeid = 'notice-' + this.uid;
            }

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

        hasAddons: function () {
            return (this.addbefore !== null) || (this.addafter !== null);
        },

        getNoticeId: function () {
            return 'notice-' + this.uid;
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
            return this.template || (this.module + '/form/element/' + this.input_type);
        },

        /**
         * Is being called when value is updated
         */
        onUpdate: function (value) {
            var isValid = this.validate();

            this.trigger('update', this, {
                value:          value,
                isValid:        isValid,
                makeVisible:    false
            });

            this.store(value);
        },

        /**
         * Defines if value has changed
         * @return {Boolean}
         */
        hasChanged: function(){
            return this.value() !== this.initialValue;
        },

        /**
         * Validates itself by it's validation rules using validator object.
         * If validation of a rule did not pass, writes it's message to
         *     errors array.
         *     
         * @return {Boolean} - true, if element is valid
         */
        validate: function (showErrors) {
            var value       = this.value(),
                rules       = this.validation,
                isValid     = true,
                isAllValid  = true;

            isAllValid = _.every(rules, function (params, rule) {
                isValid = validator.validate(rule, value, params);

                if (!isValid) {
                    this.error(validator.messageFor(rule));
                }

                return isValid;
            }, this);

            if (isAllValid) {
                this.error('');
            } else if (showErrors) {
                this.trigger('update', this, {
                    value:          value,
                    isValid:        isAllValid,
                    makeVisible:    true
                });

                this.focused(true);
            }

            return isAllValid;
        }
    });
});