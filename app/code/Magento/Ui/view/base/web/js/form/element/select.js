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
        size: false,
        template: 'ui/form/element/select'
    };

    var __super__ = Abstract.prototype;

    function hasLeafNode(nodes){
        return _.some(nodes, function(node){
            return typeof node.value === 'object'
        });
    }


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
            var value;

            this.hasLeafNode = hasLeafNode(this.options);

            if (this.hasLeafNode) {
                value = [this.value()];

                this.value(value);
                this.initialValue = value;
            }

            return this;
        },

        getCaption: function(){
            if(!this.no_caption){
                return this.caption; 
            }
        },

        formatValue: function(value){
            return Array.isArray(value) ? value[0] : value;
        },

        /**
         * Stores element's value to registry by element's path value
         * @param  {*} changedValue - current value of form element
         */
        store: function (value) {
            value = this.formatValue(value)

            this.provider.data.set(this.dataScope, value);
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