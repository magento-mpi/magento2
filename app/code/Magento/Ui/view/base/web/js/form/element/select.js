/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    'underscore',
    'i18n'
], function (AbstractElement, _, i18n) {
    'use strict';

    var defaults = {
        caption: i18n('Select...'),
        multiple: false,
        disabled: false,
        size: 10,
        template: 'ui/form/element/select'
    };

    return AbstractElement.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function () {
            AbstractElement.prototype.initialize.apply(this, arguments);

            _.extend(this, defaults);

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

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} changedValue - current value of form element
         */
        store: function (changedValue) {
            var storedValue;
            if (this.type === 'multiple_select') {
                storedValue = !Array.isArray(changedValue) ? [changedValue] : changedValue;
            } else {
                storedValue = Array.isArray(changedValue) ? changedValue[0] : changedValue;
            }

            this.provider.data.set(this.name, storedValue);
        },

        /**
         * Defines if value has changed
         * @return {Boolean}
         */
        hasChanged: function () {
            return JSON.stringify(this.value()) !== this.initialValue;
        }
    });
});