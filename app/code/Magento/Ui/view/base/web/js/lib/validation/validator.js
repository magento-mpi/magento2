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
            var isValid   = true,
                rule      = rules[rule],
                message   = true,
                validator;

            if (rule) {
                validator = rule[0];
                isValid   = validator(value, params);
                message   = rule[1];
            }

            return !isValid ? message : '';
        },

        /**
         * Returns error message assigned to the rule
         * @param  {String} rule - name of the rule
         * @return {String} - error message
         */
        messageFor: function (rule) {
            var rule    = rules[rule],
                message = '';

            if (rule) {
                message = rule[1];
            }

            return message;
        },

        /**
         * Adds new validation rule.
         * 
         * @param {String} rule - rule name
         * @param {Function} validator - validation function
         * @param {String} message - validation message
         */
        addRule: function (rule, validator, message) {
            rules[rule] = [validator, message];
        }
    };
});