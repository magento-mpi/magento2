/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
define([
    'underscore',
    'mage/utils',
    './select'
], function (_, utils, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            size:       5
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