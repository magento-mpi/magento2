/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'mage/utils',
    './abstract_select'
], function (_, utils, Select) {
    'use strict';

    var defaults = {
        size: 5,
        template: 'ui/form/element/multiselect'
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
            this.initialValue = this.initialValue || [];

            this.value(this.initialValue);

            return this;
        },

        /**
         * Defines if value has changed
         * @return {Boolean}
         */
        hasChanged: function () {
            var value   = this.value(),
                initial = this.initialValue;

            return !utils.identical(value, initial);
        }
    });
});