/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
   './rules'
], function (rules) {
    'use strict';    

    return {
        validate: function (rule, value, params) {
            var rule      = rules[rule],
                validator = rule[0];

            return validator(value, params);
        },

        messageFor: function (rule) {
            var rule = rules[rule]

            return rule[1];
        }
    };
});