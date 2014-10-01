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
], function (AbstractElement, _, t) {
    'use strict';

    var defaults = {
        meta: {
            caption: t('Select...')    
        }
    };

    return AbstractElement.extend({

        /**
         * Invokes initialize method of parent class and initializes properties of instance.
         * @param {Object} config - form element configuration
         */
        initialize: function (config) {
            _.extend(config, defaults.meta);

            this.constructor.__super__.initialize.apply(this, arguments);

            this.options = this.meta.options ? this.formatOptions() : [];

            this.setInitialValue();
        },

        /**
         * Formats options property of instance.
         * @param {Object} options - object representing options
         * @returns {Array} - Options, converted to array
         */
        formatOptions: function () {
            return _.map(this.meta.options, function (value, key) {
                return { value: key, label: value  };
            });
        },

        /**
         * Sets initial value of select by looking up through options.
         */
        setInitialValue: function () {
            var value = this.value() + '',
                options;

            if (value) {
                options = _.indexBy(this.options, 'value');
                value = options[value];
                this.value(value);

                this.initValue = value.value;
            }
            else{
                this.initValue = undefined;
            }
        },

        hasChanged: function(){
            var value = this.value();

            if(value){
                value = value.value;
            }

            return value !== this.initValue;
        }
    });
});