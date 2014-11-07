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
        hidden:             false,
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
         * Invokes initialize method of parent class.
         * @param {Object} config - form element configuration
         */
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initTemplate()
                .setNoticeId()
                .store(this.value())
                .setHidden(this.hidden());
        },

        /**
         * Initializes observable properties of instance
         * 
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            var value = this.getInititalValue(), 
                rules;

            __super__.initObservable.apply(this, arguments);

            rules = this.validation = this.validation || {};

            this.initialValue = value;

            this.observe([
                    'error',
                    'disabled',
                    'focused',
                    'preview',
                    'hidden'
                ])
                .observe({
                    'value':    value,
                    'required': rules['required-entry']
                });        

            return this;
        },

        /**
         * Initializes properties of instance
         * 
         * @return {Object} - reference to instance
         */
        initProperties: function () {
            __super__.initProperties.apply(this, arguments);

            this.uid        = utils.uniqueid();
            this.inputName  = utils.serializeName(this.dataScope);

            return this;
        },

        /**
         * Initializes instance's listeners
         * 
         * @return {Object} - reference to instance
         */
        initListeners: function(){
            var provider  = this.provider,
                data      = provider.data;

            __super__.initListeners.apply(this, arguments);

            data.on('reset', this.reset.bind(this));
            
            this.value.subscribe(this.onUpdate, this);

            return this;
        },

        /**
         * Overrides template property of instance
         * 
         * @return {Object} - reference to instance
         */
        initTemplate: function(){
            this.template = this.template || (this.tmpPath + this.input_type);

            return this;
        },

        /**
         * Gets initial value of element
         * 
         * @return {*} - value of element
         */
        getInititalValue: function(){
            var data = this.provider.data,
                value = data.get(this.dataScope);

            return _.isUndefined(value) || _.isNull(value) ? '' : value;
        },

        /**
         * Sets notice id for element
         * 
         * @return {Object} - reference to instance
         */
        setNoticeId: function () {
            if (this.notice) {
                this.noticeId = 'notice-' + this.uid;
            }

            return this;
        },

        /**
         * Sets value to preview observable
         * 
         * @param {Object} - reference to instance
         */
        setPreview: function(value){
            this.preview(value);

            return this;
        },

        /**
         * Returnes unwrapped preview observable
         * 
         * @return {*} - value of preview observable
         */
        getPreview: function(){
            return this.preview();
        },

        hide: function(){
            this.setHidden(true)
                .value('');
        },

        show: function(value){
            this.setHidden(false);
        },

        setHidden: function(value){
            var params = this.provider.params;

            this.hidden(value);
            this.trigger('toggle', value);

            params.set(this.name + '.hidden', value);

            return this;
        },

        /**
         * Checkes if element has addons
         * 
         * @return {Boolean}
         */
        hasAddons: function () {
            return this.addbefore || this.addafter;
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} value - current value of form element
         */
        store: function (value) {
            var data = this.provider.data;

            data.set(this.dataScope, value);

            this.setPreview(value);

            return this;
        },

        /**
         * Sets value observable to initialValue property
         */
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
            var notEqual = this.value() !== this.initialValue;

            return this.hidden() ? false : notEqual;
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

            if(this.hidden()){
                return false;
            }

            _.some(this.validation, function (params, rule) {
                errorMsg = validator.validate(rule, value, params);

                return !!errorMsg;
            }, this);

            if(errorMsg && !params.get('invalid')){
                params.set('invalid', this);
            }

            this.error(errorMsg);

            return !!errorMsg;
        }
    });
});