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
        tmpPath:            'ui/form/element/',
        input_type:         'input',
        placeholder:        null,
        noticeid:           null,
        description:        '',
        label:              '',
        error:              '',
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

            this.initTemplate()
                .initListeners()
                .initDisableStatus()
                .setUniqueId()
                .setNoticeId();
        },

        /**
         * Initializes observable properties of instance
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            var rules,
                data = this.provider.data;

            __super__.initObservable.apply(this, arguments);

            rules = this.validation = this.validation || {};

            this.observe({
                'value':         this.initialValue = data.get(this.dataScope),
                'required':      rules['required-entry'],
                'disabled':      this.disabled,
                'error':         this.error,
                'focused':       false
            });            

            return this;
        },

        initTemplate: function(){
            this.template =  this.template || (this.tmpPath + this.input_type);

            return this;
        },

        initListeners: function(){
            var data = this.provider.data;

            data.on('reset', this.reset.bind(this));

            this.value.subscribe(this.onUpdate, this);

            return this;
        },

        initDisableStatus: function() {
            var self = this;

            _.each(this.disable_rules, function(triggeredValue, path){
                self.provider.data.on('update:' + path, function(changedValue){
                    self.disabled(triggeredValue === changedValue);
                });
            });

            return this;
        },

        setDataScope: function (dataScope) {
            this.store(undefined);

            this.dataScope = dataScope;

            this.pull();
        },

        pull: function () {
            var value = this.provider.data.get(this.dataScope);

            this.initialValue = value;
            this.value(value);
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
                this.noticeId = 'notice-' + this.uid;
            }

            return this;
        },

        hasAddons: function () {
            return this.addbefore || this.addafter;
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} value - current value of form element
         */
        store: function (value) {
            this.provider.data.set(this.dataScope, value);

            return this;
        },

        reset: function(){
            this.value(this.initialValue);
        },

        /**
         * Is being called when value is updated
         */
        onUpdate: function (value) {            
            this.store(value)
                .trigger('update')
                .validate();
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
                params      = this.provider.params,
                isValid     = true;

             _.every(rules, function (params, rule) {
                isValid = validator.validate(rule, value, params);

                if (!isValid) {
                    this.error(validator.messageFor(rule));
                }

                return isValid;
            }, this);

            if(!isValid){

                if(!params.get('invalidElement')){
                    params.set('invalidElement', this);
                }
            }
            else{
                this.error('');
            }

            return isValid;
        }
    });
});