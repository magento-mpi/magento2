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
        preview:            '',
        focused:            false,
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

            this.initName()
                .initTemplate()
                .initDisableStatus()
                .setUniqueId()
                .setNoticeId()
                .store(this.value());
        },

        /**
         * Initializes observable properties of instance
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            var value = this.getInititalValue(), 
                rules;

            __super__.initObservable.apply(this, arguments);

            rules = this.validation = this.validation || {};

            this.initialValue = value;

            this.observe('error disabled focused preview')
                .observe({
                    'value':    value,
                    'required': rules['required-entry']
                });        

            return this;
        },

        initListeners: function(){
            var data = this.provider.data;

            data.on('reset', this.reset.bind(this));

            this.value.subscribe(this.onUpdate, this);

            return this;
        },
        
        initName: function(){
            this.name = utils.serializeName(this.dataScope);
            
            return this;
        },

        initTemplate: function(){
            this.template =  this.template || (this.tmpPath + this.input_type);

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

        getInititalValue: function(){
            var data = this.provider.data,
                value;

            if(_.has(this, 'value')){
                value = this.value;
            }
            else if(_.has(this, 'default') && this.default != null){
                value = this.default;
            }
            else{
                value = data.get(this.dataScope);
            }

            return value;
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

        setPreview: function(value){
            this.preview(value);

            return this;
        },

        getPreview: function(){
            return this.preview();
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

            this.setPreview(value);

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
        validate: function () {
            var value       = this.value(),
                params      = this.provider.params,
                errorMsg    = '';

            _.some(this.validation, function (params, rule) {
                errorMsg = validator.validate(rule, value, params);

                return !!errorMsg;
            }, this);

            if(errorMsg && !params.get('invalidElement')){
                params.set('invalidElement', this);
            }

            this.error(errorMsg);

            return !!errorMsg;
        }
    });
});