/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'mage/utils',
    './abstract',
    'i18n'
], function (_, utils, Abstract, i18n) {
    'use strict';

    var defaults = {
        caption: i18n('Select...'),
        disabled: false,
        size: 5,
        template: 'ui/form/element/multiselect'
    };

    var __super__ = Abstract.prototype;

    return Abstract.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.formatInitialValue();
        },

        formatInitialValue: function() {
            this.initialValue = this.initialValue || [];

            this.value(this.initialValue);

            return this;
        },

        getCaption: function(){
            if(!this.no_caption){
                return this.caption; 
            }
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