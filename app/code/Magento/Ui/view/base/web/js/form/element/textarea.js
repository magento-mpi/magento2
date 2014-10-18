/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract'
], function (Abstract) {
    'use strict';

    var defaults = {
        cols: 15,
        rows: 2,
        template: 'ui/form/element/textarea'
    };

    return Abstract.extend({

        /**
         * Invokes initialize method of parent class.
         */
        initialize: function (config) {
            _.extend(this, defaults);
            
            Abstract.prototype.initialize.apply(this, arguments);
        }
    });
});