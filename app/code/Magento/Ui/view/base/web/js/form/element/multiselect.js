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
            size:       5,
            template:   'ui/form/element/multiselect'
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