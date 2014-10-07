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

        /**
         * Validates value by rule and it's params.
         * @param  {String} rule - name of the rule
         * @param  {*} value
         * @param  {*} params - rule configuration
         * @return {Boolean} - true, if value is valid, false otherwise
         */
        validate: function (rule, value, params) {
            var rule      = rules[rule],
                validator = rule[0];

            return validator(value, params);
        },

        /**
         * Returns error message assigned to the rule
         * @param  {String} rule - name of the rule
         * @return {String} - error message
         */
        messageFor: function (rule) {
            var rule = rules[rule];

            return rule[1];
        }
    };
});