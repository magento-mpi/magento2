/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './abstract',
    'i18n'
], function (_, Abstract, i18n) {
    'use strict';

    var defaults = {
        caption: i18n('Select...'),
        multiple: false,
        disabled: false,
        size: 10,
        template: 'ui/form/element/select'
    };

    var __super__ = Abstract.prototype;

    function parseValue(value, type){
        return type === 'multiple_select' ?
            (!Array.isArray(value) ? [value] : value) :
            (Array.isArray(value) ? value[0] : value);
    }

    return Abstract.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.extendConfig();
        },

        extendConfig: function(){
            if (this.type === 'multiple_select') {
                this.multiple = true;
                this.caption  = false;
            } else {
                this.size = false;
            }

            this.initialValue = JSON.stringify(this.value());

            return this;
        },

        getCaption: function(){
            if(!this.no_caption){
                return this.caption; 
            }
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} changedValue - current value of form element
         */
        store: function (changedValue) {
            var storedValue = parseValue(changedValue, this.type);

            this.provider.data.set(this.dataScope, storedValue);
        },

        /**
         * Defines if value has changed
         * @return {Boolean}
         */
        hasChanged: function () {
            var storedValue = parseValue(this.value(), this.type);

            return JSON.stringify(storedValue) !== this.initialValue;
        }
    });
});