/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
define([
    './abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        /**
         * Converts the result of parent 'getInitialValue' call to boolean
         * 
         * @return {Boolean}
         */
        getInititalValue: function(){
            return !!+this._super();
        },

        /**
         * Calls 'store' method of parent, if value is defined and instance's
         *     'unique' property set to true, calls 'setUnique' method
         *     
         * @param  {*} value
         * @return {Object} - reference to instance
         */
        store: function() {
            this._super();

            if (this.hasUnique) {
                this.setUnique();
            }

            return this;
        }
    });
});