/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
define([
    'underscore',
    'mage/utils',
    './select'
], function (_, utils, Select) {
    'use strict';

    var defaults = {
        size: 5
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

        getInititalValue: function(){
            var value = __super__.getInititalValue.apply(this, arguments);

            return _.isString(value) ? value.split(',') : value;
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