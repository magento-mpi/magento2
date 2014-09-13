/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    '_'
], function (AbstractControl, _) {
    'use strict';
    
    return AbstractControl.extend({

        /**
         * Invokes initialize method of parent class and initializes observable properties of instance.
         * @param {Object} data - Item of "fields" array from grid configuration
         * @param {Object} config - Filter configuration
         */
        initialize: function (data, config) {
            this.constructor.__super__.initialize.apply(this, arguments);

            this.observe({
                from: '',
                to:   ''
            });
        },
        
        getValues: function(){
            var value = {},
                from = this.from(),
                to = this.to();

            if (from) {
                value.from = from;
            }

            if (to) {
                value.to = to;
            }

            return value;
        },

        display: function(){
            var key,
                values = this.getValues(),
                result = [];

            for(key in values){
                result.push(key + ': ' + values[key]);
            }

            return result.join(' ');
        },

        isEmpty: function(){
            return ( !this.to() && !this.from() );
        },

        /**
         * Returns dump of instance's current state
         * @returns {Object} - object which represents current state of instance
         */
        dump: function () {
            this.output( this.display() );

            return {
                field: this.index,
                value: this.getValues()
            };
        },

        /**
         * Resets state properties of instance and calls dump method.
         * @returns {Object} - object which represents current state of instance
         */
        reset: function () {
            this.to('');
            this.from('');
        }
    });
});