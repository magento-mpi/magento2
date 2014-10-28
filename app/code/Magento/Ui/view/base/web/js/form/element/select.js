/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './abstract_select'
], function (_, Select) {
    'use strict';

    var defaults = {
        template: 'ui/form/element/select'
    };

    var __super__ = Select.prototype;

    return Select.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function () {
            _.extend(this, defaults);
            
            __super__.initialize.apply(this, arguments);
        },

        formatInitialValue: function() {
            var value;

            __super__.formatInitialValue.apply(this, arguments);
            
            if (this.hasLeafNode) {
                value = [this.value()];

                this.value(value);
                this.initialValue = value;
            }

            return this;
        },

        formatValue: function(value){
            return Array.isArray(value) ? value[0] : value;
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} changedValue - current value of form element
         */
        store: function (value) {
            value = this.formatValue(value);

            return __super__.store.call(this, value);
        },

        /**
         * Defines if value has changed
         * @return {Boolean}
         */
        hasChanged: function () {
            var value   = this.formatValue(this.value()),
                initial = this.formatValue(this.initialValue);

            return value !== initial;
        }
    });
});